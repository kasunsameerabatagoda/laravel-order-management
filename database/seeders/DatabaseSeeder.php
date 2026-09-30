<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF;');
        Customer::truncate();
        Product::truncate();
        Order::truncate();
        OrderItem::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        // Create Seed Customers
        $customers = [
            [
                'name' => 'Acme Corporation',
                'email' => 'contact@acme.com',
                'phone' => '+1 (555) 019-2831',
                'city' => 'New York',
                'address' => '742 Evergreen Terrace, Suite 100',
            ],
            [
                'name' => 'Jane Smith Tech',
                'email' => 'jane.smith@techcorp.io',
                'phone' => '+1 (555) 084-9201',
                'city' => 'San Francisco',
                'address' => '100 Innovation Way',
            ],
            [
                'name' => 'Apex Retailers',
                'email' => 'orders@apexretail.com',
                'phone' => '+1 (555) 012-3456',
                'city' => 'Chicago',
                'address' => '456 Michigan Ave',
            ],
            [
                'name' => 'Starlight Media',
                'email' => 'billing@starlight.net',
                'phone' => '+1 (555) 098-7654',
                'city' => 'Los Angeles',
                'address' => '888 Sunset Blvd',
            ],
            [
                'name' => 'Global Logistics Inc',
                'email' => 'procurement@globallogistics.com',
                'phone' => '+1 (555) 043-1122',
                'city' => 'Seattle',
                'address' => '1200 Pine Street',
            ],
        ];

        foreach ($customers as $c) {
            Customer::create($c);
        }

        // Create Seed Products
        $products = [
            [
                'name' => 'Ultra HD 27" Monitor',
                'sku' => 'MON-27-4K',
                'price' => 349.99,
                'stock_quantity' => 25,
                'description' => '4K IPS display with HDR400 and USB-C hub',
            ],
            [
                'name' => 'Ergonomic Mechanical Keyboard',
                'sku' => 'KB-MECH-RGB',
                'price' => 129.50,
                'stock_quantity' => 40,
                'description' => 'Hot-swappable tactile switches with customizable RGB backlight',
            ],
            [
                'name' => 'Wireless Noise-Canceling Headphones',
                'sku' => 'AUD-NC-HEADPH',
                'price' => 199.00,
                'stock_quantity' => 15,
                'description' => 'Active noise cancellation with 35h battery life',
            ],
            [
                'name' => 'USB-C Dual Docking Station',
                'sku' => 'DOCK-USBC-101',
                'price' => 89.99,
                'stock_quantity' => 3, // Low stock alert demo
                'description' => 'Dual 4K HDMI ports, 100W PD charging, 4x USB 3.0',
            ],
            [
                'name' => 'Standing Desk Dual Motor Frame',
                'sku' => 'FURN-DESK-MOTOR',
                'price' => 450.00,
                'stock_quantity' => 8,
                'description' => 'Heavy duty motorized height adjustable standing desk frame',
            ],
            [
                'name' => 'Precision Ergonomic Wireless Mouse',
                'sku' => 'MSE-WL-PREC',
                'price' => 64.99,
                'stock_quantity' => 50,
                'description' => 'Hyper-fast scrolling wheel and ergonomic thumb rest',
            ],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }

        // Create Seed Orders
        $customerModels = Customer::all();
        $productModels = Product::all();

        // Sample Order 1: Completed & Paid
        $o1 = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id' => $customerModels[0]->id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 829.49,
            'tax' => 66.36,
            'shipping_fee' => 15.00,
            'total_amount' => 910.85,
            'notes' => 'Expedited shipping requested. Customer called to confirm delivery.',
        ]);

        $o1->items()->create([
            'product_id' => $productModels[0]->id,
            'product_name' => $productModels[0]->name,
            'quantity' => 2,
            'unit_price' => 349.99,
            'total_price' => 699.98,
        ]);
        $o1->items()->create([
            'product_id' => $productModels[1]->id,
            'product_name' => $productModels[1]->name,
            'quantity' => 1,
            'unit_price' => 129.50,
            'total_price' => 129.50,
        ]);

        // Sample Order 2: Processing & Paid
        $o2 = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id' => $customerModels[1]->id,
            'status' => 'processing',
            'payment_status' => 'paid',
            'subtotal' => 649.00,
            'tax' => 51.92,
            'shipping_fee' => 20.00,
            'total_amount' => 720.92,
            'notes' => 'Shipment package ready in warehouse.',
        ]);

        $o2->items()->create([
            'product_id' => $productModels[4]->id,
            'product_name' => $productModels[4]->name,
            'quantity' => 1,
            'unit_price' => 450.00,
            'total_price' => 450.00,
        ]);
        $o2->items()->create([
            'product_id' => $productModels[2]->id,
            'product_name' => $productModels[2]->name,
            'quantity' => 1,
            'unit_price' => 199.00,
            'total_price' => 199.00,
        ]);

        // Sample Order 3: Pending & Unpaid
        $o3 = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id' => $customerModels[2]->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => 244.97,
            'tax' => 19.60,
            'shipping_fee' => 10.00,
            'total_amount' => 274.57,
            'notes' => 'Awaiting bank transfer confirmation from accounting department.',
        ]);

        $o3->items()->create([
            'product_id' => $productModels[3]->id,
            'product_name' => $productModels[3]->name,
            'quantity' => 1,
            'unit_price' => 89.99,
            'total_price' => 89.99,
        ]);
        $o3->items()->create([
            'product_id' => $productModels[5]->id,
            'product_name' => $productModels[5]->name,
            'quantity' => 2,
            'unit_price' => 64.99,
            'total_price' => 129.98,
        ]);
    }
}
