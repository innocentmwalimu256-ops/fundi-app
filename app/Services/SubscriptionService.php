<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public static function isActive(User $user): bool
    {
        if (!$user->isTechnician()) {
            return false;
        }

        $sub = $user->subscription;
        return $sub && $sub->isActive();
    }

    public static function canReceiveRequests(User $user): bool
    {
        if (!$user->isVerifiedTechnician()) {
            return false;
        }
        return self::isActive($user);
    }

    public static function canViewProtectedContact(User $user, ServiceRequest $request): bool
    {
        // 1. If user is client on this request: contact is unlocked immediately when connection fee is paid
        if ($user->id === $request->client_id) {
            return $request->connection_fee_status === 'paid' || in_array($request->status, ['accepted', 'quotation_pending', 'quotation_accepted', 'scheduled', 'on_the_way', 'in_progress', 'completed', 'client_confirmed', 'reviewed']);
        }

        // 2. If user is technician on this request: must be assigned and have active subscription with contact access
        if ($user->id === $request->technician_id) {
            if (!self::isActive($user)) {
                return false;
            }
            $sub = $user->subscription;
            return $sub && $sub->plan && $sub->plan->contact_access;
        }

        // 3. Admin can always view
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    public static function canUsePriorityListing(User $user): bool
    {
        if (!self::isActive($user)) {
            return false;
        }
        $sub = $user->subscription;
        return $sub && $sub->plan && $sub->plan->priority_listing;
    }

    public static function activateSubscription(User $user, SubscriptionPlan $plan, string $paymentMethod = 'mobile_money', ?string $reference = null): Subscription
    {
        return DB::transaction(function () use ($user, $plan, $paymentMethod, $reference) {
            $now = now();
            $currentSub = $user->subscription;

            // If renewing an active subscription, extend from the current expires_at
            if ($currentSub && $currentSub->isActive()) {
                $startedAt = $currentSub->started_at;
                $expiresAt = $currentSub->expires_at->addDays($plan->duration_days);
            } else {
                $startedAt = $now;
                $expiresAt = $now->copy()->addDays($plan->duration_days);
            }

            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'started_at' => $startedAt,
                'expires_at' => $expiresAt,
                'auto_renew' => false,
            ]);

            // Immediately award tier rating stars to technician
            if ($user->technicianProfile) {
                $user->technicianProfile->recalculateRating();
            }

            $payRef = $reference ?: SubscriptionPayment::generateReference();
            $existingPayment = SubscriptionPayment::where('payment_reference', $payRef)->first();
            if ($existingPayment) {
                $existingPayment->update([
                    'subscription_id' => $subscription->id,
                    'status' => 'success',
                    'paid_at' => $now,
                ]);
            } else {
                SubscriptionPayment::create([
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'amount' => $plan->price,
                    'currency' => $plan->currency,
                    'payment_reference' => $payRef,
                    'payment_method' => $paymentMethod,
                    'status' => 'success',
                    'paid_at' => $now,
                ]);
            }

            AuditLog::log(
                'subscription_activated',
                "Technician {$user->full_name} subscribed to plan: {$plan->name} ({$plan->formatted_price}). Expires on {$expiresAt->format('d M Y')}.",
                'Subscription',
                $subscription->id
            );

            Notification::send(
                $user->id,
                'subscription_activated',
                'Subscription Activated ✓',
                "Your {$plan->name} subscription is active until {$expiresAt->format('d M Y')}. You now have full access to incoming client requests and contact details.",
                route('technician.subscription')
            );

            return $subscription;
        });
    }

    public static function createFreeTrial(User $user, int $trialDays = 7): Subscription
    {
        $basicPlan = SubscriptionPlan::where('slug', 'basic')->first() ?: SubscriptionPlan::first();

        return DB::transaction(function () use ($user, $basicPlan, $trialDays) {
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $basicPlan ? $basicPlan->id : 1,
                'status' => 'free_trial',
                'started_at' => now(),
                'expires_at' => now()->addDays($trialDays),
                'auto_renew' => false,
            ]);

            AuditLog::log(
                'free_trial_granted',
                "Granted {$trialDays}-day free trial to newly approved technician {$user->full_name}",
                'Subscription',
                $subscription->id
            );

            Notification::send(
                $user->id,
                'free_trial_activated',
                "Welcome to FUNDI — {$trialDays}-Day Free Trial Active!",
                "You have been granted a {$trialDays}-day free trial of FUNDI Pro features. Explore incoming client requests and start receiving jobs today.",
                route('technician.subscription')
            );

            return $subscription;
        });
    }

    public static function checkAndExpireSubscriptions(): int
    {
        $expiredCount = 0;
        $now = now();

        $activeSubs = Subscription::whereIn('status', ['active', 'free_trial'])
            ->where('expires_at', '<', $now)
            ->with('user')
            ->get();

        foreach ($activeSubs as $sub) {
            $sub->update(['status' => 'expired']);
            if ($sub->user && $sub->user->technicianProfile) {
                $sub->user->technicianProfile->recalculateRating();
            }
            $expiredCount++;

            AuditLog::log(
                'subscription_expired',
                "Subscription expired for technician {$sub->user->full_name} (#{$sub->user->id})",
                'Subscription',
                $sub->id
            );

            Notification::send(
                $sub->user_id,
                'subscription_expired',
                'Your FUNDI Subscription Has Expired',
                'Your technician subscription has expired. Renew your plan today to continue receiving new client requests and unlocking contact details.',
                route('technician.subscription')
            );
        }

        return $expiredCount;
    }
}
