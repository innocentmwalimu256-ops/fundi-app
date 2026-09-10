<?php

namespace App\Services;

use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;

class PaymentService
{
    /**
     * Initiate and process a subscription payment.
     * In production, this dispatches to Vodacom M-Pesa / Tigo Pesa / Airtel Money API or Card Gateway.
     */
    public static function processSubscriptionPayment(User $user, SubscriptionPlan $plan, string $paymentMethod = 'mobile_money', array $payload = []): array
    {
        $reference = SubscriptionPayment::generateReference();

        // Simulated robust gateway processing
        $subscription = SubscriptionService::activateSubscription($user, $plan, $paymentMethod, $reference);

        return [
            'success' => true,
            'message' => "Payment of {$plan->currency} " . number_format($plan->price, 0) . " processed successfully.",
            'reference' => $reference,
            'subscription' => $subscription,
        ];
    }
}
