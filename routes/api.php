<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes Controllers
// Auth
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'store']);
    Route::post('/login', [AuthController::class, 'login']);
});
