<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'receiver_id' => 'nullable|exists:users,id'
        ]);
        
        $user = Auth::user();
        
        // Tentukan receiver
        if ($user->is_admin) {
            // Admin mengirim ke user tertentu
            $receiverId = $request->receiver_id;
        } else {
            // User mengirim ke admin
            $receiverId = User::where('is_admin', 1)->first()->id ?? null;
        }
        
        if (!$receiverId) {
            return response()->json(['error' => 'Receiver not found'], 404);
        }
        
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'is_read' => false
        ]);
        
        $message->load(['sender', 'receiver']);
        
        // Broadcast event
        broadcast(new MessageSent($message))->toOthers();
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    public function markAsRead($userId)
    {
        // Mark all messages from this user as read
        $updated = Message::where('sender_id', $userId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json([
            'success' => true,
            'updated' => $updated
        ]);
    }
    
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }
}
