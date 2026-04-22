<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FarmerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $farmers = [
            [
                'name' => 'John Mwangi',
                'email' => 'farmer1@eg.com',
                'phone' => '+255123456789',
                'address' => 'Arusha, Tanzania',
                'region' => 'Arusha',
            ],
            [
                'name' => 'Mary Kilonzo',
                'email' => 'farmer2@eg.com',
                'phone' => '+255123456790',
                'address' => 'Dar es Salaam, Tanzania',
                'region' => 'Dar es Salaam',
            ],
            [
                'name' => 'Joseph Ndungu',
                'email' => 'farmer3@eg.com',
                'phone' => '+255123456791',
                'address' => 'Mwanza, Tanzania',
                'region' => 'Mwanza',
            ],
            [
                'name' => 'Grace Wanjiru',
                'email' => 'farmer4@eg.com',
                'phone' => '+255123456792',
                'address' => 'Dodoma, Tanzania',
                'region' => 'Dodoma',
            ],
            [
                'name' => 'Samuel Kimani',
                'email' => 'farmer5@eg.com',
                'phone' => '+255123456793',
                'address' => 'Tanga, Tanzania',
                'region' => 'Tanga',
            ],
            [
                'name' => 'Elizabeth Njoroge',
                'email' => 'farmer6@eg.com',
                'phone' => '+255123456794',
                'address' => 'Mbeya, Tanzania',
                'region' => 'Mbeya',
            ],
            [
                'name' => 'David Mutiso',
                'email' => 'farmer7@eg.com',
                'phone' => '+255123456795',
                'address' => 'Morogoro, Tanzania',
                'region' => 'Morogoro',
            ],
            [
                'name' => 'Susan Wairimu',
                'email' => 'farmer8@eg.com',
                'phone' => '+255123456796',
                'address' => 'Kilimanjaro, Tanzania',
                'region' => 'Kilimanjaro',
            ],
            [
                'name' => 'Peter Kamau',
                'email' => 'farmer9@eg.com',
                'phone' => '+255123456797',
                'address' => 'Iringa, Tanzania',
                'region' => 'Iringa',
            ],
            [
                'name' => 'Alice Nyambura',
                'email' => 'farmer10@eg.com',
                'phone' => '+255123456798',
                'address' => 'Rukwa, Tanzania',
                'region' => 'Rukwa',
            ],
        ];

        foreach ($farmers as $farmer) {
            User::create([
                'name' => $farmer['name'],
                'email' => $farmer['email'],
                'password' => Hash::make('1-8'),
                'role' => 'farmer',
                'phone' => $farmer['phone'],
                'address' => $farmer['address'],
                'region' => $farmer['region'],
            ]);
        }
    }
}
