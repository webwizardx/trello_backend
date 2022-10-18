<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\User\UserResource;

// Routes Controllers
// V1
// Auth
use App\Http\Controllers\v1\Auth\AuthController as AuthControllerV1;
// Workspace
use App\Http\Controllers\v1\Workspace\WorkspaceController as WorkspaceControllerV1;
// Board
use App\Http\Controllers\v1\Board\BoardController as BoardControllerV1;
// Lists
use App\Http\Controllers\v1\Lists\ListsController as ListsControllerV1;
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

Route::prefix('/v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return new UserResource($request->user());
        });

        Route::prefix('/workspaces')->group(function () {
            Route::get('/', [WorkspaceControllerV1::class, 'index']);
            Route::get('/{id}', [WorkspaceControllerV1::class, 'show']);
            Route::post('/', [WorkspaceControllerV1::class, 'store']);
            Route::patch('/{id}', [WorkspaceControllerV1::class, 'update']);
            Route::delete('/{id}', [WorkspaceControllerV1::class, 'destroy']);
        });
        Route::prefix('/boards')->group(function () {
            Route::get('/', [BoardControllerV1::class, 'index']);
            Route::get('/{id}', [BoardControllerV1::class, 'show']);
            Route::post('/', [BoardControllerV1::class, 'store']);
            Route::patch('/{id}', [BoardControllerV1::class, 'update']);
            Route::delete('/{id}', [BoardControllerV1::class, 'destroy']);

            // Lists
            Route::get('/{id}/lists', [ListsControllerV1::class, 'index']);
        });

        Route::prefix('/lists')->group(function () {
            Route::get('/{id}', [ListsControllerV1::class, 'show']);
            Route::post('/', [ListsControllerV1::class, 'store']);
            Route::patch('/{id}', [ListsControllerV1::class, 'update']);
            Route::delete('/{id}', [ListsControllerV1::class, 'destroy']);
        });
    });

    Route::prefix('/auth')->middleware('guest')->group(function () {
        Route::post('/signup', [AuthControllerV1::class, 'store']);
        Route::post('/login', [AuthControllerV1::class, 'login']);
    });
});
