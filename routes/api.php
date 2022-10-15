<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes Controllers
// Auth
use App\Http\Controllers\Auth\AuthController;

// Workspace
use App\Http\Controllers\Workspace\WorkspaceController;


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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('/workspaces')->group(function () {
        Route::get('/', [WorkspaceController::class, 'index']);
        Route::get('/{workspace}', [WorkspaceController::class, 'show']);
        Route::post('/', [WorkspaceController::class, 'store']);
        Route::patch('/{workspace}', [WorkspaceController::class, 'update']);
        Route::delete('/{workspace}', [WorkspaceController::class, 'destroy']);
    });
});

Route::prefix('/auth')->middleware('guest')->group(function () {
    Route::post('/signup', [AuthController::class, 'store']);
    Route::post('/login', [AuthController::class, 'login']);
});
