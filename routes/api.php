<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AddressController;

// Import our AuthController
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (No authentication required)
|--------------------------------------------------------------------------
| Anyone can access these routes without a token
| These are used for login and registration
*/

// POST /api/register
// When someone sends a POST request to /api/register,
// Laravel will call the 'register' method in AuthController
Route::post('/register', [AuthController::class, 'register']);

// POST /api/login
// When someone sends a POST request to /api/login,
// Laravel will call the 'login' method in AuthController
Route::post('/login', [AuthController::class, 'login']);



/*
|--------------------------------------------------------------------------
| PUBLIC PRODUCT ROUTES
|--------------------------------------------------------------------------
*/
// PRODUCT ROUTES
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// CATEGORY ROUTES
Route::get('/categories', [CategoryController::class, 'index']);


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (Authentication required)
|--------------------------------------------------------------------------
| These routes require a valid Sanctum token
| The 'auth:sanctum' middleware checks for the token
| If no valid token is provided, it returns 401 Unauthorized
|
| Route::middleware('auth:sanctum') means:
| "Before running these routes, check if the user is authenticated"
*/
// PROTECTED (ADMIN ONLY)
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // PRODUCT ROUTES
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Get next product_code
    Route::get('next-product_code', [ProductController::class, 'getNextproduct_code']);

    // CATEGORY ROUTES
    Route::post('/categories', [CategoryController::class, 'store']);

    // CART ROUTES
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::post('/cart/update', [CartController::class, 'update']);
    Route::post('/cart/remove', [CartController::class, 'remove']);

    // ORDER ROUTES
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

        Route::get('/address', [AddressController::class, 'index']);
    Route::post('/address', [AddressController::class, 'store']);
    Route::put('/address/{id}', [AddressController::class, 'update']);
    Route::delete('/address/{id}', [AddressController::class, 'destroy']);

});
