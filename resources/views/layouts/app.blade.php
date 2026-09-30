<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Order Management System')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

    <!-- Top Navigation Bar -->
    <nav class="bg-indigo-700 text-white shadow-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 font-bold text-xl tracking-wide hover:text-indigo-200 transition">
                        <i class="fa-solid fa-boxes-packing text-2xl text-amber-300"></i>
                        <span>OrderManager <span class="text-xs bg-indigo-900 text-indigo-200 px-2 py-0.5 rounded-full font-normal">v1.0</span></span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-6 text-sm font-medium">
                    <a href="{{ route('dashboard') }}" class="hover:text-amber-300 transition flex items-center space-x-1.5 {{ request()->routeIs('dashboard') ? 'text-amber-300 font-bold border-b-2 border-amber-300 pb-1' : '' }}">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="hover:text-amber-300 transition flex items-center space-x-1.5 {{ request()->routeIs('orders.*') ? 'text-amber-300 font-bold border-b-2 border-amber-300 pb-1' : '' }}">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Orders</span>
                    </a>
                    <a href="{{ route('customers.index') }}" class="hover:text-amber-300 transition flex items-center space-x-1.5 {{ request()->routeIs('customers.*') ? 'text-amber-300 font-bold border-b-2 border-amber-300 pb-1' : '' }}">
                        <i class="fa-solid fa-users"></i>
                        <span>Customers</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="hover:text-amber-300 transition flex items-center space-x-1.5 {{ request()->routeIs('products.*') ? 'text-amber-300 font-bold border-b-2 border-amber-300 pb-1' : '' }}">
                        <i class="fa-solid fa-box-archive"></i>
                        <span>Products</span>
                    </a>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('orders.create') }}" class="bg-amber-400 hover:bg-amber-500 text-slate-900 font-semibold px-4 py-2 rounded-lg text-sm shadow-sm transition flex items-center space-x-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>New Order</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-xl"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-rose-500 hover:text-rose-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 shadow-sm">
                <div class="flex items-start space-x-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-xl mt-0.5"></i>
                    <div>
                        <span class="font-semibold">Please fix the following validation errors:</span>
                        <ul class="mt-1 list-disc list-inside text-sm text-amber-800">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-sm text-slate-500 mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} Laravel Order Management System. Built with Laravel 13 & Tailwind CSS.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
