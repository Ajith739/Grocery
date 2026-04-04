<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import our AuthController
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;

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

    // GET /api/profile
    // Returns the logged-in user's profile data
    Route::get('/profile', [AuthController::class, 'profile']);

    // POST /api/logout
    // Logs the user out by deleting their token
    Route::post('/logout', [AuthController::class, 'logout']);

    // ADMIN-ONLY PRODUCT ROUTES
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| PUBLIC PRODUCT ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
