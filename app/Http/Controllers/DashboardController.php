<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $pendingOrders = Order::where('status', 'pending')->count();
        $lowStockProducts = Product::where('stock_quantity', '<=', 5)->count();
        $totalCustomers = Customer::count();

        $recentOrders = Order::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'lowStockProducts',
            'totalCustomers',
            'recentOrders'
        ));
    }
}
