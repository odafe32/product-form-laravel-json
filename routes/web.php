<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


//Product view
Route::get('/', [ProductController::class, 'index'])->name('products.index');
// store all product
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
// all products
Route::get('/products/all', [ProductController::class, 'getAll'])->name('products.getAll');
// update products
Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
// delete  products
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');