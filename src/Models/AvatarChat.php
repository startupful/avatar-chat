<?php

namespace Startupful\AvatarChat\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvatarChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'avatar_id', 'user_id', 'message', 'is_from_avatar'
    ];

    protected $casts = [
        'is_from_avatar' => 'boolean',
    ];

    public function avatar(): BelongsTo
    {
        return $this->belongsTo(Avatar::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}