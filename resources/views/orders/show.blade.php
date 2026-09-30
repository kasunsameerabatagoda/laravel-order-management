@extends('layouts.app')

@section('title', "Order {$order->order_number} - Details")

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Action Bar (hidden on print) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div class="flex items-center space-x-3">
            <a href="{{ route('orders.index') }}" class="p-2.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 transition" title="Back to Orders">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                    <span>Order {{ $order->order_number }}</span>
                </h1>
                <p class="text-xs text-slate-500">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <button onclick="window.print();" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2 rounded-lg text-sm font-semibold transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Print Invoice</span>
            </button>
            <a href="{{ route('orders.edit', $order) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-3.5 py-2 rounded-lg text-sm font-semibold transition flex items-center space-x-2">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Order</span>
            </a>
            <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-2 rounded-lg text-sm font-semibold transition flex items-center space-x-2">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>Delete</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Printable Invoice Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-8 print:shadow-none print:border-none print:p-0">
        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start border-b border-slate-100 pb-6 gap-4">
            <div>
                <div class="flex items-center space-x-2 font-bold text-2xl text-indigo-700">
                    <i class="fa-solid fa-boxes-packing"></i>
                    <span>INVOICE</span>
                </div>
                <p class="text-sm text-slate-500 mt-1">Reference: <span class="font-mono font-semibold text-slate-800">{{ $order->order_number }}</span></p>
            </div>

            <div class="text-right sm:text-right space-y-2">
                <!-- Status Badges -->
                <div class="flex items-center space-x-2 justify-end">
                    <!-- Status Badge -->
                    @if($order->status === 'completed')
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">Completed</span>
                    @elseif($order->status === 'processing')
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200 uppercase tracking-wider">Processing</span>
                    @elseif($order->status === 'pending')
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200 uppercase tracking-wider">Pending</span>
                    @else
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200 uppercase tracking-wider">Cancelled</span>
                    @endif

                    <!-- Payment Badge -->
                    @if($order->payment_status === 'paid')
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">Paid</span>
                    @elseif($order->payment_status === 'unpaid')
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">Unpaid</span>
                    @else
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200 uppercase tracking-wider">Refunded</span>
                    @endif
                </div>

                <p class="text-xs text-slate-400">Date: {{ $order->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Quick Status Control Panel (hidden on print) -->
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-wrap items-center justify-between gap-4 print:hidden">
            <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-sliders text-indigo-600"></i> Quick Update Status:
            </span>
            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex flex-wrap items-center gap-3">
                @csrf
                @method('PATCH')

                <div class="flex items-center space-x-1 text-xs">
                    <label class="text-slate-500 font-medium">Status:</label>
                    <select name="status" class="py-1 px-2 rounded border border-slate-300 text-xs bg-white focus:ring-1 focus:ring-indigo-500">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="flex items-center space-x-1 text-xs">
                    <label class="text-slate-500 font-medium">Payment:</label>
                    <select name="payment_status" class="py-1 px-2 rounded border border-slate-300 text-xs bg-white focus:ring-1 focus:ring-indigo-500">
                        <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition">
                    Update Status
                </button>
            </form>
        </div>

        <!-- Customer & Order Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50/50 p-6 rounded-xl border border-slate-200">
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Billed To (Customer)</h3>
                <p class="font-bold text-slate-900 text-base">{{ $order->customer->name ?? 'N/A' }}</p>
                <p class="text-sm text-slate-600 mt-1"><i class="fa-solid fa-envelope text-slate-400 mr-1.5"></i> {{ $order->customer->email ?? 'N/A' }}</p>
                @if($order->customer->phone)
                    <p class="text-sm text-slate-600 mt-0.5"><i class="fa-solid fa-phone text-slate-400 mr-1.5"></i> {{ $order->customer->phone }}</p>
                @endif
            </div>

            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Shipping Address</h3>
                <p class="text-sm text-slate-700">{{ $order->customer->address ?? 'No address recorded' }}</p>
                @if($order->customer->city)
                    <p class="text-sm text-slate-700 font-medium mt-0.5">{{ $order->customer->city }}</p>
                @endif
            </div>
        </div>

        <!-- Order Items Table -->
        <div class="space-y-3">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Itemized Breakdown</h3>
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider border-b border-slate-200">
                            <th class="py-3 px-4 font-semibold">#</th>
                            <th class="py-3 px-4 font-semibold">Product Name</th>
                            <th class="py-3 px-4 font-semibold text-center">SKU</th>
                            <th class="py-3 px-4 font-semibold text-center">Qty</th>
                            <th class="py-3 px-4 font-semibold text-right">Unit Price</th>
                            <th class="py-3 px-4 font-semibold text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($order->items as $index => $item)
                            <tr>
                                <td class="py-3 px-4 text-slate-400 text-xs">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-medium text-slate-900">
                                    {{ $item->product_name }}
                                </td>
                                <td class="py-3 px-4 text-center text-xs font-mono text-slate-500">
                                    {{ $item->product->sku ?? 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-center font-semibold text-slate-800">
                                    {{ $item->quantity }}
                                </td>
                                <td class="py-3 px-4 text-right text-slate-700">
                                    ${{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-slate-900">
                                    ${{ number_format($item->total_price, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Calculation Totals Box -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 border-t border-slate-100 pt-6">
            <div class="sm:max-w-md w-full">
                @if($order->notes)
                    <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200/60 text-xs text-amber-900 space-y-1">
                        <span class="font-bold flex items-center gap-1">
                            <i class="fa-solid fa-sticky-note text-amber-600"></i> Order Notes:
                        </span>
                        <p class="text-slate-700">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="w-full sm:w-72 space-y-2 text-sm bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-semibold text-slate-900">${{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Tax:</span>
                    <span class="font-semibold text-slate-900">${{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Shipping Fee:</span>
                    <span class="font-semibold text-slate-900">${{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                <div class="border-t border-slate-300 pt-2 flex justify-between text-base font-bold text-slate-900">
                    <span>Grand Total:</span>
                    <span class="text-indigo-600">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
