<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function exportExcel(Request $request)
    {
        // Ambil filter dari request
        $status = $request->input('status');        // status pembayaran
        $kabupaten = $request->input('kabupaten'); // filter kabupaten
        $kecamatan = $request->input('kecamatan'); // filter kecamatan

        // Query tagihan sesuai filter
        $tagihans = Tagihan::with(['pelanggan', 'paket'])
            ->when($status, function ($query, $status) {
                $query->where('status_pembayaran', $status);
            })
            ->when($kabupaten, function ($query, $kabupaten) {
                $query->whereHas('pelanggan', function ($q) use ($kabupaten) {
                    $q->where('kabupaten', $kabupaten);
                });
            })
            ->when($kecamatan, function ($query, $kecamatan) {
                $query->whereHas('pelanggan', function ($q) use ($kecamatan) {
                    $q->where('kecamatan', $kecamatan);
                });
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Pastikan ada data
        if ($tagihans->isEmpty()) {
            return back()->with('error', 'Data tagihan tidak ditemukan untuk filter yang dipilih.');
        }

        // Export ke Excel
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TagihanExport($tagihans),
            'laporan_tagihan.xlsx'
        );
    }

    public function tagihan()
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Ambil semua tagihan beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();
        $kabupatens = Pelanggan::distinct()->pluck('kabupaten');
        $kecamatans = Pelanggan::distinct()->pluck('kecamatan');
        // Statistik
        $totalCustomer = $pelanggan->count();
        $lunas = $tagihans->where('status_pembayaran', 'lunas')->count();
        $belumLunas = $tagihans->where('status_pembayaran', 'belum bayar')->count();
        $totalPaket = $paket->count();

        return view('content.apps.Laporan.tagihan', compact(
            'tagihans',
            'pelanggan',
            'paket',
            'totalCustomer',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatens',
            'kecamatans'
        ));
    }

    public function pembayaran()
    {
        return view('content.apps.Laporan.tagihan');
    }
}
