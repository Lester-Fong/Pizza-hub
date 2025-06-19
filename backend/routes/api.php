<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailsController;
use App\Http\Controllers\PizzaTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PizzaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Authentication Routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']); // Changed to POST for security
    Route::get('/import-status/{jobId}', [OrderController::class, 'importStatus']); // Added status endpoint

    // Main Functionality Routes
    Route::post('/import-pizza', [PizzaController::class, 'importPizza']);
    Route::post('/import-pizza-types', [PizzaTypeController::class, 'importPizzaTypes']);
    Route::post('/import-orders', [OrderController::class, 'importOrders']);
    Route::post('/import-order-details', [OrderDetailsController::class, 'importOrderDetails']);


    Route::get('/pizza', [PizzaController::class, 'showPizza']);
    Route::get('/pizza-types', [PizzaTypeController::class, 'showPizzaTypes']);
    Route::get('/orders', [OrderController::class, 'showOrders']);
    Route::get('/order-details', [OrderDetailsController::class, 'showOrderDetails']);

    Route::get('/sales-summary', [DashboardController::class, 'salesSummary']);
    Route::get('/daily-sales-trend', [DashboardController::class, 'dailySalesTrend']);
});
