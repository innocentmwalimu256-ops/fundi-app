<?php

namespace App\Services;

use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;

class PaymentService
{
    /**
     * Initiate and process a subscription payment via Snippe Payment Gateway.
     */
    public static function processSubscriptionPayment(User $user, SubscriptionPlan $plan, string $paymentMethod = 'mpesa', array $payload = []): array
    {
        $phoneNumber = $payload['phone_number'] ?? $user->phone ?? '';

        return SnippeService::initiateSubscriptionPayment($user, $plan, $phoneNumber, $paymentMethod);
    }
}
