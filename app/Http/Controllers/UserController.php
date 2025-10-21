<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get the roles for a specific user.
     */
    public function getRoles(Request $request, User $user)
    {
        if (!$request->user()->hasPermissionTo('roles.manage')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($user->roles);
    }

    /**
     * Update the roles for a specific user.
     */
    public function updateRoles(Request $request, User $user)
    {
        if (!$request->user()->hasPermissionTo('roles.manage')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $user->roles()->sync($request->roles);

        return response()->json($user->roles);
    }
}