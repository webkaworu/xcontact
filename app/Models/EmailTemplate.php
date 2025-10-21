<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'subject',
        'body',
        'is_default',
    ];

    /**
     * Get the forms that use this email template as notification template.
     */
    public function notificationForms(): HasMany
    {
        return $this->hasMany(Form::class, 'notification_template_id');
    }

    /**
     * Get the forms that use this email template as auto reply template.
     */
    public function autoReplyForms(): HasMany
    {
        return $this->hasMany(Form::class, 'auto_reply_template_id');
    }
}