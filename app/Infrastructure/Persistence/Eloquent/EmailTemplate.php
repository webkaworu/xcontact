<?php

namespace App\Infrastructure\Persistence\Eloquent;

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

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function notificationForms(): HasMany
    {
        return $this->hasMany(Form::class, 'notification_template_id');
    }

    public function autoReplyForms(): HasMany
    {
        return $this->hasMany(Form::class, 'auto_reply_template_id');
    }
}
