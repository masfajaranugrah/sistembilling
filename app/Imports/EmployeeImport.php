<?php

namespace App\Imports;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToCollection, WithCalculatedFormulas, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['nama_lengkap'])) {
                continue;
            }

            // --- Pisahkan tempat dan tanggal lahir dari satu kolom
            $tempatTanggal = $row['tempat_tanggal_lahir'] ?? $row['tempat_lahir'] ?? null;
            $tempatLahir = null;
            $tanggalLahir = null;

            if (! empty($tempatTanggal)) {
                $parts = explode(',', $tempatTanggal, 2);
                $tempatLahir = trim($parts[0] ?? '');
                $tanggalLahirRaw = trim($parts[1] ?? '');
                $tanggalLahir = $this->formatDate($tanggalLahirRaw);
            }

            if (! $tempatLahir && ! empty($row['tempat_lahir'])) {
                $tempatLahir = $row['tempat_lahir'];
            }
            if (! $tanggalLahir && ! empty($row['tanggal_lahir'])) {
                $tanggalLahir = $this->formatDate($row['tanggal_lahir']);
            }

            $tanggalMasuk = $this->formatDate($row['tanggal_masuk'] ?? null);

            // --- Gunakan NIK sebagai patokan utama
            $nik = $row['nik'] ?? null;

            if ($nik) {
                $employee = Employee::where('nik', $nik)->first();
            } else {
                // fallback jika nik kosong
                $employee = Employee::where('full_name', $row['nama_lengkap'])->first();
            }

            if ($employee) {
                // --- Update data karyawan yang sudah ada
                $employee->update([
                    'nik' => $nik,
                    'full_address' => $row['alamat_lengkap'] ?? null,
                    'place_of_birth' => $tempatLahir,
                    'date_of_birth' => $tanggalLahir,
                    'no_hp' => $this->normalizePhone($row['no_hp'] ?? ''),
                    'tanggal_masuk' => $tanggalMasuk,
                    'jabatan' => $row['jabatan'] ?? null,
                    'bank' => $row['bank'] ?? null,
                    'no_rekening' => $row['no_rekening'] ?? null,
                    'atas_nama' => $row['atas_nama'] ?? null,
                ]);
            } else {
                // --- Buat baru dengan UUID unik
                Employee::create([
                    'id' => (string) Str::uuid(),
                    'nik' => $nik,
                    'full_name' => $row['nama_lengkap'],
                    'full_address' => $row['alamat_lengkap'] ?? null,
                    'place_of_birth' => $tempatLahir,
                    'date_of_birth' => $tanggalLahir,
                    'no_hp' => $this->normalizePhone($row['no_hp'] ?? ''),
                    'tanggal_masuk' => $tanggalMasuk,
                    'jabatan' => $row['jabatan'] ?? null,
                    'bank' => $row['bank'] ?? null,
                    'no_rekening' => $row['no_rekening'] ?? null,
                    'atas_nama' => $row['atas_nama'] ?? null,
                ]);
            }
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

    private function formatDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            $formatted = date('Y-m-d', strtotime($value));

            return $formatted === '1970-01-01' ? null : $formatted;
        } catch (\Exception $e) {
            return null;
        }
    }
}
