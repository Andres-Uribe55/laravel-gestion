<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/products', \App\Livewire\ProductManager::class)->name('products');
    Route::get('/users', \App\Livewire\UserManager::class)->name('users');
    Route::get('/audit-log', \App\Livewire\AuditLog::class)->name('audit-log');
});
