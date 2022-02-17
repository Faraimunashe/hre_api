<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\LoginController;
use App\Http\Controllers\api\v1\UserController;
use App\Http\Controllers\api\v1\PaynowController;
use App\Http\Controllers\api\v1\TransactionController;

//users
Route::prefix('/user')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/check', [LoginController::class, 'check_account']);
    Route::post('/signup', [LoginController::class, 'signup']);
    Route::post('/payment/result', [PaynowController::class, 'resultUrl'])->name('payment-results');

    Route::middleware('auth:api')->get('/logs', [TransactionController::class, 'logs']);

    //Route::post('/payment', [PaynowController::class, 'payBill']);
    Route::middleware('auth:api')->get('/all', [UserController::class, 'index']);
    Route::middleware('auth:api')->post('/payment', [PaynowController::class, 'payBill']);
});
