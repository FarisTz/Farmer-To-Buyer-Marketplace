<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Crop;

class CropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crops = [
            // Farmer ID 2 - John Mwangi (Arusha)
            [
                'farmer_id' => 2,
                'name' => 'Fresh Tomatoes',
                'description' => 'Organic tomatoes grown in the fertile soils of Arusha. Perfect for salads and cooking.',
                'price_per_kg' => 2500.00,
                'available_quantity' => 150.50,
                'unit' => 'kg',
                'category' => 'vegetables',
                'region' => 'Arusha',
                'is_available' => true,
            ],
            [
                'farmer_id' => 2,
                'name' => 'Carrots',
                'description' => 'Sweet and crunchy carrots, rich in vitamins. Great for juicing and cooking.',
                'price_per_kg' => 1800.00,
                'available_quantity' => 80.00,
                'unit' => 'kg',
                'category' => 'vegetables',
                'region' => 'Arusha',
                'is_available' => true,
            ],
            
            // Farmer ID 3 - Mary Kilonzo (Dar es Salaam)
            [
                'farmer_id' => 3,
                'name' => 'Spinach',
                'description' => 'Fresh green spinach leaves, perfect for healthy meals and smoothies.',
                'price_per_kg' => 1200.00,
                'available_quantity' => 45.00,
                'unit' => 'kg',
                'category' => 'vegetables',
                'region' => 'Dar es Salaam',
                'is_available' => true,
            ],
            [
                'farmer_id' => 3,
                'name' => 'Bell Peppers',
                'description' => 'Colorful bell peppers - red, green, and yellow varieties available.',
                'price_per_kg' => 3200.00,
                'available_quantity' => 60.00,
                'unit' => 'kg',
                'category' => 'vegetables',
                'region' => 'Dar es Salaam',
                'is_available' => true,
            ],
            
            // Farmer ID 4 - Joseph Ndungu (Mwanza)
            [
                'farmer_id' => 4,
                'name' => 'Onions',
                'description' => 'Fresh red onions, essential for cooking. Grown in the lakes region.',
                'price_per_kg' => 1500.00,
                'available_quantity' => 200.00,
                'unit' => 'kg',
                'category' => 'vegetables',
                'region' => 'Mwanza',
                'is_available' => true,
            ],
            [
                'farmer_id' => 4,
                'name' => 'Garlic',
                'description' => 'Aromatic garlic bulbs, perfect for adding flavor to dishes.',
                'price_per_kg' => 4500.00,
                'available_quantity' => 30.00,
                'unit' => 'kg',
                'category' => 'vegetables',
                'region' => 'Mwanza',
                'is_available' => true,
            ],
            
            // Farmer ID 5 - Grace Wanjiru (Dodoma)
            [
                'farmer_id' => 5,
                'name' => 'Maize',
                'description' => 'Fresh maize kernels, great for making ugali and other traditional dishes.',
                'price_per_kg' => 800.00,
                'available_quantity' => 500.00,
                'unit' => 'kg',
                'category' => 'grains',
                'region' => 'Dodoma',
                'is_available' => true,
            ],
            [
                'farmer_id' => 5,
                'name' => 'Beans',
                'description' => 'High-quality beans, rich in protein. Perfect for traditional Tanzanian cuisine.',
                'price_per_kg' => 2800.00,
                'available_quantity' => 150.00,
                'unit' => 'kg',
                'category' => 'legumes',
                'region' => 'Dodoma',
                'is_available' => true,
            ],
            
            // Farmer ID 6 - Samuel Kimani (Tanga)
            [
                'farmer_id' => 6,
                'name' => 'Coconuts',
                'description' => 'Fresh coconuts from the coastal region. Perfect for cooking and beverages.',
                'price_per_kg' => 2000.00,
                'available_quantity' => 100.00,
                'unit' => 'kg',
                'category' => 'fruits',
                'region' => 'Tanga',
                'is_available' => true,
            ],
            [
                'farmer_id' => 6,
                'name' => 'Mangoes',
                'description' => 'Sweet tropical mangoes, ripe and ready to eat.',
                'price_per_kg' => 3500.00,
                'available_quantity' => 75.00,
                'unit' => 'kg',
                'category' => 'fruits',
                'region' => 'Tanga',
                'is_available' => true,
            ],
            
            // Farmer ID 7 - Elizabeth Njoroge (Mbeya)
            [
                'farmer_id' => 7,
                'name' => 'Potatoes',
                'description' => 'Fresh potatoes from the highlands, perfect for chips and mashed potatoes.',
                'price_per_kg' => 1200.00,
                'available_quantity' => 300.00,
                'unit' => 'kg',
                'category' => 'vegetables',
                'region' => 'Mbeya',
                'is_available' => true,
            ],
            [
                'farmer_id' => 7,
                'name' => 'Cabbage',
                'description' => 'Fresh green cabbage, great for salads and cooking.',
                'price_per_kg' => 900.00,
                'available_quantity' => 120.00,
                'unit' => 'kg',
                'category' => 'vegetables',
                'region' => 'Mbeya',
                'is_available' => true,
            ],
            
            // Farmer ID 8 - David Mutiso (Morogoro)
            [
                'farmer_id' => 8,
                'name' => 'Rice',
                'description' => 'High-quality aromatic rice, grown in the fertile Morogoro region.',
                'price_per_kg' => 2200.00,
                'available_quantity' => 400.00,
                'unit' => 'kg',
                'category' => 'grains',
                'region' => 'Morogoro',
                'is_available' => true,
            ],
            [
                'farmer_id' => 8,
                'name' => 'Sesame Seeds',
                'description' => 'Premium sesame seeds, rich in nutrients and perfect for cooking.',
                'price_per_kg' => 5500.00,
                'available_quantity' => 50.00,
                'unit' => 'kg',
                'category' => 'seeds',
                'region' => 'Morogoro',
                'is_available' => true,
            ],
            
            // Farmer ID 9 - Susan Wairimu (Kilimanjaro)
            [
                'farmer_id' => 9,
                'name' => 'Coffee Beans',
                'description' => 'Premium Arabica coffee beans from the slopes of Mount Kilimanjaro.',
                'price_per_kg' => 8500.00,
                'available_quantity' => 80.00,
                'unit' => 'kg',
                'category' => 'beverages',
                'region' => 'Kilimanjaro',
                'is_available' => true,
            ],
            [
                'farmer_id' => 9,
                'name' => 'Bananas',
                'description' => 'Sweet bananas, perfect for snacks and smoothies.',
                'price_per_kg' => 1800.00,
                'available_quantity' => 150.00,
                'unit' => 'kg',
                'category' => 'fruits',
                'region' => 'Kilimanjaro',
                'is_available' => true,
            ],
            
            // Farmer ID 10 - Peter Kamau (Iringa)
            [
                'farmer_id' => 10,
                'name' => 'Avocados',
                'description' => 'Creamy avocados, rich in healthy fats. Perfect for salads and toast.',
                'price_per_kg' => 4200.00,
                'available_quantity' => 90.00,
                'unit' => 'kg',
                'category' => 'fruits',
                'region' => 'Iringa',
                'is_available' => true,
            ],
            [
                'farmer_id' => 10,
                'name' => 'Pineapples',
                'description' => 'Sweet and juicy pineapples, grown in the fertile Iringa region.',
                'price_per_kg' => 2800.00,
                'available_quantity' => 60.00,
                'unit' => 'kg',
                'category' => 'fruits',
                'region' => 'Iringa',
                'is_available' => true,
            ],
            
            // Farmer ID 11 - Alice Nyambura (Rukwa)
            [
                'farmer_id' => 11,
                'name' => 'Pumpkin',
                'description' => 'Fresh pumpkins, perfect for soups and traditional dishes.',
                'price_per_kg' => 1500.00,
                'available_quantity' => 100.00,
                'unit' => 'kg',
                'category' => 'vegetables',
                'region' => 'Rukwa',
                'is_available' => true,
            ],
            [
                'farmer_id' => 11,
                'name' => 'Sweet Potatoes',
                'description' => 'Nutritious sweet potatoes, rich in vitamins and fiber.',
                'price_per_kg' => 1300.00,
                'available_quantity' => 180.00,
                'unit' => 'kg',
                'category' => 'vegetables',
                'region' => 'Rukwa',
                'is_available' => true,
            ],
        ];

        foreach ($crops as $crop) {
            Crop::create($crop);
        }
    }
}
