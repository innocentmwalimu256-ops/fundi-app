<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SnippeService;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentStatusController extends Controller
{
    /**
     * Poll live status of any payment reference (connection fee or subscription).
     *
     * @param Request $request
     * @param string $reference
     * @return JsonResponse
     */
    public function checkStatus(Request $request, string $reference): JsonResponse
    {
        $reference = trim($reference);

        // 1. Check if it is a Client Service Request Connection Fee
        $serviceRequest = ServiceRequest::where('connection_fee_reference', $reference)
            ->orWhere('reference_no', $reference)
            ->first();

        if ($serviceRequest) {
            $feeAmount = (int) ($serviceRequest->connection_fee ?? 500);
            if ($serviceRequest->connection_fee_status === 'paid') {
                return response()->json([
                    'paid' => true,
                    'status' => 'paid',
                    'type' => 'connection_fee',
                    'message' => __("Malipo ya ada ya TZS :amount yamethibitishwa kikamilifu!", ['amount' => number_format($feeAmount, 0)]),
                    'redirect_url' => route('client.requests.show', $serviceRequest->id),
                ]);
            }

            // If not marked paid locally yet, query Snippe API live
            $gatewayStatus = self::querySnippeLivePaymentStatus($reference, $serviceRequest->connection_fee_reference);
            if ($gatewayStatus === 'completed' || $gatewayStatus === 'succeeded' || $gatewayStatus === 'paid') {
                $serviceRequest->update(['connection_fee_status' => 'paid']);

                AuditLog::log(
                    'verify_client_connection_fee_polling',
                    "Client paid connection fee TZS " . number_format($feeAmount, 0) . " for {$serviceRequest->reference_no} verified via live check.",
                    'ServiceRequest',
                    $serviceRequest->id
                );

                Notification::send(
                    $serviceRequest->technician_id,
                    'new_request',
                    'New Verified Client Service Request Received!',
                    "Client {$serviceRequest->client->full_name} has paid the connection fee for {$serviceRequest->reference_no}.",
                    route('technician.requests.show', $serviceRequest->id)
                );

                return response()->json([
                    'paid' => true,
                    'status' => 'paid',
                    'type' => 'connection_fee',
                    'message' => __("Malipo ya ada ya TZS :amount yamethibitishwa kikamilifu!", ['amount' => number_format($feeAmount, 0)]),
                    'redirect_url' => route('client.requests.show', $serviceRequest->id),
                ]);
            }

            return response()->json([
                'paid' => false,
                'status' => 'pending',
                'type' => 'connection_fee',
                'message' => __('Inasubiri kuweka PIN kwenye simu yako...'),
            ]);
        }

        // 2. Check if it is a Subscription Payment
        $payment = SubscriptionPayment::where('payment_reference', $reference)
            ->orWhere('gateway_transaction_id', $reference)
            ->first();

        if ($payment) {
            if ($payment->status === 'success') {
                return response()->json([
                    'paid' => true,
                    'status' => 'paid',
                    'type' => 'subscription',
                    'message' => __('Malipo ya kifurushi yamethibitishwa! Akaunti yako imewashwa.'),
                    'redirect_url' => route('technician.subscription'),
                ]);
            }

            // Query Snippe live
            $gatewayStatus = self::querySnippeLivePaymentStatus($reference, $payment->gateway_transaction_id);
            if ($gatewayStatus === 'completed' || $gatewayStatus === 'succeeded' || $gatewayStatus === 'paid') {
                $user = $payment->user;
                $plan = $payment->plan ?? SubscriptionPlan::find($payment->plan_id);

                if ($user && $plan) {
                    SubscriptionService::activateSubscription($user, $plan, $payment->payment_method ?? 'mobile_money', $reference);
                    $payment->update(['status' => 'success']);

                    return response()->json([
                        'paid' => true,
                        'status' => 'paid',
                        'type' => 'subscription',
                        'message' => __('Malipo ya kifurushi yamethibitishwa! Akaunti yako imewashwa.'),
                        'redirect_url' => route('technician.subscription'),
                    ]);
                }
            }

            return response()->json([
                'paid' => false,
                'status' => 'pending',
                'type' => 'subscription',
                'message' => __('Inasubiri kuweka PIN kwenye simu yako...'),
            ]);
        }

        // 3. Fallback generic check
        $liveStatus = self::querySnippeLivePaymentStatus($reference);
        if ($liveStatus === 'completed' || $liveStatus === 'succeeded') {
            return response()->json([
                'paid' => true,
                'status' => 'paid',
                'message' => __('Malipo yamethibitishwa!'),
                'redirect_url' => url()->previous() ?: route('home'),
            ]);
        }

        return response()->json([
            'paid' => false,
            'status' => 'pending',
            'message' => __('Inasubiri kuweka PIN kwenye simu yako...'),
        ]);
    }

    /**
     * Query Snippe API directly for live status.
     */
    private static function querySnippeLivePaymentStatus(string ...$references): ?string
    {
        $apiKey = SnippeService::getApiKey();
        $baseUrl = SnippeService::getBaseUrl();

        foreach ($references as $ref) {
            if (empty($ref)) continue;

            try {
                // Try direct payment check
                $res = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Accept' => 'application/json',
                ])->timeout(5)->get("{$baseUrl}/payments/{$ref}");

                if ($res->successful()) {
                    $data = $res->json()['data'] ?? [];
                    $status = strtolower($data['status'] ?? '');
                    if ($status) return $status;
                }

                // Try query param
                $resQ = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Accept' => 'application/json',
                ])->timeout(5)->get("{$baseUrl}/payments", ['reference' => $ref]);

                if ($resQ->successful()) {
                    $items = $resQ->json()['data']['items'] ?? $resQ->json()['data'] ?? [];
                    if (!empty($items) && is_array($items)) {
                        $first = is_array($items[0] ?? null) ? $items[0] : $items;
                        $status = strtolower($first['status'] ?? '');
                        if ($status) return $status;
                    }
                }
            } catch (Throwable $e) {
                // ignore timeout during polling
            }
        }

        return null;
    }

    /**
     * AJAX Endpoint to initiate Client Connection Fee Payment.
     */
    public function initiateClientFeeAjax(Request $request, $id): JsonResponse
    {
        $client = Auth::user();
        $serviceRequest = ServiceRequest::where('client_id', $client->id)->findOrFail($id);

        if ($serviceRequest->connection_fee_status === 'paid') {
            return response()->json([
                'success' => true,
                'already_paid' => true,
                'message' => __('Ada ya kuunganishwa tayari imeshalipwa.'),
                'redirect_url' => route('client.requests.show', $serviceRequest->id),
            ]);
        }

        $phone = trim($request->input('phone_number') ?? '') ?: ($client->phone ?? '');
        $method = $request->input('payment_method', 'mpesa');

        $result = SnippeService::initiateClientConnectionFeePayment($client, $serviceRequest, $phone, $method);

        return response()->json([
            'success' => $result['success'] ?? false,
            'reference' => $result['reference'] ?? $serviceRequest->connection_fee_reference,
            'message' => $result['message'] ?? '',
            'phone' => SnippeService::formatPhoneNumber($phone),
            'amount' => (int) ($serviceRequest->connection_fee ?? 500),
        ], $result['success'] ? 200 : 400);
    }

    /**
     * AJAX Endpoint to initiate Technician Subscription Payment.
     */
    public function initiateSubscriptionAjax(Request $request, $slug): JsonResponse
    {
        $technician = Auth::user();
        $plan = SubscriptionPlan::where('slug', $slug)
            ->orWhere('id', $slug)
            ->first() ?? SubscriptionPlan::first();

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => __('Kifurushi hakijapatikana.'),
            ], 404);
        }

        $phone = trim($request->input('phone_number') ?? '') ?: ($technician->phone ?? '');
        $method = $request->input('payment_method', 'mpesa');

        $result = SnippeService::initiateSubscriptionPayment($technician, $plan, $phone, $method);

        return response()->json([
            'success' => $result['success'] ?? false,
            'reference' => $result['reference'] ?? '',
            'message' => $result['message'] ?? '',
            'phone' => SnippeService::formatPhoneNumber($phone),
            'amount' => (int) $plan->price,
        ], $result['success'] ? 200 : 400);
    }
}
