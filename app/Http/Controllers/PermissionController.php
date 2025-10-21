<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('roles.manage')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(Permission::all());
    }
}