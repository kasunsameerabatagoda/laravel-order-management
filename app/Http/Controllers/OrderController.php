<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer', 'items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded',
            'tax' => 'nullable|numeric|min:0',
            'shipping_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $unitPrice = $product->price;
                $lineTotal = $unitPrice * $quantity;

                $subtotal += $lineTotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                ];

                // Decrement stock if order status is not cancelled
                if ($validated['status'] !== 'cancelled') {
                    $product->decrement('stock_quantity', $quantity);
                }
            }

            $tax = $validated['tax'] ?? 0;
            $shippingFee = $validated['shipping_fee'] ?? 0;
            $totalAmount = $subtotal + $tax + $shippingFee;

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => $validated['customer_id'],
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', "Order #{$order->order_number} created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['customer', 'items.product']);
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('orders.edit', compact('order', 'customers', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded',
            'tax' => 'nullable|numeric|min:0',
            'shipping_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Restore previous product stock quantities before re-processing items
            if ($order->status !== 'cancelled') {
                foreach ($order->items as $oldItem) {
                    if ($oldItem->product_id) {
                        Product::where('id', $oldItem->product_id)->increment('stock_quantity', $oldItem->quantity);
                    }
                }
            }

            // Delete old items
            $order->items()->delete();

            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $unitPrice = $product->price;
                $lineTotal = $unitPrice * $quantity;

                $subtotal += $lineTotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                ]);

                if ($validated['status'] !== 'cancelled') {
                    $product->decrement('stock_quantity', $quantity);
                }
            }

            $tax = $validated['tax'] ?? 0;
            $shippingFee = $validated['shipping_fee'] ?? 0;
            $totalAmount = $subtotal + $tax + $shippingFee;

            $order->update([
                'customer_id' => $validated['customer_id'],
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', "Order #{$order->order_number} updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();

            // Restore product stocks if order wasn't cancelled
            if ($order->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            $orderNumber = $order->order_number;
            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', "Order #{$orderNumber} has been deleted.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:pending,processing,completed,cancelled',
            'payment_status' => 'nullable|in:unpaid,paid,refunded',
        ]);

        $data = array_filter($validated);
        $order->update($data);

        return back()->with('success', "Order #{$order->order_number} status updated.");
    }
}
