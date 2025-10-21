<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    protected $fillable = [
        'form_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the form that owns the inquiry.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}