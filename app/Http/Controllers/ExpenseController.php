<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::all();

        return view('content.apps.Laba.keluar.keluar', compact('expenses'));
    }

    public function create()
    {
        $kategori_default = ['Gaji', 'Transportasi', 'Internet', 'DLL'];

        return view('content.apps.Laba.keluar.add-keluar', compact('kategori_default'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'kategori_dll' => 'nullable|string',
            'tanggal_keluar' => 'required|date', // validasi tanggal & jam
        ]);

        // Tentukan kategori final
        $kategori = $request->kategori === 'DLL' && $request->kategori_dll
            ? $request->kategori_dll
            : $request->kategori;

        // Generate kode
        $kode = $this->getKode($kategori);

        Expense::create([
            'kategori' => $kategori,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'kode' => $kode,
            'tanggal_keluar' => $request->tanggal_keluar, // simpan tanggal & jam
        ]);

        return redirect()->route('keluar.index')->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return redirect()->route('keluar.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }

    private function getKode($kategori)
    {
        return match (strtolower($kategori)) {
            'pembelian' => '01',
            'jasa' => '02',
            'gaji' => '03',
            'internet' => '04',
            'transportasi' => '05',
            default => '06',
        };
    }
}
