@extends('layouts.app')

@section('title', 'Products - Order Management System')

@section('content')
<div class="space-y-6" x-data="{ showModal: false, isEdit: false, formUrl: '{{ route('products.store') }}', product: { id: '', name: '', sku: '', price: '', stock_quantity: 0, description: '' } }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Product Inventory</h1>
            <p class="text-slate-500 mt-1">Manage catalog items, prices, and stock inventory.</p>
        </div>
        <div>
            <button @click="isEdit = false; product = { id: '', name: '', sku: '', price: '', stock_quantity: 0, description: '' }; formUrl = '{{ route('products.store') }}'; showModal = true;" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2.5 rounded-lg shadow-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-box-open"></i>
                <span>Add Product</span>
            </button>
        </div>
    </div>

    <!-- Search Card -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('products.index') }}" class="flex items-center space-x-3">
            <div class="relative flex-grow">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product name or SKU..." class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white py-2 px-4 rounded-lg text-sm font-medium transition flex items-center space-x-2">
                <i class="fa-solid fa-filter"></i>
                <span>Search</span>
            </button>
            @if(request('search'))
                <a href="{{ route('products.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 py-2 px-3 rounded-lg text-sm transition" title="Reset Search">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider border-b border-slate-200">
                        <th class="py-3.5 px-6 font-semibold">SKU</th>
                        <th class="py-3.5 px-6 font-semibold">Product Name</th>
                        <th class="py-3.5 px-6 font-semibold text-right">Price</th>
                        <th class="py-3.5 px-6 font-semibold text-center">Stock Level</th>
                        <th class="py-3.5 px-6 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 text-xs font-mono font-bold text-indigo-600">
                                {{ $p->sku }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-900">{{ $p->name }}</div>
                                @if($p->description)
                                    <div class="text-xs text-slate-400 truncate max-w-xs">{{ $p->description }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right font-bold text-slate-900">
                                ${{ number_format($p->price, 2) }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($p->stock_quantity <= 0)
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200">Out of Stock</span>
                                @elseif($p->stock_quantity <= 5)
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">Low Stock ({{ $p->stock_quantity }})</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">{{ $p->stock_quantity }} units</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <button @click="isEdit = true; product = @js($p); formUrl = '{{ route('products.update', $p) }}'; showModal = true;" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded transition" title="Edit Product">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('products.destroy', $p) }}" method="POST" onsubmit="return confirm('Delete product {{ $p->name }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded transition" title="Delete Product">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                No products found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
        <div @click.away="showModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900" x-text="isEdit ? 'Edit Product' : 'Add New Product'"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form :action="formUrl" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Product Name *</label>
                    <input type="text" name="name" x-model="product.name" required class="w-full p-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">SKU Code *</label>
                    <input type="text" name="sku" x-model="product.sku" required class="w-full p-2.5 text-sm rounded-lg border border-slate-300 font-mono text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Price ($) *</label>
                        <input type="number" step="0.01" min="0" name="price" x-model="product.price" required class="w-full p-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Stock Qty *</label>
                        <input type="number" min="0" name="stock_quantity" x-model="product.stock_quantity" required class="w-full p-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Description</label>
                    <textarea name="description" x-model="product.description" rows="2" class="w-full p-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
