<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BarangController extends Controller
{
    // LIST DATA
    public function index()
    {
        $barangs = Barang::all();

        return view('content.apps.Barang.barang-list', compact('barangs'));
    }

    // FORM CREATE
    public function create()
    {
        return view('content.apps.Barang.add-barang');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $stok = $request->stok ?? 0;

        Barang::create([
            'id' => Str::uuid(),
            'nama_barang' => $request->nama_barang,
            'stok' => $stok,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('barangs')->with('success', 'Barang berhasil ditambahkan');
    }

    // DETAIL
    public function show($id)
    {
        $barang = Barang::findOrFail($id);

        return view('content.apps.Barang.barang-edit', compact('barang'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Cari barang
        $barang = Barang::findOrFail($id);

        // Update data
        $barang->update([
            'nama_barang' => $request->nama_barang,
            'stok' => $request->stok,
            'keterangan' => $request->keterangan,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('barangs')->with('success', 'Barang berhasil diperbarui');
    }

    // DELETE
    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('barangs')->with('success', 'Barang berhasil dihapus');
    }
}
