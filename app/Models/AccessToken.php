<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessToken extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'token',
        'form_id',
        'expires_at',
    ];

    /**
     * Get the form that owns the access token.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}