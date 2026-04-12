<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/users', function () {
    return view('users.index');
})->name('users.index');

Route::get('/roles', function () {
    return view('roles.index');
})->name('roles.index');

Route::get('/permissions', function () {
    return view('permissions.index');
})->name('permissions.index');

Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
