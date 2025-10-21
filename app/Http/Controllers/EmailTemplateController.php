<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmailTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('check.permission:templates.manage')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return response()->json(['data' => EmailTemplate::all()]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching email templates', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('templates.manage');

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:email_templates'],
            'type' => ['required', 'string', Rule::in(['notification', 'auto_reply'])],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_default' => ['boolean'],
        ]);

        $emailTemplate = EmailTemplate::create($validatedData);

        return response()->json(['data' => $emailTemplate], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailTemplate $emailTemplate)
    {
        return response()->json(['data' => $emailTemplate]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $this->authorize('templates.manage');

        $validatedData = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('email_templates')->ignore($emailTemplate->id)],
            'type' => ['sometimes', 'string', Rule::in(['notification', 'auto_reply'])],
            'subject' => ['sometimes', 'string', 'max:255'],
            'body' => ['sometimes', 'string'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        $emailTemplate->update($validatedData);

        return response()->json(['data' => $emailTemplate]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        $this->authorize('templates.manage');

        $emailTemplate->delete();

        return response()->json(null, 204);
    }
}