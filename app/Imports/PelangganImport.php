<?php

namespace App\Imports;

use App\Models\Paket;
use App\Models\Pelanggan;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PelangganImport implements ToCollection, WithCalculatedFormulas, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip row jika nama kosong
            if (empty($row['nama'])) {
                continue;
            }

            // Ambil paket
            $paketUuid = trim($row['paket'] ?? '');
            $paket = Paket::where('id', $paketUuid)->first();

            if (! $paket) {
                $nominal = (int) ($row['nominal'] ?? 0);
                $paket = match ($nominal) {
                    100000 => Paket::where('nama', 'Paket 2')->first(),
                    75000 => Paket::where('nama', 'Paket 1')->first(),
                    120000 => Paket::where('nama', 'Paket 3')->first(),
                    150000 => Paket::where('nama', 'Paket 4')->first(),
                    default => Paket::first(),
                };
            }

            // Insert atau update pelanggan
            Pelanggan::updateOrCreate(
                ['nomer_id' => $row['no_id'] ?? Str::uuid()],
                [
                    'nama_lengkap' => $row['nama'] ?? null,
                    'no_ktp' => $row['no_ktp'] ?? null,
                    'no_whatsapp' => $this->normalizePhone($row['no_hp'] ?? ''),
                    'alamat_jalan' => $row['jalan'] ?? null, // langsung simpan alamat utuh
                    'rt' => $row['rt'] ?? null,
                    'rw' => $row['rw'] ?? null,
                    'desa' => $row['desa'] ?? null,
                    'kecamatan' => $row['kecamatan'] ?? null,
                    'kabupaten' => $row['kabupaten'] ?? null,
                    'provinsi' => $row['provinsi'] ?? null,
                    'kode_pos' => $row['kode_pos'] ?? null,
                    'status' => 'approve',
                    'paket_id' => $paket->id ?? null,
                    'tanggal_mulai' => now(),
                    'tanggal_berakhir' => $row['tanggal_berakhir'] ?? null,
                    'deskripsi' => $row['deskripsi'] ?? null,
                    'biaya_langganan' => $row['biaya_langganan'] ?? null,
                ]
            );
        }
    }

    private function normalizePhone($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        if (substr($number, 0, 1) === '0') {
            $number = '62'.substr($number, 1);
        }

        return $number;
    }
}
