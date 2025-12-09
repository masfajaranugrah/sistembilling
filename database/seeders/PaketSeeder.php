<?php

namespace Database\Seeders;

use App\Models\Paket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pakets = [
            [
                'nama_paket' => 'Paket Basic',
                'harga' => 100000,
                'masa_pembayaran' => 30,
                'cycle' => 'bulanan',
                'kecepatan' => '10',
            ],
            [
                'nama_paket' => 'Paket Standard',
                'harga' => 150000,
                'masa_pembayaran' => 30,
                'cycle' => 'bulanan',
                'kecepatan' => '20',
            ],
            [
                'nama_paket' => 'Paket Premium',
                'harga' => 250000,
                'masa_pembayaran' => 30,
                'cycle' => 'bulanan',
                'kecepatan' => '50',
            ],
            [
                'nama_paket' => 'Paket Ultimate',
                'harga' => 350000,
                'masa_pembayaran' => 30,
                'cycle' => 'bulanan',
                'kecepatan' => '100',
            ],
            [
                'nama_paket' => 'Paket VIP',
                'harga' => 500000,
                'masa_pembayaran' => 30,
                'cycle' => 'bulanan',
                'kecepatan' => '200',
            ],
        ];

        foreach ($pakets as $paket) {
            Paket::create(array_merge($paket, ['id' => (string) Str::uuid()]));
        }

        $this->command->info(count($pakets).' paket berhasil dibuat!');
    }
}
