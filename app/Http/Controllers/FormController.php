<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->hasPermissionTo('forms.manage')) {
            return response()->json(['data' => Form::with(['user', 'notificationTemplate', 'autoReplyTemplate'])->get()]);
        }

        return response()->json(['data' => auth()->user()->forms()->with(['notificationTemplate', 'autoReplyTemplate'])->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Form::class);

        $user = auth()->user();

        // Check form creation limit
        if ($user->form_creation_limit !== null && $user->forms()->count() >= $user->form_creation_limit) {
            return response()->json(['message' => 'Form creation limit reached.'], 403);
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'recipient_email' => ['required', 'email', 'max:255'],
            'notification_template_id' => ['nullable', 'exists:email_templates,id'],
            'auto_reply_enabled' => ['boolean'],
            'auto_reply_template_id' => ['nullable', 'exists:email_templates,id'],
            'daily_limit' => ['nullable', 'integer', 'min:0'],
            'monthly_limit' => ['nullable', 'integer', 'min:0'],
        ]);

        // Set default templates if not provided
        if (!isset($validatedData['notification_template_id'])) {
            $defaultNotificationTemplate = EmailTemplate::where('is_default', true)->where('type', 'notification')->first();
            if ($defaultNotificationTemplate) {
                $validatedData['notification_template_id'] = $defaultNotificationTemplate->id;
            }
        }

        if (!isset($validatedData['auto_reply_template_id']) && isset($validatedData['auto_reply_enabled']) && $validatedData['auto_reply_enabled']) {
            $defaultAutoReplyTemplate = EmailTemplate::where('is_default', true)->where('type', 'auto_reply')->first();
            if ($defaultAutoReplyTemplate) {
                $validatedData['auto_reply_template_id'] = $defaultAutoReplyTemplate->id;
            }
        }

        $form = $user->forms()->create($validatedData);

        return response()->json(['data' => $form], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form)
    {
        $this->authorize('view', $form);

        return response()->json(['data' => $form->load(['user', 'notificationTemplate', 'autoReplyTemplate'])]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form)
    {
        $this->authorize('update', $form);

        $validatedData = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'recipient_email' => ['sometimes', 'email', 'max:255'],
            'notification_template_id' => ['sometimes', 'nullable', 'exists:email_templates,id'],
            'auto_reply_enabled' => ['sometimes', 'boolean'],
            'auto_reply_template_id' => ['sometimes', 'nullable', 'exists:email_templates,id'],
            'daily_limit' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'monthly_limit' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ]);

        $form->update($validatedData);

        return response()->json(['data' => $form]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form)
    {
        $this->authorize('delete', $form);

        $form->delete();

        return response()->json(null, 204);
    }
}
