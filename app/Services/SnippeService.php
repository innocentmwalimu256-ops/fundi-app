<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SnippeService
{
    /**
     * Get the configured Snippe API Key.
     */
    public static function getApiKey(): string
    {
        return config('services.snippe.api_key') 
            ?: env('SNIPPE_API_KEY', 'snp_d72351fa5858490448258a2d515e5a8ad2439fca3ddf64a2123737fbc1c28ce8');
    }

    /**
     * Get the configured Webhook Secret.
     */
    public static function getWebhookSecret(): string
    {
        return config('services.snippe.webhook_secret') 
            ?: env('SNIPPE_WEBHOOK_SECRET', 'whsec_0836d02c3d08337fe6597f9c199e2d82c98fea6c718cf3669bc08dc9b6485cf6');
    }

    /**
     * Get Base API URL.
     */
    public static function getBaseUrl(): string
    {
        return rtrim(config('services.snippe.base_url') ?: env('SNIPPE_BASE_URL', 'https://api.snippe.sh/v1'), '/');
    }

    /**
     * Get Webhook URL to deliver notifications to.
     */
    public static function getWebhookUrl(): string
    {
        return config('services.snippe.webhook_url') 
            ?: env('SNIPPE_WEBHOOK_URL', 'https://fundi-app-one.vercel.app/api/webhook/snippe');
    }

    /**
     * Format phone number to standard E.164 without leading plus (e.g. 2557XXXXXXXX)
     */
    public static function formatPhoneNumber(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '0')) {
            $clean = '255' . substr($clean, 1);
        } elseif (str_starts_with($clean, '255')) {
            // already in 255 format
        } else {
            $clean = '255' . $clean;
        }
        return $clean;
    }

    /**
     * Initiate a payment session or charge via Snippe API.
     *
     * @param User $user
     * @param SubscriptionPlan $plan
     * @param string $phoneNumber
     * @param string $paymentMethod
     * @return array
     */
    public static function initiateSubscriptionPayment(User $user, SubscriptionPlan $plan, string $phoneNumber, string $paymentMethod = 'mpesa'): array
    {
        $reference = SubscriptionPayment::generateReference();
        $formattedPhone = self::formatPhoneNumber($phoneNumber);
        $amount = (int) $plan->price;

        // Record pending payment in database first
        $payment = SubscriptionPayment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'payment_reference' => $reference,
            'amount' => $amount,
            'currency' => 'TZS',
            'payment_method' => $paymentMethod,
            'phone_number' => $formattedPhone,
            'status' => 'pending',
            'gateway' => 'snippe',
            'gateway_payload' => [
                'user_name' => $user->full_name,
                'user_email' => $user->email,
                'phone' => $formattedPhone,
                'plan_name' => $plan->name,
                'initiated_at' => now()->toIso8601String(),
            ],
        ]);

        $apiKey = self::getApiKey();
        $baseUrl = self::getBaseUrl();
        $webhookUrl = self::getWebhookUrl();
        $redirectUrl = route('technician.subscription', ['ref' => $reference]);

        $payload = [
            'amount' => $amount,
            'currency' => 'TZS',
            'customer' => [
                'name' => $user->full_name,
                'email' => $user->email,
                'phone' => $formattedPhone,
            ],
            'customer_phone' => $formattedPhone,
            'customer_name' => $user->full_name,
            'customer_email' => $user->email,
            'reference' => $reference,
            'description' => "FUNDI Subscription: {$plan->name} ({$plan->duration_days} Days)",
            'webhook_url' => $webhookUrl,
            'redirect_url' => $redirectUrl,
            'metadata' => [
                'user_id' => (string) $user->id,
                'plan_id' => (string) $plan->id,
                'payment_id' => (string) $payment->id,
                'reference' => $reference,
                'type' => 'subscription',
            ],
        ];

        Log::info("[Snippe] Initiating payment request", [
            'reference' => $reference,
            'user_id' => $user->id,
            'amount' => $amount,
            'phone' => $formattedPhone,
            'webhook_url' => $webhookUrl,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post("{$baseUrl}/sessions", $payload);

            $statusCode = $response->status();
            $responseBody = $response->json() ?? [];

            Log::info("[Snippe] Response received", [
                'status_code' => $statusCode,
                'reference' => $reference,
                'body' => $responseBody,
            ]);

            // Handle successful session creation (200 / 201)
            if ($response->successful()) {
                $checkoutUrl = $responseBody['checkout_url'] 
                    ?? $responseBody['url'] 
                    ?? $responseBody['data']['checkout_url'] 
                    ?? $responseBody['data']['url'] 
                    ?? null;

                $gatewayId = $responseBody['id'] 
                    ?? $responseBody['session_id'] 
                    ?? $responseBody['data']['id'] 
                    ?? null;

                $payment->update([
                    'gateway_transaction_id' => $gatewayId,
                    'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                        'session_response' => $responseBody,
                    ]),
                ]);

                return [
                    'success' => true,
                    'status_code' => $statusCode,
                    'checkout_url' => $checkoutUrl,
                    'reference' => $reference,
                    'message' => __('Malipo yameanzishwa kikamilifu. Tafadhali kamilisha uthibitisho kwenye simu yako.'),
                    'data' => $responseBody,
                ];
            }

            // Handle Error responses with exact status code mapping
            $errorMessage = self::parseSnippeError($statusCode, $responseBody, $response->body());

            Log::error("[Snippe] Payment initiation failed", [
                'status_code' => $statusCode,
                'reference' => $reference,
                'error_message' => $errorMessage,
                'raw_body' => $response->body(),
            ]);

            $payment->update([
                'status' => 'failed',
                'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                    'error_status' => $statusCode,
                    'error_message' => $errorMessage,
                    'raw_response' => $responseBody,
                ]),
            ]);

            return [
                'success' => false,
                'status_code' => $statusCode,
                'message' => $errorMessage,
                'reference' => $reference,
                'raw' => $responseBody,
            ];

        } catch (Throwable $e) {
            Log::critical("[Snippe] Connection / Exception error during checkout", [
                'reference' => $reference,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $payment->update([
                'status' => 'failed',
                'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                    'exception' => $e->getMessage(),
                ]),
            ]);

            return [
                'success' => false,
                'status_code' => 500,
                'message' => __('Kuna hitilafu ya mawasiliano na seva ya malipo ya Snippe. Tafadhali jaribu tena baada ya muda mfupi.'),
                'reference' => $reference,
                'error_detail' => $e->getMessage(),
            ];
        }
    }

    /**
     * Map and extract readable error messages from Snippe HTTP response.
     */
    public static function parseSnippeError(int $statusCode, array $body, string $rawText = ''): string
    {
        $rawMessage = $body['message'] 
            ?? $body['error']['message'] 
            ?? $body['error'] 
            ?? $body['detail'] 
            ?? null;

        $errorCode = $body['code'] 
            ?? $body['error']['code'] 
            ?? null;

        // Specific HTTP Status Code Interpretations
        switch ($statusCode) {
            case 400: // Bad Request
                if ($errorCode === 'invalid_phone' || (is_string($rawMessage) && stripos($rawMessage, 'phone') !== false)) {
                    return __('Namba ya simu si sahihi. Hakikisha ni namba halisi ya Tanzania (mfano: 07XXXXXXXX au 2557XXXXXXXX).');
                }
                if ($errorCode === 'invalid_amount' || (is_string($rawMessage) && stripos($rawMessage, 'amount') !== false)) {
                    return __('Kiasi cha malipo hakiruhusiwi (Kiwango cha chini ni TZS 500).');
                }
                if ($errorCode === 'invalid_state') {
                    return __('Muamala huu tayari ulikwishashughulikiwa au umekamilika.');
                }
                return $rawMessage ? (string)$rawMessage : __('Taarifa za malipo hazikubaliwi (400 Bad Request).');

            case 401: // Unauthorized
                return __('Hitilafu ya uthibitishaji wa API (401 Unauthorized). API Key ya Snippe si sahihi au imeisha muda.');

            case 403: // Forbidden
                return __('Ufikiaji umezuiwa (403 Forbidden). Akaunti ya Snippe haina ruhusa ya kufanya muamala huu.');

            case 404: // Not Found
                return __('Huduma ya malipo au URL haikupatikana (404 Not Found kwenye seva ya Snippe).');

            case 405: // Method Not Allowed
                return __('Njia ya ombi hairuhusiwi (405 Method Not Allowed).');

            case 409: // Conflict
                return __('Muamala wenye namba hii unaendelea kushughulikiwa tayari. Tafadhali subiri uthibitisho kwenye simu yako.');

            case 410: // Gone / Expired
                return __('Muda wa kufanya muamala huu umekwisha (410 Payment Session Expired). Tafadhali fungua upya.');

            case 422: // Validation Error
                if (!empty($body['errors']) && is_array($body['errors'])) {
                    $details = [];
                    foreach ($body['errors'] as $field => $errors) {
                        $details[] = is_array($errors) ? implode(', ', $errors) : (string)$errors;
                    }
                    return __('Hitilafu ya data: ') . implode('; ', $details);
                }
                return $rawMessage ? (string)$rawMessage : __('Taarifa ulizojaza hazijakamilika (422 Unprocessable Entity).');

            case 429: // Rate Limited
                return __('Maombi mengi ya malipo kwa wakati mmoja (429 Too Many Requests). Tafadhali subiri kidogo kisha ujaribu tena.');

            case 500:
            case 502:
            case 503:
            case 504:
                return __('Mtandao wa malipo (Snippe Gateway) unashughulikiwa kwa sasa (500 Server Error). Tafadhali jaribu tena baada ya muda mfupi.');

            default:
                return $rawMessage ? (string)$rawMessage : __('Hitilafu imetokea wakati wa kuwasiliana na mfumo wa malipo (Code: ' . $statusCode . ').');
        }
    }

    /**
     * Verify incoming Webhook signature from Snippe using HMAC-SHA256.
     *
     * @param Request $request
     * @return bool
     */
    public static function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('X-Webhook-Signature') 
            ?: $request->header('X-Signature') 
            ?: $request->header('Signature') 
            ?: $request->server('HTTP_X_WEBHOOK_SIGNATURE');

        $timestamp = $request->header('X-Webhook-Timestamp') 
            ?: $request->header('Timestamp') 
            ?: $request->server('HTTP_X_WEBHOOK_TIMESTAMP');

        $secret = self::getWebhookSecret();
        $rawBody = $request->getContent();

        if (empty($signature) || empty($secret)) {
            Log::warning("[Snippe Webhook] Missing signature header or secret key", [
                'has_signature' => !empty($signature),
                'has_secret' => !empty($secret),
            ]);
            return false;
        }

        // Replay protection: Reject if timestamp is older than 5 minutes (300 seconds)
        if ($timestamp && is_numeric($timestamp)) {
            $currentTime = time();
            if (abs($currentTime - (int)$timestamp) > 300) {
                Log::warning("[Snippe Webhook] Timestamp expired (replay attack protection)", [
                    'current_time' => $currentTime,
                    'webhook_timestamp' => $timestamp,
                ]);
                return false;
            }
        }

        // Compute HMAC-SHA256 signature
        $dataToSign = $timestamp ? "{$timestamp}.{$rawBody}" : $rawBody;
        $expectedSignature = hash_hmac('sha256', $dataToSign, $secret);

        // Also check if signature was computed directly on raw body without timestamp
        $expectedAltSignature = hash_hmac('sha256', $rawBody, $secret);

        $isValid = hash_equals($expectedSignature, (string)$signature) 
            || hash_equals($expectedAltSignature, (string)$signature);

        if (!$isValid) {
            Log::warning("[Snippe Webhook] Invalid signature match", [
                'received_signature' => $signature,
                'expected_signature' => $expectedSignature,
            ]);
        }

        return $isValid;
    }

    /**
     * Process verified webhook event from Snippe.
     *
     * @param array $payload
     * @return array
     */
    public static function processWebhookEvent(array $payload): array
    {
        $event = $payload['event'] ?? $payload['type'] ?? 'unknown';
        $data = $payload['data'] ?? $payload;

        $reference = $data['reference'] 
            ?? $data['metadata']['reference'] 
            ?? $payload['reference'] 
            ?? null;

        $gatewayId = $data['id'] ?? $payload['id'] ?? null;
        $status = strtolower($data['status'] ?? $event);

        Log::info("[Snippe Webhook] Processing event", [
            'event' => $event,
            'reference' => $reference,
            'gateway_id' => $gatewayId,
            'status' => $status,
        ]);

        if (!$reference) {
            return [
                'success' => false,
                'message' => 'No reference found in webhook payload',
            ];
        }

        $payment = SubscriptionPayment::where('payment_reference', $reference)->first();
        if (!$payment) {
            Log::warning("[Snippe Webhook] Payment reference not found in DB", ['reference' => $reference]);
            return [
                'success' => false,
                'message' => "Payment reference {$reference} not found",
            ];
        }

        // Check if event is successful payment
        $isSuccess = in_array($event, ['payment.completed', 'payment.succeeded', 'charge.completed', 'session.completed'])
            || in_array($status, ['completed', 'succeeded', 'paid', 'successful']);

        if ($isSuccess) {
            // If already processed successfully, return idempotent OK
            if ($payment->status === 'success') {
                return [
                    'success' => true,
                    'message' => 'Payment was already processed successfully',
                ];
            }

            $user = $payment->user;
            $plan = $payment->plan ?? SubscriptionPlan::find($payment->plan_id);

            if ($user && $plan) {
                // Activate subscription
                $subscription = SubscriptionService::activateSubscription(
                    $user,
                    $plan,
                    $payment->payment_method ?? 'mobile_money',
                    $reference
                );

                $payment->update([
                    'status' => 'success',
                    'gateway_transaction_id' => $gatewayId ?: $payment->gateway_transaction_id,
                    'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                        'webhook_payload' => $payload,
                        'completed_at' => now()->toIso8601String(),
                    ]),
                ]);

                // Create in-app notification for technician
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'subscription_activated',
                    'title' => 'Malipo Yamekamilika: Kifurushi Kimewashwa',
                    'message' => "Hongera {$user->full_name}! Malipo yako ya TZS " . number_format($payment->amount, 0) . " yamethibitishwa na akaunti yako imekuwa ACTIVE kikamilifu.",
                    'action_url' => route('technician.subscription'),
                    'is_read' => false,
                ]);

                AuditLog::record(
                    'subscription_paid_snippe_webhook',
                    $user,
                    "Subscription for plan '{$plan->name}' activated via Snippe Webhook (Ref: {$reference}).",
                    [
                        'payment_id' => $payment->id,
                        'reference' => $reference,
                        'amount' => $payment->amount,
                        'gateway_id' => $gatewayId,
                    ]
                );

                Log::info("[Snippe Webhook] Subscription successfully activated for user", [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'reference' => $reference,
                ]);

                return [
                    'success' => true,
                    'message' => 'Subscription activated successfully',
                    'subscription_id' => $subscription->id,
                ];
            }
        }

        // Check if event is failed payment
        $isFailed = in_array($event, ['payment.failed', 'charge.failed', 'session.expired'])
            || in_array($status, ['failed', 'expired', 'canceled', 'cancelled']);

        if ($isFailed) {
            $payment->update([
                'status' => 'failed',
                'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                    'webhook_payload' => $payload,
                    'failed_at' => now()->toIso8601String(),
                ]),
            ]);

            Log::info("[Snippe Webhook] Payment marked as failed", ['reference' => $reference]);

            return [
                'success' => true,
                'message' => 'Payment marked as failed',
            ];
        }

        return [
            'success' => true,
            'message' => "Unhandled event: {$event}",
        ];
    }
}
