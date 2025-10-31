<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    protected $fillable = [
        'user_id',
        'slug',
        'destination_url',
        'password_hash',
        'expires_at',
        'is_active',
        'clicks_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function clicks()
    {
        return $this->hasMany(LinkClick::class);
    }

    public function getShortUrlAttribute(): string
    {
        return config('app.url') . '/r/' . $this->slug;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }
}
