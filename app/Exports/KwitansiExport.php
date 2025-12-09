<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KwitansiExport implements FromCollection, WithHeadings
{
    protected $tagihans;

    public function __construct($tagihans)
    {
        $this->tagihans = $tagihans;
    }

    public function collection()
    {
        return $this->tagihans->map(function ($tagihan) {
            return [
                'Nomor ID' => $tagihan->pelanggan->nomer_id ?? '',
                'Nama Lengkap' => $tagihan->pelanggan->nama_lengkap ?? '',
                'Paket' => $tagihan->paket->nama_paket ?? '',
                'Harga' => $tagihan->paket->harga ?? '',
                'Tanggal Mulai' => $tagihan->tanggal_mulai,
                'Tanggal Berakhir' => $tagihan->tanggal_berakhir,
                'Status Pembayaran' => $tagihan->status_pembayaran,
                'Catatan' => $tagihan->catatan,
                // Gunakan URL/public path agar bisa diakses sebagai link
                'Kwitansi' => $tagihan->kwitansi_path
                    ? asset('storage/'.$tagihan->kwitansi_path)
                    : '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nomor ID', 'Nama Lengkap', 'Paket', 'Harga',
            'Tanggal Mulai', 'Tanggal Berakhir',
            'Status Pembayaran', 'Catatan', 'Kwitansi',
        ];
    }
}
