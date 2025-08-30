<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PaymentController;

//user management
Route::prefix('v1/user')->middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

//company management
Route::prefix('v1/company')->middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/', [CompanyController::class, 'index']);
    Route::post('/', [CompanyController::class, 'store']);
    Route::get('/{id}', [CompanyController::class, 'show']);
    Route::put('/{id}', [CompanyController::class, 'update']);
    Route::delete('/{id}', [CompanyController::class, 'destroy']);
});


//job management
Route::prefix('v1/jobs')->middleware(['auth:api', 'role:admin,recruiter'])->group(function () {
    Route::get('/', [JobController::class, 'index']);
    Route::post('/', [JobController::class, 'store']);
    Route::get('/{id}', [JobController::class, 'show']);
    Route::put('/{id}', [JobController::class, 'update']);
    Route::delete('/{id}', [JobController::class, 'destroy']);
});


//all jobs
Route::prefix('v1/jobs')->middleware('auth:api')->group(function () {
    Route::get('/', [JobController::class, 'index']);
    Route::post('/payment', [PaymentController::class, 'createCheckout']);
});

Route::post('/stripe/webhook', [PaymentController::class, 'handle']);
Route::get('/payment/success', [PaymentController::class, 'success'])->name('api.payment.success');



Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'store']);
