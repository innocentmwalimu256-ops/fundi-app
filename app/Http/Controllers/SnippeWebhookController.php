<?php

namespace App\Http\Controllers;

use App\Services\SnippeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SnippeWebhookController extends Controller
{
    /**
     * Handle incoming webhook requests from Snippe.
     * Supported URL routes:
     * - POST /api/webhook/snippe
     * - POST /snippe/payment/webhook
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        $rawPayload = $request->getContent();
        $payload = $request->json()->all();

        Log::info("[Snippe Webhook] Incoming Webhook received", [
            'ip' => $request->ip(),
            'headers' => [
                'signature' => $request->header('X-Webhook-Signature') ?: $request->header('X-Signature'),
                'timestamp' => $request->header('X-Webhook-Timestamp') ?: $request->header('Timestamp'),
                'user_agent' => $request->header('User-Agent'),
            ],
            'payload' => $payload,
        ]);

        // 1. Verify HMAC-SHA256 Signature
        $isValidSignature = SnippeService::verifyWebhookSignature($request);
        if (!$isValidSignature) {
            // For testing / initial development, log a warning if signature mismatch
            Log::warning("[Snippe Webhook] Signature verification failed");
            
            // If environment is production and signature secret is present, reject invalid signatures
            if (config('app.env') === 'production' && !empty(SnippeService::getWebhookSecret())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid webhook signature',
                ], 401);
            }
        }

        // 2. Process Webhook Event
        try {
            $result = SnippeService::processWebhookEvent($payload);

            return response()->json([
                'status' => $result['success'] ? 'success' : 'ignored',
                'message' => $result['message'] ?? 'Webhook handled',
                'timestamp' => now()->toIso8601String(),
            ], 200);

        } catch (Throwable $e) {
            Log::error("[Snippe Webhook] Exception while processing webhook", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error while processing webhook',
            ], 500);
        }
    }
}
