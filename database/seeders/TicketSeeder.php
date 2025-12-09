<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\Ticket;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Ambil semua UUID pelanggan
        $pelangganIds = Pelanggan::pluck('id')->toArray();

        // Ambil semua user dengan role 'team'
        $teamUserIds = User::where('role', 'team')->pluck('id')->toArray();

        if (empty($teamUserIds)) {
            $this->command->info('Tidak ada user dengan role team. Seeder dibatalkan.');

            return;
        }

        for ($i = 1; $i <= 50; $i++) {
            Ticket::create([
                'pelanggan_id' => $faker->randomElement($pelangganIds),
                'phone' => $faker->phoneNumber,
                'location_link' => $faker->url,
                'category' => $faker->randomElement(['Network', 'Hardware', 'Software', 'Other']),
                'issue_description' => $faker->sentence(10),
                'additional_note' => $faker->sentence(6),
                'cs_note' => $faker->sentence(5),
                'technician_note' => $faker->sentence(5),
                'technician_attachment' => null,
                'attachment' => null,
                'complaint_source' => $faker->randomElement(['whatsapp', 'telepon', 'datang', 'email', 'app']),
                'priority' => $faker->randomElement(['urgent', 'medium', 'low']),
                'technician_group_id' => rand(1, 5),
                'user_id' => $faker->randomElement($teamUserIds), // pastikan ambil dari role 'team'
                'status' => $faker->randomElement(['pending', 'assigned', 'progress', 'finished', 'approved', 'rejected']),
                'created_by' => rand(1, 5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
