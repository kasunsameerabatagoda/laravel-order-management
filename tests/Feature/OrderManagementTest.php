<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $product1;
    protected $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::create([
            'name' => 'Acme Test Corp',
            'email' => 'test@acme.com',
            'phone' => '1234567890',
            'city' => 'Testville',
            'address' => '123 Test St',
        ]);

        $this->product1 = Product::create([
            'name' => 'Test Laptop',
            'sku' => 'SKU-LAPTOP-01',
            'price' => 1000.00,
            'stock_quantity' => 20,
            'description' => 'High performance laptop',
        ]);

        $this->product2 = Product::create([
            'name' => 'Test Mouse',
            'sku' => 'SKU-MOUSE-01',
            'price' => 50.00,
            'stock_quantity' => 50,
            'description' => 'Wireless mouse',
        ]);
    }

    public function test_can_display_dashboard()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Overview');
    }

    public function test_can_display_orders_list()
    {
        $response = $this->get('/orders');
        $response->assertStatus(200);
        $response->assertSee('Orders List');
    }

    public function test_can_create_a_new_order()
    {
        $orderData = [
            'customer_id' => $this->customer->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'tax' => 80.00,
            'shipping_fee' => 20.00,
            'notes' => 'Please deliver on weekdays.',
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 1,
                ],
                [
                    'product_id' => $this->product2->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->post('/orders', $orderData);

        // Subtotal = 1000*1 + 50*2 = 1100. Tax = 80, Shipping = 20. Total = 1200.
        $this->assertDatabaseHas('orders', [
            'customer_id' => $this->customer->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => 1100.00,
            'tax' => 80.00,
            'shipping_fee' => 20.00,
            'total_amount' => 1200.00,
        ]);

        $order = Order::first();
        $response->assertRedirect(route('orders.show', $order));

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product1->id,
            'quantity' => 1,
            'unit_price' => 1000.00,
            'total_price' => 1000.00,
        ]);

        // Stock quantity decremented
        $this->assertDatabaseHas('products', [
            'id' => $this->product1->id,
            'stock_quantity' => 19,
        ]);
    }

    public function test_can_update_an_existing_order()
    {
        $order = Order::create([
            'order_number' => 'ORD-20260930-9999',
            'customer_id' => $this->customer->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => 1000.00,
            'tax' => 0.00,
            'shipping_fee' => 0.00,
            'total_amount' => 1000.00,
        ]);

        $order->items()->create([
            'product_id' => $this->product1->id,
            'product_name' => $this->product1->name,
            'quantity' => 1,
            'unit_price' => 1000.00,
            'total_price' => 1000.00,
        ]);

        $updateData = [
            'customer_id' => $this->customer->id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'tax' => 10.00,
            'shipping_fee' => 5.00,
            'notes' => 'Updated notes',
            'items' => [
                [
                    'product_id' => $this->product2->id,
                    'quantity' => 3, // 3 * 50 = 150
                ],
            ],
        ];

        $response = $this->put("/orders/{$order->id}", $updateData);
        $response->assertRedirect(route('orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 150.00,
            'total_amount' => 165.00,
        ]);
    }

    public function test_can_update_order_status_quick_action()
    {
        $order = Order::create([
            'order_number' => 'ORD-20260930-8888',
            'customer_id' => $this->customer->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => 50.00,
            'tax' => 0.00,
            'shipping_fee' => 0.00,
            'total_amount' => 50.00,
        ]);

        $response = $this->patch("/orders/{$order->id}/status", [
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);
    }

    public function test_can_delete_an_order()
    {
        $order = Order::create([
            'order_number' => 'ORD-20260930-7777',
            'customer_id' => $this->customer->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => 50.00,
            'tax' => 0.00,
            'shipping_fee' => 0.00,
            'total_amount' => 50.00,
        ]);

        $response = $this->delete("/orders/{$order->id}");
        $response->assertRedirect(route('orders.index'));

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }
}
