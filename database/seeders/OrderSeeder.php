<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentReceipt;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [
            // Order 1: Buyer 12 (John Buyer) from Farmer 2 (John Mwangi)
            [
                'buyer_id' => 12,
                'order_number' => 'ORD20260413001',
                'total_amount' => 7500.00,
                'status' => 'confirmed',
                'delivery_address' => 'Dar es Salaam, Tanzania',
                'phone' => '+255123456800',
                'notes' => 'Please deliver fresh produce',
                'items' => [
                    ['crop_id' => 1, 'quantity' => 2.0, 'price_per_kg' => 2500.00], // Tomatoes
                    ['crop_id' => 2, 'quantity' => 1.5, 'price_per_kg' => 1800.00], // Carrots
                ],
            ],
            
            // Order 2: Buyer 13 from Farmer 3
            [
                'buyer_id' => 13,
                'order_number' => 'ORD20260413002',
                'total_amount' => 5400.00,
                'status' => 'pending',
                'delivery_address' => 'Arusha, Tanzania',
                'phone' => '+255123456801',
                'notes' => 'Need vegetables for restaurant',
                'items' => [
                    ['crop_id' => 3, 'quantity' => 3.0, 'price_per_kg' => 1200.00], // Spinach
                    ['crop_id' => 4, 'quantity' => 1.0, 'price_per_kg' => 3200.00], // Bell Peppers
                ],
            ],
            
            // Order 3: Buyer 14 from Farmer 4
            [
                'buyer_id' => 14,
                'order_number' => 'ORD20260413003',
                'total_amount' => 12000.00,
                'status' => 'delivered',
                'delivery_address' => 'Mwanza, Tanzania',
                'phone' => '+255123456802',
                'notes' => 'Bulk order for grocery store',
                'items' => [
                    ['crop_id' => 5, 'quantity' => 5.0, 'price_per_kg' => 1500.00], // Onions
                    ['crop_id' => 6, 'quantity' => 2.0, 'price_per_kg' => 4500.00], // Garlic
                ],
            ],
            
            // Order 4: Buyer 15 from Farmer 5
            [
                'buyer_id' => 15,
                'order_number' => 'ORD20260413004',
                'total_amount' => 7200.00,
                'status' => 'pending_payment',
                'delivery_address' => 'Dodoma, Tanzania',
                'phone' => '+255123456803',
                'notes' => 'Payment via bank transfer',
                'items' => [
                    ['crop_id' => 7, 'quantity' => 4.0, 'price_per_kg' => 800.00],  // Maize
                    ['crop_id' => 8, 'quantity' => 2.0, 'price_per_kg' => 2800.00], // Beans
                ],
            ],
            
            // Order 5: Buyer 16 from Farmer 6
            [
                'buyer_id' => 16,
                'order_number' => 'ORD20260413005',
                'total_amount' => 11000.00,
                'status' => 'confirmed',
                'delivery_address' => 'Tanga, Tanzania',
                'phone' => '+255123456804',
                'notes' => 'Fresh fruits needed',
                'items' => [
                    ['crop_id' => 9, 'quantity' => 3.0, 'price_per_kg' => 2000.00], // Coconuts
                    ['crop_id' => 10, 'quantity' => 2.0, 'price_per_kg' => 3500.00], // Mangoes
                ],
            ],
            
            // Order 6: Buyer 17 from Farmer 7
            [
                'buyer_id' => 17,
                'order_number' => 'ORD20260413006',
                'total_amount' => 6300.00,
                'status' => 'delivered',
                'delivery_address' => 'Mbeya, Tanzania',
                'phone' => '+255123456805',
                'notes' => 'Vegetables for hotel kitchen',
                'items' => [
                    ['crop_id' => 11, 'quantity' => 3.0, 'price_per_kg' => 1200.00], // Potatoes
                    ['crop_id' => 12, 'quantity' => 3.0, 'price_per_kg' => 900.00],  // Cabbage
                ],
            ],
            
            // Order 7: Buyer 18 from Farmer 8
            [
                'buyer_id' => 18,
                'order_number' => 'ORD20260413007',
                'total_amount' => 15400.00,
                'status' => 'pending',
                'delivery_address' => 'Morogoro, Tanzania',
                'phone' => '+255123456806',
                'notes' => 'Grains and seeds order',
                'items' => [
                    ['crop_id' => 13, 'quantity' => 5.0, 'price_per_kg' => 2200.00], // Rice
                    ['crop_id' => 14, 'quantity' => 1.0, 'price_per_kg' => 5500.00], // Sesame Seeds
                ],
            ],
            
            // Order 8: Buyer 19 from Farmer 9
            [
                'buyer_id' => 19,
                'order_number' => 'ORD20260413008',
                'total_amount' => 10300.00,
                'status' => 'confirmed',
                'delivery_address' => 'Kilimanjaro, Tanzania',
                'phone' => '+255123456807',
                'notes' => 'Premium coffee and fruits',
                'items' => [
                    ['crop_id' => 15, 'quantity' => 1.0, 'price_per_kg' => 8500.00], // Coffee Beans
                    ['crop_id' => 16, 'quantity' => 2.0, 'price_per_kg' => 1800.00], // Bananas
                ],
            ],
            
            // Order 9: Buyer 20 from Farmer 10
            [
                'buyer_id' => 20,
                'order_number' => 'ORD20260413009',
                'total_amount' => 14000.00,
                'status' => 'pending_payment',
                'delivery_address' => 'Iringa, Tanzania',
                'phone' => '+255123456808',
                'notes' => 'Tropical fruits order',
                'items' => [
                    ['crop_id' => 17, 'quantity' => 2.0, 'price_per_kg' => 4200.00], // Avocados
                    ['crop_id' => 18, 'quantity' => 2.0, 'price_per_kg' => 2800.00], // Pineapples
                ],
            ],
            
            // Order 10: Buyer 21 from Farmer 11
            [
                'buyer_id' => 21,
                'order_number' => 'ORD20260413010',
                'total_amount' => 5600.00,
                'status' => 'delivered',
                'delivery_address' => 'Rukwa, Tanzania',
                'phone' => '+255123456809',
                'notes' => 'Root vegetables for market',
                'items' => [
                    ['crop_id' => 19, 'quantity' => 2.0, 'price_per_kg' => 1500.00], // Pumpkin
                    ['crop_id' => 20, 'quantity' => 2.0, 'price_per_kg' => 1300.00], // Sweet Potatoes
                ],
            ],
            
            // Additional orders with different statuses
            // Order 11: Mixed order from Farmer 2
            [
                'buyer_id' => 12,
                'order_number' => 'ORD20260413011',
                'total_amount' => 8600.00,
                'status' => 'payment_rejected',
                'delivery_address' => 'Dar es Salaam, Tanzania',
                'phone' => '+255123456800',
                'notes' => 'Payment rejected - invalid receipt',
                'items' => [
                    ['crop_id' => 1, 'quantity' => 3.0, 'price_per_kg' => 2500.00], // Tomatoes
                    ['crop_id' => 2, 'quantity' => 2.0, 'price_per_kg' => 1800.00], // Carrots
                ],
            ],
            
            // Order 12: Large order from Farmer 3
            [
                'buyer_id' => 14,
                'order_number' => 'ORD20260413012',
                'total_amount' => 13200.00,
                'status' => 'confirmed',
                'delivery_address' => 'Arusha, Tanzania',
                'phone' => '+255123456802',
                'notes' => 'Large restaurant order',
                'items' => [
                    ['crop_id' => 3, 'quantity' => 6.0, 'price_per_kg' => 1200.00], // Spinach
                    ['crop_id' => 4, 'quantity' => 3.0, 'price_per_kg' => 3200.00], // Bell Peppers
                ],
            ],
            
            // Order 13: Bank transfer order from Farmer 4
            [
                'buyer_id' => 15,
                'order_number' => 'ORD20260413013',
                'total_amount' => 9000.00,
                'status' => 'pending_payment',
                'delivery_address' => 'Mwanza, Tanzania',
                'phone' => '+255123456803',
                'notes' => 'Bank transfer payment pending',
                'items' => [
                    ['crop_id' => 5, 'quantity' => 6.0, 'price_per_kg' => 1500.00], // Onions
                ],
            ],
            
            // Order 14: Cash on delivery from Farmer 5
            [
                'buyer_id' => 16,
                'order_number' => 'ORD20260413014',
                'total_amount' => 5600.00,
                'status' => 'pending',
                'delivery_address' => 'Dodoma, Tanzania',
                'phone' => '+255123456804',
                'notes' => 'Cash on delivery preferred',
                'items' => [
                    ['crop_id' => 7, 'quantity' => 7.0, 'price_per_kg' => 800.00],  // Maize
                ],
            ],
            
            // Order 15: Delivered order from Farmer 6
            [
                'buyer_id' => 17,
                'order_number' => 'ORD20260413015',
                'total_amount' => 7000.00,
                'status' => 'delivered',
                'delivery_address' => 'Tanga, Tanzania',
                'phone' => '+255123456805',
                'notes' => 'Successfully delivered',
                'items' => [
                    ['crop_id' => 9, 'quantity' => 3.5, 'price_per_kg' => 2000.00], // Coconuts
                ],
            ],
        ];

        foreach ($orders as $orderData) {
            // Create order
            $order = Order::create([
                'buyer_id' => $orderData['buyer_id'],
                'order_number' => $orderData['order_number'],
                'total_amount' => $orderData['total_amount'],
                'status' => $orderData['status'],
                'delivery_address' => $orderData['delivery_address'],
                'phone' => $orderData['phone'],
                'notes' => $orderData['notes'],
            ]);

            // Create order items
            foreach ($orderData['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'crop_id' => $item['crop_id'],
                    'farmer_id' => $this->getFarmerIdForCrop($item['crop_id']),
                    'quantity' => $item['quantity'],
                    'price_per_kg' => $item['price_per_kg'],
                    'total_price' => $item['quantity'] * $item['price_per_kg'],
                ]);
            }

            // Create payment receipt for pending_payment orders
            if ($orderData['status'] === 'pending_payment') {
                PaymentReceipt::create([
                    'order_id' => $order->id,
                    'buyer_id' => $orderData['buyer_id'],
                    'farmer_id' => $this->getFarmerIdForOrder($orderData['items']),
                    'receipt_image' => 'receipt_' . $order->order_number . '.jpg',
                    'amount_paid' => $orderData['total_amount'],
                    'payment_method' => 'bank_transfer',
                    'transaction_reference' => 'TRX' . Str::random(8),
                    'notes' => 'Payment receipt uploaded',
                    'status' => 'pending',
                    'payment_date' => now(),
                ]);
            }
        }
    }

    /**
     * Get farmer ID for a specific crop
     */
    private function getFarmerIdForCrop($cropId): int
    {
        $cropFarmerMapping = [
            1 => 2, 2 => 2,   // Farmer 2
            3 => 3, 4 => 3,   // Farmer 3
            5 => 4, 6 => 4,   // Farmer 4
            7 => 5, 8 => 5,   // Farmer 5
            9 => 6, 10 => 6,  // Farmer 6
            11 => 7, 12 => 7, // Farmer 7
            13 => 8, 14 => 8, // Farmer 8
            15 => 9, 16 => 9, // Farmer 9
            17 => 10, 18 => 10, // Farmer 10
            19 => 11, 20 => 11, // Farmer 11
        ];

        return $cropFarmerMapping[$cropId] ?? 2;
    }

    /**
     * Get farmer ID for order (from first item)
     */
    private function getFarmerIdForOrder($items): int
    {
        if (empty($items)) {
            return 2;
        }

        $firstCropId = $items[0]['crop_id'];
        return $this->getFarmerIdForCrop($firstCropId);
    }
}
