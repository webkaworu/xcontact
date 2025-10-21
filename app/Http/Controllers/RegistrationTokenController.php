<?php

namespace App\Http\Controllers;

use App\Models\RegistrationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Support\Str;

class RegistrationTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(RegistrationToken::with('creator')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'expires_at' => 'nullable|date',
        ]);

        $token = RegistrationToken::create([
            'token' => Str::random(40),
            'email' => $request->email,
            'expires_at' => $request->expires_at,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($token, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, RegistrationToken $registrationToken)
    {
        $registrationToken->delete();

        return response()->json(null, 204);
    }
}