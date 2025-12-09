<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tema 1: Team 1
        User::create([
            'name' => 'User Team 1',
            'email' => 'team1@example.com',
            'password' => Hash::make('password123'),
            'role' => 'team',
        ]);

        // Tema 2: Team 2
        User::create([
            'name' => 'User Team 2',
            'email' => 'team2@example.com',
            'password' => Hash::make('password123'),
            'role' => 'team',
        ]);

        // Tema 3: Team 3
        User::create([
            'name' => 'User Team 3',
            'email' => 'team3@example.com',
            'password' => Hash::make('password123'),
            'role' => 'team',
        ]);

        // Tema 4: Team 4
        User::create([
            'name' => 'User Team 4',
            'email' => 'team4@example.com',
            'password' => Hash::make('password123'),
            'role' => 'team',
        ]);

        // Tema 5: Team 5
        User::create([
            'name' => 'User Team 5',
            'email' => 'team5@example.com',
            'password' => Hash::make('password123'),
            'role' => 'team',
        ]);
    }
}
