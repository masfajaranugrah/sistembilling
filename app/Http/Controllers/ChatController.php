<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Get authenticated user from multiple guards
     */
    private function getAuthUser()
    {
        // Cek guard web dulu (untuk admin)
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }
        
        // Cek guard customer
        if (Auth::guard('customer')->check()) {
            return Auth::guard('customer')->user();
        }
        
        return null;
    }
    
    public function admin()
    {
        $user = $this->getAuthUser();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Pastikan yang akses adalah admin
        if (!in_array($user->role, ['administrator', 'admin'])) {
            return redirect()->route('chat.user')->with('error', 'Akses ditolak');
        }
        
        // Ambil semua user/pelanggan yang pernah chat dengan siapa saja (termasuk dengan admin)
        $senderIds = Message::distinct()->pluck('sender_id');
        $receiverIds = Message::distinct()->pluck('receiver_id');
        
        // Gabungkan dan hilangkan duplikat
        $userIdsFromMessages = $senderIds->merge($receiverIds)
            ->unique()
            ->filter(function($id) use ($user) {
                // Exclude admin sendiri
                return $id !== $user->id;
            })
            ->values();
        
        // Ambil users yang pernah chat (exclude admin)
        $users = User::whereIn('id', $userIdsFromMessages)
            ->whereNotIn('role', ['administrator', 'admin'])
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'type' => 'user',
                    'created_at' => $user->created_at,
                ];
            });
            
        // Ambil pelanggans yang pernah chat
        $pelanggans = Pelanggan::whereIn('id', $userIdsFromMessages)
            ->get()
            ->map(function($pelanggan) {
                return [
                    'id' => $pelanggan->id,
                    'name' => $pelanggan->nama_lengkap ?? 'Pelanggan',
                    'type' => 'pelanggan',
                    'created_at' => $pelanggan->created_at,
                ];
            });
        
        // Gabungkan dan sort by last message time
        $contacts = $users->concat($pelanggans);
        
        // Sort by last message timestamp (pesan terbaru muncul paling atas)
        $contacts = $contacts->map(function($contact) {
            // Ambil pesan terakhir dari/ke user ini (tidak harus dengan admin yang login)
            $lastMessage = Message::where(function($query) use ($contact) {
                $query->where('sender_id', $contact['id'])
                      ->orWhere('receiver_id', $contact['id']);
            })
            ->orderBy('created_at', 'desc')
            ->first();
            
            $contact['last_message_at'] = $lastMessage ? $lastMessage->created_at : $contact['created_at'];
            return $contact;
        })
        ->sortByDesc('last_message_at')
        ->values();
       
        return view('content.apps.chat.admin.chat', ['users' => $contacts]);
    }

    public function user()
    {
        $user = $this->getAuthUser();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Pastikan yang akses bukan admin
        if (in_array($user->role, ['administrator', 'admin'])) {
            return redirect()->route('chat.admin')->with('error', 'Silakan gunakan chat admin');
        }
        
        return view('content.apps.chat.user.chat');
    }
    
    public function getMessages($userId = null)
    {
        $user = $this->getAuthUser();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        // Cek apakah user adalah admin
        $isAdmin = isset($user->role) && in_array($user->role, ['administrator', 'admin']);
        
        if ($isAdmin && $userId) {
            // Admin melihat chat dengan user/pelanggan tertentu
            $messages = Message::where(function($query) use ($userId, $user) {
                // Pesan dari user/pelanggan ke admin
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $user->id);
            })
            ->orWhere(function($query) use ($userId, $user) {
                // Pesan dari admin ke user/pelanggan
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();
        } else {
            // Pelanggan/User melihat chat dengan admin
            $adminId = User::whereIn('role', ['administrator', 'admin'])->first()->id ?? null;
            
            if (!$adminId) {
                return response()->json(['error' => 'Admin not found'], 404);
            }
            
            $messages = Message::where(function($query) use ($user, $adminId) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $adminId);
            })
            ->orWhere(function($query) use ($user, $adminId) {
                $query->where('sender_id', $adminId)
                      ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();
        }
        
        return response()->json($messages);
    }
    
    public function getUserList()
    {
        // Untuk admin mendapatkan list semua kontak (users + pelanggans)
        $users = User::whereNotIn('role', ['administrator', 'admin'])
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'type' => 'user',
                ];
            });
            
        $pelanggans = Pelanggan::all()->map(function($pelanggan) {
            return [
                'id' => $pelanggan->id,
                'name' => $pelanggan->nama_lengkap ?? 'Pelanggan',
                'type' => 'pelanggan',
            ];
        });
        
        $contacts = $users->concat($pelanggans)->values();
            
        return response()->json($contacts);
    }

    /**
     * Send a new message
     */
    public function send(Request $request)
    {
        $user = $this->getAuthUser();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        // Validasi berbeda untuk admin dan user
        $isAdmin = in_array($user->role, ['administrator', 'admin']);
        
        if ($isAdmin) {
            $request->validate([
                'message' => 'required|string|max:5000',
                'receiver_id' => 'required|string', // Accept any UUID string
            ]);
            $receiverId = $request->receiver_id;
            
            // Validasi receiver_id ada di users atau pelanggans
            $receiverExists = User::find($receiverId) || Pelanggan::find($receiverId);
            if (!$receiverExists) {
                return response()->json(['error' => 'Receiver not found'], 404);
            }
        } else {
            $request->validate([
                'message' => 'required|string|max:5000',
            ]);
            // User/Pelanggan mengirim ke admin
            $admin = User::whereIn('role', ['administrator', 'admin'])->first();
            
            if (!$admin) {
                return response()->json(['error' => 'Admin not found'], 404);
            }
            
            $receiverId = $admin->id;
        }

        // Simpan message
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Load sender info dengan accessor untuk broadcast
        $message->sender; // Trigger accessor
        
        // Broadcast event ke receiver channel
        broadcast(new MessageSent($message));

        return response()->json([
            'success' => true,
            'message' => $message->fresh(), // Reload dengan accessor
        ], 201);
    }

    /**
     * Mark messages as read
     */
    public function markRead($userId)
    {
        $user = $this->getAuthUser();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        if (in_array($user->role, ['administrator', 'admin'])) {
            // Admin mark pesan dari user sebagai sudah dibaca
            Message::where('sender_id', $userId)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            // User mark pesan dari admin sebagai sudah dibaca
            $adminId = User::whereIn('role', ['administrator', 'admin'])->first()->id ?? null;
            
            Message::where('sender_id', $adminId)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get unread message count
     */
    public function getUnreadCount()
    {
        $user = $this->getAuthUser();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        if (in_array($user->role, ['administrator', 'admin'])) {
            // Admin: hitung unread per user
            $unreadCounts = Message::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->selectRaw('sender_id, COUNT(*) as count')
                ->groupBy('sender_id')
                ->get()
                ->pluck('count', 'sender_id');
                
            return response()->json($unreadCounts);
        } else {
            // User: total unread dari admin
            $adminId = User::whereIn('role', ['administrator', 'admin'])->first()->id ?? null;
            
            $count = Message::where('sender_id', $adminId)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();
                
            return response()->json(['count' => $count]);
        }
    }
}
