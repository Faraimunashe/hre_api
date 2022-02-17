<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\clerk\PaymentsController;
use App\Http\Controllers\clerk\AccountsController;
use App\Http\Controllers\clerk\TransactionsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/clerk/payments', [PaymentsController::class, 'index'])->name('payments');
    Route::get('/clerk/transactions', [TransactionsController::class, 'index'])->name('transactions');
    Route::get('/clerk/accounts', [AccountsController::class, 'index'])->name('accounts');
    Route::get('/clerk/new/account', [AccountsController::class, 'newA'])->name('new-account');

    Route::post('/clerk/add/account', [AccountsController::class, 'addnew'])->name('add-account');
    Route::post('/clerk/payment', [PaymentsController::class, 'payment'])->name('payment');

    Route::post('/clerk/payment/status', [PaymentsController::class, 'status'])->name('payment-status');
});

require __DIR__ . '/auth.php';
