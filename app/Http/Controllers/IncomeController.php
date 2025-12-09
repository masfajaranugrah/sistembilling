<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\LedgerDaily;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::all(); // ambil semua data

        return view('content.apps.Laba.masuk.masuk', compact('incomes'));
    }

    public function create()
    {
        $kategori_default = ['Internet', 'Penjualan', 'Piutang', 'DLL'];

        return view('content.apps.Laba.masuk.add-masuk', compact('kategori_default'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'kategori_dll' => 'nullable|string', // untuk DLL input manual
            'tanggal_masuk' => 'nullable|date',   // bisa diisi tanggal bebas
        ]);

        // Tentukan kategori final
        $kategori = $request->kategori === 'DLL' && $request->kategori_dll
            ? $request->kategori_dll
            : $request->kategori;

        // Generate kode otomatis berdasarkan kategori
        $kode = $this->getKode($kategori);

        // Convert tanggal_masuk ke Carbon, default sekarang jika kosong
        $tanggalMasuk = $request->tanggal_masuk
            ? \Carbon\Carbon::parse($request->tanggal_masuk)
            : now();

        // Simpan income ke database
        $income = Income::create([
            'kategori' => $kategori,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'kode' => $kode,
            'tanggal_masuk' => $tanggalMasuk,
            'created_at' => $tanggalMasuk,
            'updated_at' => $tanggalMasuk,
        ]);

        // Update ledger harian otomatis sesuai tanggal masuk
        $this->updateLedger($tanggalMasuk->toDateString());

        return redirect()->route('income.index')->with('success', 'Laba Masuk berhasil ditambahkan.');
    }

    /**
     * Update ledger harian otomatis sesuai tanggal
     */
    private function updateLedger($tanggal)
    {
        $ledger = LedgerDaily::firstOrCreate(['tanggal' => $tanggal]);

        $ledger->total_masuk = Income::whereDate('tanggal_masuk', $tanggal)->sum('jumlah');
        $ledger->total_keluar = Expense::whereDate('tanggal_keluar', $tanggal)->sum('jumlah'); // sesuaikan field tanggal keluar
        $ledger->saldo = $ledger->total_masuk - $ledger->total_keluar;

        $ledger->save();
    }

    /**
     * Generate kode otomatis per kategori
     */
    private function getKode($kategori)
    {
        return match (strtolower($kategori)) {
            'internet' => '01',
            'penjualan' => '02',
            'piutang' => '03',
            default => 'O4', // DLL atau kategori custom
        };

    }

    public function edit($id)
    {
        $income = Income::findOrFail($id);
        $kategori_default = ['Internet', 'Penjualan', 'Piutang', 'DLL'];

        return view('content.apps.Laba.masuk.edit-masuk', compact('income', 'kategori_default'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'kategori_dll' => 'nullable|string',
            'tanggal_masuk' => 'nullable|date',
        ]);

        $income = Income::findOrFail($id);

        $kategori = $request->kategori === 'DLL' && $request->kategori_dll
            ? $request->kategori_dll
            : $request->kategori;

        $tanggalSebelumnya = $income->tanggal_masuk->toDateString();

        $income->update([
            'kategori' => $kategori,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'tanggal_masuk' => $request->tanggal_masuk ? Carbon::parse($request->tanggal_masuk) : $income->tanggal_masuk,
        ]);

        // Update ledger untuk tanggal lama dan tanggal baru
        $this->updateLedger($tanggalSebelumnya);
        $this->updateLedger($income->tanggal_masuk->toDateString());

        return redirect()->route('income.index')->with('success', 'Laba Masuk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $income = Income::findOrFail($id);
        $tanggal = $income->tanggal_masuk->toDateString();
        $income->delete();

        $this->updateLedger($tanggal);

        return redirect()->route('income.index')->with('success', 'Laba Masuk berhasil dihapus.');
    }

    // Generate kode otomatis per kategori
    // private function getKode($kategori)
    // {
    //     $prefix = match (strtolower($kategori)) {
    //         'internet' => 'IN',
    //         'penjualan' => 'PN',
    //         'piutang' => 'PI',
    //         default => 'OT',
    //     };

    //     $last = Income::where('kategori', $kategori)->orderBy('id', 'desc')->first();
    //     $nextNumber = $last ? intval(substr($last->kode, 2)) + 1 : 1;

    //     return $prefix.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    // }

    // Update ledger harian otomatis
    // private function updateLedger($tanggal)
    // {
    //     $ledger = LedgerDaily::firstOrCreate(['tanggal' => $tanggal]);

    //     $ledger->total_masuk = Income::whereDate('tanggal_masuk', $tanggal)->sum('jumlah');
    //     $ledger->total_keluar = Expense::whereDate('tanggal_keluar', $tanggal)->sum('jumlah');
    //     $ledger->saldo = $ledger->total_masuk - $ledger->total_keluar;

    //     $ledger->save();
    // }
}
