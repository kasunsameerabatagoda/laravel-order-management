@extends('layouts.app')

@section('title', 'Create Order - Order Management System')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="orderForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Create New Order</h1>
            <p class="text-slate-500 mt-1">Select a customer, add items, and specify order totals.</p>
        </div>
        <a href="{{ route('orders.index') }}" class="text-slate-600 hover:text-slate-900 px-3 py-2 rounded-lg border border-slate-300 text-sm font-medium transition flex items-center space-x-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Orders</span>
        </a>
    </div>

    <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Customer & Line Items -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Selection -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-user-check text-indigo-600"></i>
                        <span>1. Customer Details</span>
                    </h2>

                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-slate-700 mb-1">Select Customer <span class="text-rose-500">*</span></label>
                        <select name="customer_id" id="customer_id" x-model="customerId" required class="w-full py-2.5 px-3 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Choose a customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-boxes-packing text-indigo-600"></i>
                            <span>2. Order Line Items</span>
                        </h2>
                        <button type="button" @click="addItem()" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center space-x-1.5">
                            <i class="fa-solid fa-plus"></i>
                            <span>Add Product</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-4 rounded-lg border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                <!-- Product Select -->
                                <div class="flex-grow w-full sm:w-auto">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Product</label>
                                    <select :name="`items[${index}][product_id]`" x-model="item.product_id" @change="updateItemPrice(index)" required class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 bg-white focus:ring-2 focus:ring-indigo-500">
                                        <option value="">-- Select Product --</option>
                                        <template x-for="p in products" :key="p.id">
                                            <option :value="p.id" x-text="`${p.name} ($${parseFloat(p.price).toFixed(2)}) - Stock: ${p.stock_quantity}`"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Quantity -->
                                <div class="w-28">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Qty</label>
                                    <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" min="1" required class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 bg-white text-center focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <!-- Unit Price display -->
                                <div class="w-28">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Unit Price</label>
                                    <div class="py-2 px-3 text-sm font-semibold text-slate-700 text-right bg-slate-100 rounded-lg border border-slate-200" x-text="`$${parseFloat(item.unit_price || 0).toFixed(2)}`"></div>
                                </div>

                                <!-- Line Total display -->
                                <div class="w-32">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Total</label>
                                    <div class="py-2 px-3 text-sm font-bold text-indigo-700 text-right bg-indigo-50 rounded-lg border border-indigo-100" x-text="`$${(item.quantity * (item.unit_price || 0)).toFixed(2)}`"></div>
                                </div>

                                <!-- Delete button -->
                                <div class="mt-4 sm:mt-5">
                                    <button type="button" @click="removeItem(index)" class="p-2 text-slate-400 hover:text-rose-600 rounded transition" title="Remove Item" x-show="items.length > 1">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-2">
                    <label for="notes" class="block text-sm font-medium text-slate-700">Order Notes / Instructions</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Enter optional notes for shipping or fulfillment..." class="w-full p-3 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Right 1 Col: Summary & Settings -->
            <div class="space-y-6">
                <!-- Status & Payment -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-indigo-600"></i>
                        <span>3. Order Status</span>
                    </h2>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Fulfillment Status</label>
                        <select name="status" id="status" required class="w-full py-2.5 px-3 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ old('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-slate-700 mb-1">Payment Status</label>
                        <select name="payment_status" id="payment_status" required class="w-full py-2.5 px-3 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="unpaid" {{ old('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="refunded" {{ old('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                </div>

                <!-- Price Breakdown Card -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-indigo-600"></i>
                        <span>4. Financial Summary</span>
                    </h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal:</span>
                            <span class="font-semibold text-slate-900" x-text="`$${subtotal.toFixed(2)}`"></span>
                        </div>

                        <div>
                            <label for="tax" class="block text-xs font-medium text-slate-500 mb-1">Tax Amount ($)</label>
                            <input type="number" step="0.01" min="0" name="tax" id="tax" x-model.number="tax" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 text-right focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="shipping_fee" class="block text-xs font-medium text-slate-500 mb-1">Shipping Fee ($)</label>
                            <input type="number" step="0.01" min="0" name="shipping_fee" id="shipping_fee" x-model.number="shippingFee" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 text-right focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div class="border-t border-slate-200 pt-3 flex justify-between items-center text-lg font-bold text-slate-900">
                            <span>Total Amount:</span>
                            <span class="text-indigo-600" x-text="`$${totalAmount.toFixed(2)}`"></span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Save & Create Order</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function orderForm() {
        return {
            customerId: '{{ old("customer_id") }}',
            products: @json($products),
            items: [
                { product_id: '', quantity: 1, unit_price: 0 }
            ],
            tax: {{ old('tax', 0) }},
            shippingFee: {{ old('shipping_fee', 0) }},

            addItem() {
                this.items.push({ product_id: '', quantity: 1, unit_price: 0 });
            },

            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },

            updateItemPrice(index) {
                const selectedId = this.items[index].product_id;
                const prod = this.products.find(p => p.id == selectedId);
                if (prod) {
                    this.items[index].unit_price = parseFloat(prod.price);
                } else {
                    this.items[index].unit_price = 0;
                }
            },

            get subtotal() {
                return this.items.reduce((sum, item) => {
                    const q = parseFloat(item.quantity) || 0;
                    const p = parseFloat(item.unit_price) || 0;
                    return sum + (q * p);
                }, 0);
            },

            get totalAmount() {
                const taxVal = parseFloat(this.tax) || 0;
                const shipVal = parseFloat(this.shippingFee) || 0;
                return this.subtotal + taxVal + shipVal;
            }
        }
    }
</script>
@endpush
