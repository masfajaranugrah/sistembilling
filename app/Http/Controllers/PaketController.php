<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    /**
     * Menampilkan daftar semua paket.
     */
    public function index()
    {
        $pakets = Paket::latest()->get();

        return view('content.apps.Paket.paket', compact('pakets'));
    }

    /**
     * Menampilkan form tambah paket baru.
     */
    public function create()
    {
        return view('content.apps.Paket.add-paket');
    }

    /**
     * Menyimpan data paket baru ke database.
     */
    public function store(Request $request)
    {
        $harga = $request->harga;

        // Hapus Rp, spasi biasa dan non-breaking space, serta titik ribuan
        $harga = preg_replace('/[^\d]/', '', $harga); // hanya menyisakan angka

        Paket::create([
            'nama_paket' => $request->nama_paket,
            'harga' => $harga,
            'masa_pembayaran' => $request->masa_pembayaran,
            'cycle' => $request->cycle,
            'kecepatan' => $request->kecepatan,
        ]);

        return redirect()->route('paket.index')
            ->with('success', '✅ Paket berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit untuk paket tertentu.
     */
    public function edit($id)
    {
        $paket = Paket::findOrFail($id);

        return view('content.apps.Paket.edit-paket', compact('paket'));
    }

    /**
     * Memperbarui data paket yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $paket = Paket::findOrFail($id);

        // Bersihkan harga dari format Rp, titik, spasi, dsb
        $harga = preg_replace('/[^\d]/', '', $request->harga);

        // Validasi input (pakai nilai harga bersih)
        $validated = $request->validate([
            'namaTitle' => 'required|string|max:255',
            'masaPembayaran' => 'required|integer|min:1',
            'cycle' => 'required|string|in:daily,weekly,monthly,yearly',
            'kecepatan' => 'required|integer|min:1',
        ]);

        // Update data
        $paket->update([
            'nama_paket' => $validated['namaTitle'],
            'harga' => $harga,
            'masa_pembayaran' => $validated['masaPembayaran'],
            'cycle' => $validated['cycle'],
            'kecepatan' => $validated['kecepatan'],
        ]);

        return redirect()
            ->route('paket.index')
            ->with('success', '✅ Paket berhasil diperbarui!');
    }

    /**
     * Menghapus paket dari database.
     */
    public function destroy($id)
    {
        $paket = Paket::findOrFail($id);

        // Misal paket punya relasi 'pelanggans'
        if ($paket->pelanggans()->count() > 0) {
            return redirect()->route('paket.index')
                ->with('error', '❌ Paket tidak bisa dihapus karena sedang digunakan!');
        }

        $paket->delete();

        return redirect()->route('paket.index')->with('success', '🗑️ Paket berhasil dihapus!');
    }
}
