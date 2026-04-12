<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ExportController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');

Route::middleware([EnsureTokenIsValid::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::get('/users', [UserController::class, 'index'])->name('api.users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('api.users.show');
    Route::post('/users', [UserController::class, 'store'])->name('api.users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('api.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('api.users.destroy');

    Route::get('/states', [LocationController::class, 'states'])->name('api.states');
    Route::get('/states/{state}/cities', [LocationController::class, 'cities'])->name('api.cities');

    Route::middleware(['admin'])->group(function () {
        Route::apiResource('roles', RoleController::class)->names('api.roles');
        Route::apiResource('permissions', PermissionController::class)->names('api.permissions');
    });

    Route::apiResource('suppliers', SupplierController::class)->names('api.suppliers');
    Route::apiResource('customers', CustomerController::class)->names('api.customers');

    // Export Routes
    Route::get('/export/{module}/{format}', [ExportController::class, 'export'])->name('api.export');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
