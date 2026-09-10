<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Role Helpers
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isTechnician(): bool
    {
        return $this->role === 'technician';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVerifiedTechnician(): bool
    {
        return $this->isTechnician() && 
               $this->technicianProfile && 
               $this->technicianProfile->verification_status === 'approved';
    }

    public function hasActiveSubscription(): bool
    {
        if (!$this->isTechnician()) {
            return false;
        }
        $sub = $this->subscription;
        return $sub && $sub->isActive();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getFirstNameAttribute(): string
    {
        $parts = explode(' ', trim($this->full_name), 2);
        return $parts[0] ?? '';
    }

    public function getLastNameAttribute(): string
    {
        $parts = explode(' ', trim($this->full_name), 2);
        return $parts[1] ?? '';
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->full_name));
        $initials = '';
        foreach ($words as $w) {
            if (!empty($w)) {
                $initials .= strtoupper($w[0]);
            }
        }
        return substr($initials, 0, 2) ?: 'U';
    }

    public function getMaskedPhoneAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (strlen($phone) >= 4) {
            return substr($phone, 0, 2) . '••••••••';
        }
        return '07••••••••';
    }

    public function getWhatsappUrl(string $prefilledText = ''): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '255' . substr($cleanPhone, 1);
        } elseif (!str_starts_with($cleanPhone, '255')) {
            $cleanPhone = '255' . $cleanPhone;
        }

        return 'https://wa.me/' . $cleanPhone . ($prefilledText ? '?text=' . urlencode($prefilledText) : '');
    }

    public function isFeaturedTechnician(): bool
    {
        return $this->hasActiveSubscription() 
            && $this->subscription->plan 
            && $this->subscription->plan->is_featured;
    }

    public function getSubscriptionExpiryWarning(): ?array
    {
        if ($this->role !== 'technician' || !$this->subscription) return null;
        
        $sub = $this->subscription;
        if (!$sub->isActive()) {
            return [
                'status' => 'expired',
                'message' => 'Your subscription has expired. Renew your plan to receive new service opportunities and unlock new client contacts.',
                'days_left' => 0,
                'is_urgent' => true,
            ];
        }

        $days = $sub->days_remaining;
        if ($days <= 3) {
            $msg = $days === 1 
                ? 'Your subscription expires tomorrow!' 
                : "Your {$sub->plan->name} Plan expires in {$days} days.";

            return [
                'status' => 'warning',
                'message' => $msg,
                'days_left' => $days,
                'is_urgent' => $days <= 1,
            ];
        }

        return null;
    }

    // Relationships
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->latest('started_at');
    }

    public function subscriptionPayments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class)->latest();
    }

    public function technicianProfile(): HasOne
    {
        return $this->hasOne(TechnicianProfile::class);
    }

    public function technicianApplication(): HasOne
    {
        return $this->hasOne(TechnicianApplication::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'technician_services', 'technician_id', 'service_id')->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TechnicianDocument::class, 'technician_id');
    }

    public function clientRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'client_id');
    }

    public function technicianRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'technician_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'client_id');
    }

    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'client_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'technician_id');
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'client_id');
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(TechnicianPortfolio::class, 'technician_id')->latest();
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(TechnicianAvailability::class, 'technician_id');
    }

    public function serviceAreas(): HasMany
    {
        return $this->hasMany(TechnicianServiceArea::class, 'technician_id');
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotificationsCount(): int
    {
        return $this->hasMany(Notification::class)->where('is_read', false)->count();
    }
}
