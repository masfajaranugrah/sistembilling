<?php

namespace Database\Seeders;

use App\Models\Employee;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $positions = ['Manager', 'Supervisor', 'Staff', 'Admin', 'HRD', 'Finance', 'Marketing', 'Developer', 'Designer', 'Sales'];

        for ($i = 1; $i <= 50; $i++) {
            Employee::create([
                'full_name' => $faker->name(),
                'full_address' => $faker->address(),
                'place_of_birth' => $faker->city(),
                'date_of_birth' => $faker->date('Y-m-d', '2005-12-31'),
                'no_hp' => $faker->phoneNumber(),
                'tanggal_masuk' => $faker->date('Y-m-d', '2023-12-31'),
                'jabatan' => $faker->randomElement($positions),
                'bank' => $faker->randomElement(['BCA', 'Mandiri', 'BRI', 'BNI', 'CIMB Niaga']),
                'no_rekening' => $faker->numerify('##########'),
                'atas_nama' => $faker->name(),
            ]);
        }
    }
}
