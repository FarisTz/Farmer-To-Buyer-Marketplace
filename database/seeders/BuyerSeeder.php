<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BuyerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buyers = [
            [
                'name' => 'Ahmed Hassan',
                'email' => 'buyer1@eg.com',
                'phone' => '+255987654321',
                'address' => 'Kariakoo, Dar es Salaam',
                'region' => 'Dar es Salaam',
            ],
            [
                'name' => 'Fatuma Mohamed',
                'email' => 'buyer2@eg.com',
                'phone' => '+255987654322',
                'address' => 'Mlimani City, Dar es Salaam',
                'region' => 'Dar es Salaam',
            ],
            [
                'name' => 'James Wilson',
                'email' => 'buyer3@eg.com',
                'phone' => '+255987654323',
                'address' => 'Town Area, Arusha',
                'region' => 'Arusha',
            ],
            [
                'name' => 'Grace Mbeki',
                'email' => 'buyer4@eg.com',
                'phone' => '+255987654324',
                'address' => 'Nyamwezi, Mwanza',
                'region' => 'Mwanza',
            ],
            [
                'name' => 'Robert Kimathi',
                'email' => 'buyer5@eg.com',
                'phone' => '+255987654325',
                'address' => 'Industrial Area, Dodoma',
                'region' => 'Dodoma',
            ],
            [
                'name' => 'Sarah Chen',
                'email' => 'buyer6@eg.com',
                'phone' => '+255987654326',
                'address' => 'City Center, Tanga',
                'region' => 'Tanga',
            ],
            [
                'name' => 'Michael Ochieng',
                'email' => 'buyer7@eg.com',
                'phone' => '+255987654327',
                'address' => 'Market Street, Mbeya',
                'region' => 'Mbeya',
            ],
            [
                'name' => 'Linda Mwangi',
                'email' => 'buyer8@eg.com',
                'phone' => '+255987654328',
                'address' => 'Business District, Morogoro',
                'region' => 'Morogoro',
            ],
            [
                'name' => 'Daniel Kariuki',
                'email' => 'buyer9@eg.com',
                'phone' => '+255987654329',
                'address' => 'Main Market, Kilimanjaro',
                'region' => 'Kilimanjaro',
            ],
            [
                'name' => 'Jennifer Aisha',
                'email' => 'buyer10@eg.com',
                'phone' => '+255987654330',
                'address' => 'Central Business, Iringa',
                'region' => 'Iringa',
            ],
        ];

        foreach ($buyers as $buyer) {
            User::create([
                'name' => $buyer['name'],
                'email' => $buyer['email'],
                'password' => Hash::make('1-8'),
                'role' => 'buyer',
                'phone' => $buyer['phone'],
                'address' => $buyer['address'],
                'region' => $buyer['region'],
            ]);
        }
    }
}
