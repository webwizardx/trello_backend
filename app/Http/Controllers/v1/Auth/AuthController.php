<?php

namespace App\Http\Controllers\v1\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\UserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    function store(UserRequest $request)
    {
        $validated = $request->validated();
        $user = User::create($validated);

        return new UserResource($user, 201);
    }

    function login(LoginRequest $request)
    {
        $validated = $request->validated();
        if (!Auth::attempt($validated)) return response()->json(['message' => 'The credentials are not valid'], 401);

        $token = $request->user()->createToken('api');

        return response()->json(['message' => 'Login successfully', 'data' => ['apiToken' => $token->plainTextToken]], 201);
    }

    function logout(Request $request)
    {
        $deleted = $request->user()->currentAccessToken()->delete();

        if (!$deleted) return response()->json(['message' => 'Token not found', 404]);

        return response()->json(['message' => 'Logout successfully'], 200);
    }
}
