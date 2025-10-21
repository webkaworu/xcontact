<?php

namespace App\Http\Controllers;

use App\Models\RegistrationToken;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required|string',
        ]);

        $token = RegistrationToken::where('token', $request->token)->first();

        if (!$token) {
            throw ValidationException::withMessages([
                'token' => ['Invalid registration token.'],
            ]);
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'token' => ['Registration token has expired.'],
            ]);
        }

        if ($token->email && $token->email !== $request->email) {
            throw ValidationException::withMessages([
                'email' => ['This email is not allowed to use this registration token.'],
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'registration_token_id' => $token->id,
        ]);

        // Assign default 'user' role
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $user->roles()->attach($userRole);
        }

        return response()->json($user, 201);
    }

    /**
     * Authenticate a user and return a token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    /**
     * Log out the user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}