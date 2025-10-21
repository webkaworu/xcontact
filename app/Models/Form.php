<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'recipient_email',
        'notification_template_id',
        'auto_reply_enabled',
        'auto_reply_template_id',
        'daily_limit',
        'monthly_limit',
    ];

    /**
     * Get the user that owns the form.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the inquiries for the form.
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    /**
     * Get the notification email template for the form.
     */
    public function notificationTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'notification_template_id');
    }

    /**
     * Get the auto reply email template for the form.
     */
    public function autoReplyTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'auto_reply_template_id');
    }

    /**
     * Get the access tokens for the form.
     */
    public function accessTokens(): HasMany
    {
        return $this->hasMany(AccessToken::class);
    }
}