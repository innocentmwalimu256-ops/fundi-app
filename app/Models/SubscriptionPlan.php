<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'currency',
        'duration_days',
        'description',
        'features',
        'request_limit',
        'portfolio_limit',
        'service_area_limit',
        'contact_access',
        'priority_listing',
        'is_featured',
        'priority_support',
        'analytics_level',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
            'portfolio_limit' => 'integer',
            'service_area_limit' => 'integer',
            'contact_access' => 'boolean',
            'priority_listing' => 'boolean',
            'is_featured' => 'boolean',
            'priority_support' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class, 'plan_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return sprintf('%s %s / %d Days', $this->currency, number_format($this->price, 0), $this->duration_days);
    }
}
