<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationToken extends Model
{
    protected $fillable = [
        'token',
        'email',
        'expires_at',
        'created_by',
    ];

    /**
     * Get the users that registered with this token.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'registration_token_id');
    }

    /**
     * Get the user who created the registration token.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}