<?php

namespace App\Interfaces\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Infrastructure\Persistence\Eloquent\User;
use App\Infrastructure\Persistence\Eloquent\RegistrationToken;
use App\Infrastructure\Persistence\Eloquent\Plan;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'registration_token' => ['required', 'string', 'exists:registration_tokens,token'],
        ]);

        $registrationToken = RegistrationToken::where('token', $request->registration_token)->first();

        if (! $registrationToken) {
            throw ValidationException::withMessages([
                'registration_token' => ['登録トークンが無効です。'],
            ]);
        }

        if ($registrationToken->expires_at && $registrationToken->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'registration_token' => ['登録トークンの有効期限が切れています。'],
            ]);
        }

        if ($registrationToken->email && $registrationToken->email !== $request->email) {
            throw ValidationException::withMessages([
                'registration_token' => ['この登録トークンは指定されたメールアドレスでのみ使用可能です。'],
            ]);
        }

        $defaultPlan = Plan::where('name', '無料プラン')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'registration_token_id' => $registrationToken->id,
            'plan_id' => $defaultPlan ? $defaultPlan->id : null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['認証情報が正しくありません。'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'ログアウトしました。',
        ]);
    }
}
