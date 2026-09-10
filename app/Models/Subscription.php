<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status', // free_trial, active, expired, cancelled, suspended
        'started_at',
        'expires_at',
        'auto_renew',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'auto_renew' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class, 'subscription_id');
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'free_trial']) && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->expires_at->isPast();
    }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        return max(0, (int) now()->diffInDays($this->expires_at, false));
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        if ($this->isExpired()) {
            return 'bg-rose-100 text-rose-800 border-rose-200';
        }

        if ($this->days_remaining <= 3) {
            return 'bg-amber-100 text-amber-800 border-amber-200';
        }

        return match ($this->status) {
            'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'free_trial' => 'bg-teal-100 text-teal-800 border-teal-200',
            'cancelled' => 'bg-slate-100 text-slate-700 border-slate-200',
            'suspended' => 'bg-red-100 text-red-800 border-red-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
