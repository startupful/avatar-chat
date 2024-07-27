<?php

namespace Filament\AvatarChat\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Avatar extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'is_public', 'profile_image', 'categories', 'first_message',
        'profile_intro', 'hashtags', 'profile_details', 'fine_tuning_data'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'categories' => 'array',
        'first_message' => 'array',
        'hashtags' => 'array',
        'fine_tuning_data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($avatar) {
            $avatar->uuid = Str::uuid();
        });
    }

    public function chats(): HasMany
    {
        return $this->hasMany(AvatarChat::class);
    }

    public function getProfileImageAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return $value;
    }

    public function getProfileImageUrlAttribute()
    {
        if (!$this->profile_image) {
            return null;
        }
        return asset('storage/' . $this->profile_image);
    }
}