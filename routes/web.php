<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::view('/compras', 'pages.purchases.index')->name('purchases.index');
    Route::view('/compras/nova', 'pages.purchases.create')->name('purchases.create');
    Route::view('/fornecedores', 'pages.suppliers.index')->name('suppliers.index');
    Route::view('/clientes', 'pages.customers.index')->name('customers.index');
    Route::view('/vendas', 'pages.sales.index')->name('sales.index');
    Route::view('/vendas/nova', 'pages.sales.create')->name('sales.create');
    Route::view('/produtos/novo', 'pages.products.create')->name('products.create');
    Route::view('/estoque/produtos', 'pages.products.index')->name('products.index');
    Route::get('/anexos/{attachment}/preview', [AttachmentController::class, 'preview'])->name('attachments.preview');
});

require __DIR__.'/auth.php';
