<?php

namespace Database\Seeders;

use App\Models\Paket;
use App\Models\Pelanggan;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Ambil semua paket_id dari tabel Paket
        $paketIds = Paket::pluck('id')->toArray();

        if (empty($paketIds)) {
            $this->command->info('Tidak ada paket di database. Jalankan PaketSeeder dulu!');

            return;
        }

        $total = 20; // jumlah pelanggan dummy

        for ($i = 0; $i < $total; $i++) {
            Pelanggan::create([
                'id' => (string) Str::uuid(),
                'nama_lengkap' => $faker->name(),
                'no_ktp' => $faker->numerify('################'),
                'no_whatsapp' => $faker->numerify('08##########'),
                'no_telp' => $faker->phoneNumber(),
                'paket_id' => $faker->randomElement($paketIds), // ambil UUID dari paket
                'nomer_id' => 'PLG'.$faker->unique()->numerify('#####'),
                'tanggal_mulai' => $faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d'),
                'tanggal_berakhir' => $faker->dateTimeBetween('now', '+1 years')->format('Y-m-d'),
                'deskripsi' => $faker->sentence(),
                'foto_ktp' => 'public/foto_ktp/dummy.png', // path dummy
                'status' => $faker->randomElement(['pending', 'approve', 'reject']), // ENUM valid
                'alamat_jalan' => $faker->streetAddress(),
                'rt' => $faker->numberBetween(1, 10),
                'rw' => $faker->numberBetween(1, 10),
                'desa' => $faker->city(),
                'kecamatan' => $faker->citySuffix(),
                'kabupaten' => $faker->city(),
                'provinsi' => $faker->state(),
                'kode_pos' => $faker->postcode(),
            ]);
        }

        $this->command->info("$total pelanggan berhasil dibuat!");
    }
}
