<?php

namespace App\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Infrastructure\Persistence\Eloquent\RegistrationToken;

class RegistrationTokenController extends Controller
{
    public function index()
    {
        $this->authorize('users.manage');

        return response()->json([
            'data' => RegistrationToken::all(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('users.manage');

        $request->validate([
            'email' => ['nullable', 'email'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $registrationToken = RegistrationToken::create([
            'token' => Str::random(32),
            'email' => $request->email,
            'expires_at' => $request->expires_at,
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'data' => $registrationToken,
        ], 201);
    }

    public function destroy(string $id)
    {
        $this->authorize('users.manage');

        $registrationToken = RegistrationToken::findOrFail($id);
        $registrationToken->delete();

        return response()->json(null, 204);
    }
}
