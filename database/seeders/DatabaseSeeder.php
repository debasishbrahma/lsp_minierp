<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Database\Factories\ProductsFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Create Users
        /*  $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $sales = User::create([
            'name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => bcrypt('password'),
            'role' => 'sales',
        ]);

        // Create Product
        $product = Product::create([
            'name' => 'Sample Product',
            'description' => 'A sample product',
            'unit_price' => 100.00,
            'quantity_available' => 50,
        ]);
 */

        // Create Quotation
        /*  $quotation = Quotation::create([
            'customer_name' => 'John Doe',
            'user_id' => $sales->id,
            'total_price' => 200.00,
            'status' => 'pending',
        ]); */

        // Create Quotation Item
        /*  QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 100.00,
            'subtotal' => 200.00,
        ]); */

        // Create Sample Notification
        /* DB::table('notifications')->insert([
            'id' => Uuid::uuid4()->toString(),
            'type' => 'App\Notifications\QuotationStatusUpdated',
            'notifiable_type' => \App\Models\User::class,
            'notifiable_id' => $sales->id,
            'data' => json_encode([
                'message' => 'Your quotation for John Doe has been approved.',
                'quotation_id' => $quotation->id,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]); */


        // Create Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $sales = User::create([
            'name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => bcrypt('password'),
            'role' => 'sales',
        ]);

        // Create Product
        $product = Product::create([
            'name' => 'Sample Product',
            'description' => 'A sample product',
            'unit_price' => 100.00,
            'quantity_available' => 50,
        ]);

        /* // Create Quotation
        $quotation = Quotation::create([
            'customer_name' => 'John Doe',
            'user_id' => $sales->id,
            'total_price' => 200.00,
            'status' => 'pending',
        ]);

        // Create Quotation Item
        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 100.00,
            'subtotal' => 200.00,
        ]);

        // Create Sample Notification
        $notificationData = [
            'id' => Uuid::uuid4()->toString(),
            'type' => 'App\Notifications\QuotationStatusUpdated',
            'notifiable_type' => \App\Models\User::class,
            'notifiable_id' => $sales->id,
            'data' => json_encode([
                'message' => 'Your quotation for John Doe has been approved.',
                'quotation_id' => $quotation->id,
            ]),
            'read_at' => null,
            'created_at' => '2025-07-22 11:42:19',
            'updated_at' => '2025-07-22 11:42:19',
        ];
        Log::info('Seeding notification', $notificationData);
        DB::table('notifications')->insert($notificationData); */
    }
}
