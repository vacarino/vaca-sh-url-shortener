<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InviteCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'created_by',
        'used_by',
        'used_at',
        'is_active',
        'is_single_use',
        'description',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'is_active' => 'boolean',
        'is_single_use' => 'boolean',
    ];

    /**
     * Get the user who created this invite code.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who used this invite code.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    /**
     * Check if the invite code can be used.
     */
    public function canBeUsed(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->is_single_use && $this->used_by !== null) {
            return false;
        }

        return true;
    }

    /**
     * Mark the invite code as used by a user.
     */
    public function markAsUsed(User $user): void
    {
        $this->update([
            'used_by' => $user->id,
            'used_at' => now(),
        ]);
    }

    /**
     * Generate a unique invite code.
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Scope for active invite codes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for unused invite codes.
     */
    public function scopeUnused($query)
    {
        return $query->whereNull('used_by');
    }
} 