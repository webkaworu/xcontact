<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    protected function casts(): array
    {
        return [
            'auto_reply_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notificationTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'notification_template_id');
    }

    public function autoReplyTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'auto_reply_template_id');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function accessTokens(): HasMany
    {
        return $this->hasMany(AccessToken::class);
    }
}
