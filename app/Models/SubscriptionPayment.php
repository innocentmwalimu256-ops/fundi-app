<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'user_id',
        'plan_id',
        'amount',
        'currency',
        'payment_reference',
        'payment_method',
        'status', // pending, under_review, verified, success, failed, rejected
        'admin_notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public static function generateReference(): string
    {
        return 'SUB-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function getFormattedAmountAttribute(): string
    {
        return sprintf('%s %s', $this->currency, number_format($this->amount, 0));
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'success', 'verified', 'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
            'under_review' => 'bg-blue-100 text-blue-800 border-blue-200',
            'failed', 'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
