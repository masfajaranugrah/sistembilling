<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    // Tampilkan daftar barang keluar
    public function index()
    {
        $barangKeluars = BarangKeluar::with('barang')->get();

        return view('content.apps.BarangKeluar.barangkeluar-list', compact('barangKeluars'));
    }

    // Form tambah barang keluar
    public function create()
    {
        $barangs = Barang::all();

        return view('content.apps.BarangKeluar.add-barangkeluar', compact('barangs'));
    }

    // Simpan barang keluar
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|integer|min:1',
            'diambil_oleh' => 'required|string|max:255',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        // Cek stok cukup
        if ($barang->stok < $request->jumlah) {
            return redirect()->back()->with('error', 'Stok tidak cukup!');
        }

        // Kurangi stok
        $barang->stok -= $request->jumlah;
        $barang->save();

        BarangKeluar::create([
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'diambil_oleh' => $request->diambil_oleh,
            'keterangan' => $request->keterangan,
            'tanggal' => now(),
        ]);

        return redirect()->route('index.barangkeluar')->with('success', 'Barang keluar berhasil ditambahkan.');
    }

    // Form edit barang keluar
    public function edit($id)
    {
        $barangKeluar = BarangKeluar::findOrFail($id);
        $barangs = Barang::all();

        return view('content.apps.BarangKeluar.barangkeluar-edit', compact('barangKeluar', 'barangs'));
    }

    // Update barang keluar
    public function update(Request $request, $id)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|integer|min:1',
            'diambil_oleh' => 'required|string|max:255',
            'tanggal' => 'nullable|date',
        ]);

        $barangKeluar = BarangKeluar::findOrFail($id);
        $newBarangId = $request->barang_id;
        $newJumlah = $request->jumlah;

        $oldBarang = $barangKeluar->barang;
        $oldJumlah = $barangKeluar->jumlah;

        // Jika barang yang sama, hanya sesuaikan stok
        if ($oldBarang->id == $newBarangId) {
            $stokAvailable = $oldBarang->stok + $oldJumlah;
            if ($stokAvailable < $newJumlah) {
                return redirect()->back()->with('error', 'Stok tidak cukup!');
            }
            $oldBarang->stok = $stokAvailable - $newJumlah;
            $oldBarang->save();
        } else {
            // Barang berubah: rollback stok lama
            $oldBarang->stok += $oldJumlah;
            $oldBarang->save();

            // Kurangi stok barang baru
            $newBarang = Barang::findOrFail($newBarangId);
            if ($newBarang->stok < $newJumlah) {
                return redirect()->back()->with('error', 'Stok barang baru tidak cukup!');
            }
            $newBarang->stok -= $newJumlah;
            $newBarang->save();
        }

        // Update record barang keluar
        $barangKeluar->update([
            'barang_id' => $newBarangId,
            'jumlah' => $newJumlah,
            'diambil_oleh' => $request->diambil_oleh,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal ?? now(),
        ]);

        return redirect()->route('index.barangkeluar')->with('success', 'Barang keluar berhasil diperbarui.');
    }

    // Hapus barang keluar
    public function destroy($id)
    {
        $bk = BarangKeluar::findOrFail($id);

        // Kembalikan stok
        $barang = $bk->barang;
        $barang->stok += $bk->jumlah;
        $barang->save();

        $bk->delete();

        return redirect()->route('index.barangkeluar')->with('success', 'Barang keluar berhasil dihapus.');
    }
}
