<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use Illuminate\Http\Request;

class RekeningController extends Controller
{
    // Menampilkan semua rekening
    public function index()
    {
        $rekenings = Rekening::all();

        return view('content.apps.Rekening.rekening', compact('rekenings'));
    }

    // Menampilkan form tambah rekening
    public function create()
    {
        return view('content.apps.Rekening.add-rekening');
    }

    // Menambahkan rekening baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:255',
            'nomor_rekening' => 'required|string|unique:rekenings',
            'nama_pemilik' => 'required|string|max:255', // validasi nama pemilik
        ]);

        $rekening = Rekening::create([
            'nama_bank' => $request->nama_bank,
            'nomor_rekening' => $request->nomor_rekening,
            'nama_pemilik' => $request->nama_pemilik, // simpan nama pemilik
        ]);

        return redirect()->route('rekenings.index')->with('success', 'Rekening berhasil diperbarui');
    }

    // Menampilkan form edit
    public function edit($id)
    {
        $rekening = Rekening::findOrFail($id);

        return view('content.apps.Rekening.edit-rekening', compact('rekening'));
    }

    // Update rekening
    public function update(Request $request, $id)
    {
        $rekening = Rekening::findOrFail($id);

        $request->validate([
            'nama_bank' => 'required|string|max:255',
            'nomor_rekening' => 'required|string|unique:rekenings,nomor_rekening,'.$rekening->id,
            'nama_pemilik' => 'required|string|max:255', // validasi nama pemilik
        ]);

        $rekening->update([
            'nama_bank' => $request->nama_bank,
            'nomor_rekening' => $request->nomor_rekening,
            'nama_pemilik' => $request->nama_pemilik, // update nama pemilik
        ]);

        return redirect()->route('rekenings.index')->with('success', 'Rekening berhasil diperbarui');
    }

    // Hapus rekening
    public function destroy($id)
    {
        $rekening = Rekening::findOrFail($id);
        $rekening->delete();

        return redirect()->route('rekenings.index')->with('success', 'Rekening berhasil dihapus');
    }
}
