<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user and return token
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|alpha_num|max:255',
            'last_name' => 'required|string|alpha_num|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'contact_number' => 'required|string|regex:/^[\d\s+\-\()]+$/|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('KombeeAppToken')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name'),
                    'permissions' => $user->getAllPermissions(),
                ],
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login user and return token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            // Create Passport token
            $token = $user->createToken('KombeeAppToken')->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'User logged in successfully.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'roles' => $user->roles->pluck('name'),
                        'permissions' => $user->getAllPermissions(),
                    ],
                    'token' => $token,
                ],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials.',
        ], 401);
    }

    /**
     * Logout user and revoke token
     */
    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();

        if ($user) {
            $user->token()->revoke();

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully.',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated or already logged out.',
        ], 401);
    }
}
