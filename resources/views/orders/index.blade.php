@extends('layouts.app')

@section('title', 'Orders - Order Management System')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Orders List</h1>
            <p class="text-slate-500 mt-1">Search, filter, view, update, and manage customer orders.</p>
        </div>
        <div>
            <a href="{{ route('orders.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2.5 rounded-lg shadow-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Add New Order</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search input -->
            <div>
                <label for="search" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Search</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Order #, Customer..." class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Order Status</label>
                <select name="status" id="status" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Payment Status Filter -->
            <div>
                <label for="payment_status" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Payment Status</label>
                <select name="payment_status" id="payment_status" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Payment Statuses</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium py-2 px-4 rounded-lg text-sm transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'status', 'payment_status']))
                    <a href="{{ route('orders.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 py-2 px-3 rounded-lg text-sm transition flex items-center justify-center" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
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
                        <th class="py-3.5 px-6 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 font-semibold text-indigo-600">
                                <a href="{{ route('orders.show', $order) }}" class="hover:underline flex items-center space-x-1.5">
                                    <i class="fa-solid fa-receipt text-slate-400 text-xs"></i>
                                    <span>{{ $order->order_number }}</span>
                                </a>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium text-slate-900">{{ $order->customer->name ?? 'Deleted Customer' }}</div>
                                <div class="text-xs text-slate-400">{{ $order->customer->email ?? '' }}</div>
                            </td>
                            <td class="py-4 px-6 text-slate-500 text-xs">
                                {{ $order->created_at->format('M d, Y') }}
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
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                        <i class="fa-solid fa-check text-[10px]"></i> Paid
                                    </span>
                                @elseif($order->payment_status === 'unpaid')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                        <i class="fa-solid fa-clock text-[10px]"></i> Unpaid
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                        <i class="fa-solid fa-rotate-left text-[10px]"></i> Refunded
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-900 text-right">
                                ${{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('orders.show', $order) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded transition" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('orders.edit', $order) }}" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded transition" title="Edit Order">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete order {{ $order->order_number }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded transition" title="Delete Order">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-box-open text-4xl text-slate-300 mb-2 block"></i>
                                <span class="font-medium">No orders matching your criteria.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
