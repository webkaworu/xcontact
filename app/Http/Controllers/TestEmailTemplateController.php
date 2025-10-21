<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestEmailTemplateController extends Controller
{
    public function index()
    {
        try {
            return response()->json(['message' => 'Test successful']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }
}
