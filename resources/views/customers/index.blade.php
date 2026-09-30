@extends('layouts.app')

@section('title', 'Customers - Order Management System')

@section('content')
<div class="space-y-6" x-data="{ showModal: false, isEdit: false, formUrl: '{{ route('customers.store') }}', customer: { id: '', name: '', email: '', phone: '', city: '', address: '' } }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Customer Management</h1>
            <p class="text-slate-500 mt-1">View, search, create, and edit customer accounts.</p>
        </div>
        <div>
            <button @click="isEdit = false; customer = { id: '', name: '', email: '', phone: '', city: '', address: '' }; formUrl = '{{ route('customers.store') }}'; showModal = true;" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2.5 rounded-lg shadow-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Add Customer</span>
            </button>
        </div>
    </div>

    <!-- Search Card -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('customers.index') }}" class="flex items-center space-x-3">
            <div class="relative flex-grow">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone, city..." class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white py-2 px-4 rounded-lg text-sm font-medium transition flex items-center space-x-2">
                <i class="fa-solid fa-filter"></i>
                <span>Search</span>
            </button>
            @if(request('search'))
                <a href="{{ route('customers.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 py-2 px-3 rounded-lg text-sm transition" title="Reset Search">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider border-b border-slate-200">
                        <th class="py-3.5 px-6 font-semibold">Name</th>
                        <th class="py-3.5 px-6 font-semibold">Email</th>
                        <th class="py-3.5 px-6 font-semibold">Phone</th>
                        <th class="py-3.5 px-6 font-semibold">City</th>
                        <th class="py-3.5 px-6 font-semibold text-center">Orders Count</th>
                        <th class="py-3.5 px-6 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($customers as $c)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 font-semibold text-slate-900">
                                {{ $c->name }}
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ $c->email }}
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ $c->phone ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ $c->city ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $c->orders_count }} orders
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <button @click="isEdit = true; customer = @js($c); formUrl = '{{ route('customers.update', $c) }}'; showModal = true;" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded transition" title="Edit Customer">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('customers.destroy', $c) }}" method="POST" onsubmit="return confirm('Delete customer {{ $c->name }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded transition" title="Delete Customer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
        <div @click.away="showModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900" x-text="isEdit ? 'Edit Customer' : 'Add New Customer'"></h3>
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
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Full Name *</label>
                    <input type="text" name="name" x-model="customer.name" required class="w-full p-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Email Address *</label>
                    <input type="email" name="email" x-model="customer.email" required class="w-full p-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Phone</label>
                        <input type="text" name="phone" x-model="customer.phone" class="w-full p-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">City</label>
                        <input type="text" name="city" x-model="customer.city" class="w-full p-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Street Address</label>
                    <textarea name="address" x-model="customer.address" rows="2" class="w-full p-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
