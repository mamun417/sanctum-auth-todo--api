<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken("auth_token")->plainTextToken;

        return $this->success('User registered successfully.', [
            'data' => [
                'access_token' => $token,
                'type' => 'Bearer',
            ]
        ], HttpStatusCodes::CREATED);
    }

    public function login(LoginRequest $request)
    {
        $user = User::query()->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('The provided credentials are incorrect.', HttpStatusCodes::NOT_FOUND);
        }

        $token = $user->createToken("auth_token")->plainTextToken;

        return $this->success('Login successful.', [
            'data' => [
                'access_token' => $token,
                'type' => 'Bearer',
            ]
        ]);
    }

    public function logout()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error('No authenticated user found.', HttpStatusCodes::UNAUTHORIZED);
        }

        $user->tokens()->delete();

        return $this->success('Logout successful.');
    }
}
