<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return ApiResponder::success([
            'user'  => $user,
            'token' => $token,
        ], 'Registered', 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            // 422 con tu formato global
            return ApiResponder::error('Invalid credentials', 422, null, []);
        }

        // Si usas habilidades: createToken('api', ['posts:read'])
        $token = $user->createToken('api')->plainTextToken;

        return ApiResponder::success([
            'user'  => $user,
            'token' => $token,
        ], 'Authenticated', 200);
    }
    public function logout(Request $r)
    {
        $r->user()->currentAccessToken()->delete();
        return ApiResponder::success(null, 'Logged out', 200);
    }
}
