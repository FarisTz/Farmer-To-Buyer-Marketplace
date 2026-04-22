<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatbotKnowledge;
use Illuminate\Support\Facades\DB;

class ChatbotKnowledgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $knowledge = [
            [
                'category' => 'general',
                'question' => 'What is FarmMarket?',
                'answer' => 'FarmMarket is a farmer-to-buyer marketplace platform that connects farmers directly with buyers. Farmers can list their crops, manage orders, and receive payments. Buyers can browse crops, place orders, and communicate with farmers.',
                'keywords' => 'farmmarket,platform,marketplace,farmer,buyer',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'category' => 'general',
                'question' => 'How do I register as a farmer?',
                'answer' => 'To register as a farmer: 1. Click "Register" in the top menu 2. Select "Farmer" as your role 3. Fill in your details (name, email, phone, address) 4. Set a secure password 5. Click "Register" 6. Complete your profile and verification to start selling.',
                'keywords' => 'register,farmer,signup,account',
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'category' => 'general',
                'question' => 'How do I register as a buyer?',
                'answer' => 'To register as a buyer: 1. Click "Register" in the top menu 2. Select "Buyer" as your role 3. Fill in your details (name, email, phone, address) 4. Set a secure password 5. Click "Register" 6. Start browsing crops and placing orders.',
                'keywords' => 'register,buyer,account,shop',
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'category' => 'verification',
                'question' => 'What documents do I need for verification?',
                'answer' => 'For ID verification: 1. National ID card (front and back) 2. Passport copy 3. Driver\'s license. For address verification: 1. Utility bill 2. Lease agreement 3. Bank statement. All documents must be clear and recent (within 3 months).',
                'keywords' => 'verify,verification,documents,id,passport,license,bill,lease,statement',
                'priority' => 3,
                'is_active' => true,
            ],
            [
                'category' => 'crops',
                'question' => 'How do I list my crops for sale?',
                'answer' => 'To list crops: 1. Complete your account verification 2. Go to your dashboard 3. Click "My Crops" 4. Click "Add New Crop" 5. Fill in crop details (name, category, price, quantity, description, image) 6. Set availability 7. Submit for review.',
                'keywords' => 'list,crops,sell,marketplace,listing',
                'priority' => 4,
                'is_active' => true,
            ],
            [
                'category' => 'orders',
                'question' => 'How do I track my orders?',
                'answer' => 'Order tracking: 1. Go to "My Orders" in your dashboard 2. View order status 3. Track delivery progress 4. Contact farmers for updates.',
                'keywords' => 'orders,track,delivery,status,shipping',
                'priority' => 5,
                'is_active' => true,
            ],
            [
                'category' => 'payment',
                'question' => 'How do I make payments?',
                'answer' => 'Payment methods: 1. Bank transfer to farmer\'s account 2. Mobile money 3. Cash on delivery. Farmers must verify their account before receiving payments. Payment receipts are required for bank transfers.',
                'keywords' => 'payment,bank,transfer,mobile,money,receipt',
                'priority' => 6,
                'is_active' => true,
            ],
            [
                'category' => 'contact',
                'question' => 'How do I contact support?',
                'answer' => 'Support channels: 1. Use the chat assistant on this page 2. Email support@farmmarket.com 3. Call our helpline 4. Report issues through your dashboard 5. Emergency contacts available 24/7.',
                'keywords' => 'support,help,contact,email,phone,emergency',
                'priority' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($knowledge as $item) {
            ChatbotKnowledge::create($item);
        }
    }
}
