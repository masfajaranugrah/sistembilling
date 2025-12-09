<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Ticket; // ← jangan lupa ini
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobsController extends Controller
{
    /**
     * Display all tickets assigned to the logged-in teknisi (excluding status approve)
     */
    public function index()
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Ambil semua tagihan beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();
        $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
        $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
        // Statistik
        $totalCustomer = $pelanggan->count(); // jumlah pelanggan
        $lunas = $tagihans->where('status_pembayaran', 'lunas')->count(); // jumlah tagihan lunas
        $belumLunas = $tagihans->where('status_pembayaran', 'belum bayar')->count(); // jumlah tagihan belum lunas
        $totalPaket = $paket->count(); // jumlah paket

        $user = auth()->user();

        $tickets = Ticket::with(['user', 'creator'])
            ->where('user_id', $user->id)
            ->where('status', '!=', 'Approved') // status approve dihilangkan
            ->latest()
            ->get()
            ->groupBy(fn ($item) => strtolower($item->priority));

        return view('content.apps.teknisi.jobs.jobs', compact('tickets', 'tagihans',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList'));
    }

    /**
     * Display tickets with status "approve"
     */
    public function approved()
    {
        $user = auth()->user();

        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Ambil semua tagihan beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();
        $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
        $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
        // Statistik
        $totalCustomer = $pelanggan->count(); // jumlah pelanggan
        $lunas = $tagihans->where('status_pembayaran', 'lunas')->count(); // jumlah tagihan lunas
        $belumLunas = $tagihans->where('status_pembayaran', 'belum bayar')->count(); // jumlah tagihan belum lunas
        $totalPaket = $paket->count(); // jumlah paket

        $tickets = Ticket::with(['user', 'creator'])
            ->where('user_id', $user->id)
            ->where('status', 'Approved') // hanya tiket approve
            ->latest()
            ->get()
            ->groupBy(fn ($item) => strtolower($item->priority));

        return view('content.apps.teknisi.jobs.approved-jobs', compact('tickets', 'tagihans',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function edit(Ticket $ticket)
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Ambil semua tagihan beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();
        $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
        $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
        // Statistik
        $totalCustomer = $pelanggan->count(); // jumlah pelanggan
        $lunas = $tagihans->where('status_pembayaran', 'lunas')->count(); // jumlah tagihan lunas
        $belumLunas = $tagihans->where('status_pembayaran', 'belum bayar')->count(); // jumlah tagihan belum lunas
        $totalPaket = $paket->count(); // jumlah paket

        $users = User::all(); // kalau mau assign teknisi

        return view('content.apps.teknisi.jobs.edit-jobs', compact('ticket', 'users', 'tagihans',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Auto update status ticket (untuk tombol mulai & selesai)
     */
    public function autoUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,progress,finished',
        ]);

        $ticket = Ticket::findOrFail($id);
        $oldStatus = $ticket->status;

        // Update status
        $ticket->update([
            'status' => $request->status,
        ]);

        // Simpan log status
        \App\Models\TicketStatusLog::create([
            'ticket_id' => $ticket->id,
            'status' => $request->status,
            'user_id' => auth()->id(),
        ]);

        // Tentukan pesan sesuai status
        $message = match ($request->status) {
            'progress' => 'Ticket telah dimulai pengerjaannya.',
            'finished' => 'Ticket telah diselesaikan.',
            default => 'Status ticket diperbarui.',
        };

        return redirect()->back()->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Ambil semua tagihan beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();
        $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
        $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
        // Statistik
        $totalCustomer = $pelanggan->count(); // jumlah pelanggan
        $lunas = $tagihans->where('status_pembayaran', 'lunas')->count(); // jumlah tagihan lunas
        $belumLunas = $tagihans->where('status_pembayaran', 'belum bayar')->count(); // jumlah tagihan belum lunas
        $totalPaket = $paket->count(); // jumlah paket

        $ticket = Ticket::with(['user', 'creator'])->findOrFail($id); // ambil ticket sesuai ID
        $users = User::all(); // kalau mau assign teknisi

        return view('content.apps.teknisi.jobs.preview-jobs', compact('ticket', 'users', 'tagihans',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:pending,progress,finished',
            'technician_note' => 'nullable|string',
            'technician_attachment' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'status' => $request->status,
            'technician_note' => $request->technician_note,
        ];

        if ($request->hasFile('technician_attachment')) {
            // Hapus file lama jika ada
            if ($ticket->technician_attachment) {
                Storage::disk('public')->delete($ticket->technician_attachment);
            }
            // Simpan file baru
            $data['technician_attachment'] = $request->file('technician_attachment')->store('tickets/technician', 'public');
        }

        // Update ticket
        $ticket->update($data);

        // Simpan log status
        \App\Models\TicketStatusLog::create([
            'ticket_id' => $ticket->id,
            'status' => $request->status,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('jobs.index')->with('success', 'Progress ticket berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
