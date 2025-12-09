<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tanggal dari query string atau default hari ini
        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal)->toDateString() : now()->toDateString();

        // Ambil semua income sesuai tanggal
        $incomes = Income::whereDate('tanggal_masuk', $tanggal)->get();

        // Hitung total masuk
        $totalMasuk = $incomes->sum('jumlah');

        return view('content.apps.Pembukuan.masuk.masuk', compact('incomes', 'tanggal', 'totalMasuk'));
    }

    public function keluar(Request $request)
    {
        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal)->toDateString() : now()->toDateString();

        $expenses = Expense::whereDate('tanggal_keluar', $tanggal)->get();

        $totalKeluar = $expenses->sum('jumlah');

        return view('content.apps.Pembukuan.keluar.keluar', compact('expenses', 'tanggal', 'totalKeluar'));
    }

    public function total(Request $request)
    {
        // Ambil tanggal dari query string atau default hari ini
        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal)->toDateString() : now()->toDateString();

        // Ambil semua Laba Masuk sesuai tanggal
        $incomes = Income::whereDate('tanggal_masuk', $tanggal)->get();
        $totalMasuk = $incomes->sum('jumlah');

        // Ambil semua Laba Keluar sesuai tanggal
        $expenses = Expense::whereDate('tanggal_keluar', $tanggal)->get();
        $totalKeluar = $expenses->sum('jumlah');

        // Hitung saldo bersih
        $saldoBersih = $totalMasuk - $totalKeluar;

        return view('content.apps.Pembukuan.total.total', compact('incomes', 'expenses', 'tanggal', 'totalMasuk', 'totalKeluar', 'saldoBersih'));
    }
}
