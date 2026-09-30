@extends('layouts.app')

@section('title', 'Dashboard - Order Management System')

@section('content')
<div class="space-y-8">
    <!-- Header banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Dashboard Overview</h1>
            <p class="text-slate-500 mt-1">Manage orders, track sales performance, and review customer activity.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('orders.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold shadow transition flex items-center space-x-2">
                <i class="fa-solid fa-cart-plus"></i>
                <span>Create New Order</span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stat Card 1 -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Revenue</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">${{ number_format($totalRevenue, 2) }}</p>
                <p class="text-xs text-emerald-600 mt-1 flex items-center gap-1 font-medium">
                    <i class="fa-solid fa-circle-check"></i> Paid orders total
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Orders</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalOrders) }}</p>
                <p class="text-xs text-slate-500 mt-1">All time orders created</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pending Orders</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($pendingOrders) }}</p>
                <p class="text-xs text-amber-600 mt-1 font-medium">Requires fulfillment</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Low Stock Items</p>
                <p class="text-2xl font-bold text-rose-600 mt-1">{{ number_format($lowStockProducts) }}</p>
                <p class="text-xs text-rose-600 mt-1 font-medium">Stock &le; 5 units</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i>
                <span>Recent Orders</span>
            </h2>
            <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                <span>View All Orders</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider border-b border-slate-200">
                        <th class="py-3.5 px-6 font-semibold">Order #</th>
                        <th class="py-3.5 px-6 font-semibold">Customer</th>
                        <th class="py-3.5 px-6 font-semibold">Date</th>
                        <th class="py-3.5 px-6 font-semibold">Status</th>
                        <th class="py-3.5 px-6 font-semibold">Payment</th>
                        <th class="py-3.5 px-6 font-semibold text-right">Total</th>
                        <th class="py-3.5 px-6 font-semibold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 font-semibold text-indigo-600">
                                <a href="{{ route('orders.show', $order) }}" class="hover:underline">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-900">
                                {{ $order->customer->name ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-slate-500 text-xs">
                                {{ $order->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="py-4 px-6">
                                @if($order->status === 'completed')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">Completed</span>
                                @elseif($order->status === 'processing')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">Processing</span>
                                @elseif($order->status === 'pending')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-200">Pending</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 border border-rose-200">Cancelled</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if($order->payment_status === 'paid')
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700">
                                        <i class="fa-solid fa-circle-dot text-[10px]"></i> Paid
                                    </span>
                                @elseif($order->payment_status === 'unpaid')
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700">
                                        <i class="fa-solid fa-circle-dot text-[10px]"></i> Unpaid
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-500">
                                        <i class="fa-solid fa-circle-dot text-[10px]"></i> Refunded
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-900 text-right">
                                ${{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="py-4 px-6 text-center space-x-2">
                                <a href="{{ route('orders.show', $order) }}" class="text-slate-600 hover:text-indigo-600 px-2 py-1 rounded transition" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('orders.edit', $order) }}" class="text-slate-600 hover:text-amber-600 px-2 py-1 rounded transition" title="Edit Order">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                No orders found. Click "Create New Order" to create your first order.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
