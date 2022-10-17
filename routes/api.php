<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\User\UserResource;

// Routes Controllers
// Auth
use App\Http\Controllers\Auth\AuthController;
// Workspace
use App\Http\Controllers\Workspace\WorkspaceController;
//Board
use App\Http\Controllers\Board\BoardController;

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
        return new UserResource($request->user());
    });

    Route::prefix('/workspaces')->group(function () {
        Route::get('/', [WorkspaceController::class, 'index']);
        Route::get('/{id}', [WorkspaceController::class, 'show']);
        Route::post('/', [WorkspaceController::class, 'store']);
        Route::patch('/{id}', [WorkspaceController::class, 'update']);
        Route::delete('/{id}', [WorkspaceController::class, 'destroy']);
    });
    Route::prefix('/boards')->group(function () {
        Route::get('/', [BoardController::class, 'index']);
        Route::get('/{id}', [BoardController::class, 'show']);
        Route::post('/', [BoardController::class, 'store']);
        Route::patch('/{id}', [BoardController::class, 'update']);
        Route::delete('/{id}', [BoardController::class, 'destroy']);
    });
});

Route::prefix('/auth')->middleware('guest')->group(function () {
    Route::post('/signup', [AuthController::class, 'store']);
    Route::post('/login', [AuthController::class, 'login']);
});
