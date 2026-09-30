<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Dashboard / Overview
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Order Management Routes (CRUD + Status updates)
Route::resource('orders', OrderController::class);
Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

// Customer Management Routes
Route::resource('customers', CustomerController::class)->except(['create', 'edit', 'show']);

// Product Inventory Routes
Route::resource('products', ProductController::class)->except(['create', 'edit', 'show']);
