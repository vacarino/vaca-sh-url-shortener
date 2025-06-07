<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'original_url',
        'short_code',
        'clicks',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'clicks' => 'integer',
    ];

    /**
     * Get the user that owns the short URL.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the click logs for the short URL.
     */
    public function clickLogs()
    {
        return $this->hasMany(ClickLog::class);
    }

    /**
     * Generate a unique short code.
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = Str::random(6);
        } while (self::where('short_code', $code)->exists());

        return $code;
    }

    /**
     * Get the full short URL.
     */
    public function getShortUrlAttribute(): string
    {
        return url("/{$this->short_code}");
    }

    /**
     * Get the full short URL (method version).
     */
    public function getShortUrl(): string
    {
        return url("/{$this->short_code}");
    }

    /**
     * Check if the URL has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Increment the click count.
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks');
    }

    /**
     * Scope to get URLs for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get non-expired URLs.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
} 