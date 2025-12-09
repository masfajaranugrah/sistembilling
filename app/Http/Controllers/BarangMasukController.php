<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    // Tampilkan daftar barang masuk
    public function index()
    {
        $barangMasuks = BarangMasuk::with('barang')->get();

        return view('content.apps.BarangMasuk.barang-masuk', compact('barangMasuks'));
    }

    // Form tambah barang masuk
    public function create()
    {
        $barangs = Barang::all();

        return view('content.apps.BarangMasuk.add-barangmasuk', compact('barangs'));
    }

    // Simpan barang masuk
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|integer|min:1',
            'jenis' => 'required|in:pembelian,pengembalian_barang',
            'tanggal_masuk' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        // Update stok barang
        $barang->stok += $request->jumlah;
        $barang->save();

        // Simpan record barang masuk
        BarangMasuk::create([
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'jenis' => $request->jenis,
            'tanggal_masuk' => $request->tanggal_masuk,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('index.barangmasuk')->with('success', 'Barang masuk berhasil ditambahkan.');
    }

    // Form edit barang masuk
    public function edit($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        $barangs = Barang::all();

        return view('content.apps.BarangMasuk.barangmasuk-edit', compact('barangMasuk', 'barangs'));
    }

    // Update barang masuk
    public function update(Request $request, $id)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|integer|min:1',
            'jenis' => 'required|in:pembelian,pengembalian_barang',
            'tanggal_masuk' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $barangMasuk = BarangMasuk::findOrFail($id);

        // Kembalikan stok lama
        $oldBarang = $barangMasuk->barang;
        $oldBarang->stok -= $barangMasuk->jumlah; // rollback stok lama
        $oldBarang->save();

        // Tambahkan stok baru
        $newBarang = Barang::findOrFail($request->barang_id);
        $newBarang->stok += $request->jumlah;
        $newBarang->save();

        // Update record barang masuk
        $barangMasuk->update([
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'jenis' => $request->jenis,
            'tanggal_masuk' => $request->tanggal_masuk,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('index.barangmasuk')->with('success', 'Barang masuk berhasil diperbarui.');
    }

    // Hapus barang masuk
    public function destroy($id)
    {
        $bm = BarangMasuk::findOrFail($id);

        // Kurangi stok saat dihapus
        $barang = $bm->barang;
        $barang->stok -= $bm->jumlah;
        $barang->save();

        $bm->delete();

        return redirect()->route('index.barangmasuk')->with('success', 'Barang masuk berhasil dihapus.');
    }
}
