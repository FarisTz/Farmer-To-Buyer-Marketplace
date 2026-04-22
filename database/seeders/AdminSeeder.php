<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@farmmarket.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+2341234567890',
            'address' => 'Admin Office, FarmMarket HQ',
            'region' => 'Lagos',
        ]);
    }
}
