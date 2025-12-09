<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TagihanExport implements FromCollection, WithHeadings
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
                'Nomor ID' => $tagihan->pelanggan->nomer_id ?? '-',
                'Nama Lengkap' => $tagihan->pelanggan->nama_lengkap ?? '-',
                'Alamat Jalan' => $tagihan->pelanggan->alamat_jalan ?? '-',
                'RT' => $tagihan->pelanggan->rt ?? '-',
                'RW' => $tagihan->pelanggan->rw ?? '-',
                'Desa' => $tagihan->pelanggan->desa ?? '-',
                'Kecamatan' => $tagihan->pelanggan->kecamatan ?? '-',
                'Kabupaten' => $tagihan->pelanggan->kabupaten ?? '-',
                'Provinsi' => $tagihan->pelanggan->provinsi ?? '-',
                'Kode Pos' => $tagihan->pelanggan->kode_pos ?? '-',
                'Paket' => $tagihan->paket->nama_paket ?? '-',
                'Harga' => $tagihan->paket->harga ? number_format($tagihan->paket->harga, 0, ',', '.') : '-',
                'Kecepatan' => $tagihan->paket->kecepatan ?? '-',
                'Tanggal Mulai' => $tagihan->tanggal_mulai ? $tagihan->tanggal_mulai : '-',
                'Tanggal Berakhir' => $tagihan->tanggal_berakhir ? $tagihan->tanggal_berakhir : '-',
                'Status Pembayaran' => ucfirst($tagihan->status_pembayaran ?? '-'),
                'Bukti Pembayaran' => $tagihan->bukti_pembayaran ? asset('storage/'.$tagihan->bukti_pembayaran) : '-',
                'Catatan' => $tagihan->catatan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nomor ID', 'Nama Lengkap', 'Alamat Jalan', 'RT', 'RW',
            'Desa', 'Kecamatan', 'Kabupaten', 'Provinsi', 'Kode Pos',
            'Paket', 'Harga', 'Kecepatan',
            'Tanggal Mulai', 'Tanggal Berakhir',
            'Status Pembayaran', 'Bukti Pembayaran', 'Catatan',
        ];
    }
}
