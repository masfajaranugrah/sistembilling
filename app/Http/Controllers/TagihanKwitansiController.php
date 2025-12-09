<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TagihanKwitansiController extends Controller
{
    /**
     * Tampilkan halaman daftar tagihan untuk export Excel
     */
    public function index()
    {
        // Ambil semua tagihan beserta relasi
        $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();

        // Tambahkan URL kwitansi untuk view
        foreach ($tagihans as $tagihan) {
            $tagihan->kwitansi_url = $tagihan->kwitansi
                ? asset('storage/'.$tagihan->kwitansi)
                : null;
        }

        return view('content.apps.Laporan.kwitansi', compact('tagihans'));
    }

    /**
     * Export Excel tagihan beserta gambar kwitansi
     */
    public function exportExcel(Request $request)
    {
        $query = Tagihan::with(['pelanggan', 'paket']);

        // Filter status pembayaran
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        // Filter kabupaten/kecamatan melalui relasi pelanggan
        if ($request->filled('kabupaten')) {
            $kabupaten = strtolower($request->kabupaten);
            $query->whereHas('pelanggan', function ($q) use ($kabupaten) {
                $q->whereRaw('LOWER(kabupaten) = ?', [$kabupaten]);
            });
        }

        if ($request->filled('kecamatan')) {
            $kecamatan = strtolower($request->kecamatan);
            $query->whereHas('pelanggan', function ($q) use ($kecamatan) {
                $q->whereRaw('LOWER(kecamatan) = ?', [$kecamatan]);
            });
        }

        $tagihans = $query->get();

        // Download Excel menggunakan TagihanExport
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\KwitansiExport($tagihans),
            'laporan_kwitansi.xlsx'
        );
    }
}
