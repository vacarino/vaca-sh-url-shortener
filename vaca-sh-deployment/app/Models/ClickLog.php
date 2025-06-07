<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'short_url_id',
        'ip_address',
        'user_agent',
        'country',
        'browser',
        'platform',
    ];

    /**
     * Get the short URL that owns the click log.
     */
    public function shortUrl()
    {
        return $this->belongsTo(ShortUrl::class);
    }

    /**
     * Parse user agent and extract browser information.
     */
    public static function parseUserAgent(string $userAgent): array
    {
        $browser = 'Unknown';
        $platform = 'Unknown';

        // Basic browser detection
        if (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent) && !preg_match('/Edge/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Opera/i', $userAgent)) {
            $browser = 'Opera';
        }

        // Basic platform detection
        if (preg_match('/Windows/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            $platform = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iOS/i', $userAgent)) {
            $platform = 'iOS';
        }

        return [
            'browser' => $browser,
            'platform' => $platform,
        ];
    }

    /**
     * Scope to get logs for a specific short URL.
     */
    public function scopeForShortUrl($query, $shortUrlId)
    {
        return $query->where('short_url_id', $shortUrlId);
    }

    /**
     * Scope to get recent logs.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
} 