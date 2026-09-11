<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    // ==========================================
    // TECHNICIAN SUBSCRIPTION ACTIONS
    // ==========================================

    public function index()
    {
        $technician = Auth::user();
        $subscription = $technician->subscription;
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('display_order')->get();
        $payments = SubscriptionPayment::where('user_id', $technician->id)->latest()->take(10)->get();

        return view('technician.subscription.index', compact('technician', 'subscription', 'plans', 'payments'));
    }

    public function expired()
    {
        $technician = Auth::user();
        $subscription = $technician->subscription;
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('display_order')->get();

        return view('technician.subscription.expired', compact('technician', 'subscription', 'plans'));
    }

    public function checkout($slug)
    {
        $technician = Auth::user();
        $plan = SubscriptionPlan::where('slug', $slug)
            ->orWhere('id', $slug)
            ->where('is_active', true)
            ->first() ?? SubscriptionPlan::where('is_active', true)->first();

        if (!$plan) {
            return redirect()->route('technician.subscription')->with('error', 'Kifurushi hakijapatikana.');
        }

        $currentSub = $technician->subscription;

        $admin = User::where('role', 'admin')->first();
        $adminPhone = $admin ? $admin->phone : '0675315279';
        $cleanPhone = preg_replace('/[^0-9]/', '', $adminPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '255' . substr($cleanPhone, 1);
        } elseif (!str_starts_with($cleanPhone, '255')) {
            $cleanPhone = '255' . $cleanPhone;
        }

        $waText = "Habari Admin wa FUNDI, mimi ni Fundi {$technician->full_name} (Simu: {$technician->phone}). Nahitaji kulipia kifurushi cha {$plan->name} (TZS " . number_format($plan->price, 0) . "). Naomba maelekezo ya malipo.";
        $adminWhatsappUrl = 'https://wa.me/' . $cleanPhone . '?text=' . urlencode($waText);

        return view('technician.subscription.checkout', compact('technician', 'plan', 'currentSub', 'adminWhatsappUrl', 'adminPhone'));
    }

    public function processPayment(Request $request, $slug)
    {
        $technician = Auth::user();
        $plan = SubscriptionPlan::where('slug', $slug)
            ->orWhere('id', $slug)
            ->first() ?? SubscriptionPlan::first();

        if (!$plan) {
            return redirect()->route('technician.subscription')
                ->with('error', __('Kifurushi ulichochagua hakijapatikana. Tafadhali chagua kifurushi sahihi.'));
        }

        $paymentMethod = strtolower(trim($request->input('payment_method', 'mpesa')));
        if (empty($paymentMethod)) {
            $paymentMethod = 'mpesa';
        }

        $phoneNumber = trim($request->input('phone_number') ?? '') ?: ($technician->phone ?? '');
        if (empty($phoneNumber)) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Tafadhali weka namba yako ya simu ya kulipia (mfano: 07XXXXXXXX).'));
        }

        $result = PaymentService::processSubscriptionPayment($technician, $plan, $paymentMethod, [
            'phone_number' => $phoneNumber,
        ]);

        // If checkout URL is provided by Snippe, redirect user to the secure payment page
        if (!empty($result['checkout_url'])) {
            return redirect()->away($result['checkout_url']);
        }

        // If payment initiated successfully
        if (!empty($result['success'])) {
            return redirect()->route('technician.subscription')
                ->with('success', $result['message'] ?? __("Ombi la malipo ya TZS :amount limetumwa kwenye namba yako ya simu. Tafadhali weka PIN kukamilisha.", ['amount' => number_format($plan->price, 0)]));
        }

        // If error occurred (400, 401, 403, 404, 405, 422, 500, etc.)
        return redirect()->back()
            ->withInput()
            ->with('error', $result['message'] ?? __('Kuna hitilafu imetokea wakati wa kuanzisha malipo. Tafadhali jaribu tena.'));
    }

    // ==========================================
    // ADMIN SUBSCRIPTION MANAGEMENT
    // ==========================================

    public function adminIndex(Request $request)
    {
        $status = $request->input('status');

        $query = Subscription::with(['user.technicianProfile', 'plan']);

        if ($status) {
            $query->where('status', $status);
        }

        $subscriptions = $query->latest('started_at')->paginate(15)->withQueryString();

        $pendingPaymentsCount = SubscriptionPayment::where('status', 'pending')->count();

        $stats = [
            'total_technicians' => User::where('role', 'technician')->count(),
            'active_subs' => Subscription::whereIn('status', ['active', 'free_trial'])->where('expires_at', '>', now())->count(),
            'expired_subs' => Subscription::where('status', 'expired')->orWhere(function ($q) {
                $q->whereIn('status', ['active', 'free_trial'])->where('expires_at', '<=', now());
            })->count(),
            'pending_verifications' => $pendingPaymentsCount,
            'monthly_revenue' => SubscriptionPayment::where('status', 'success')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'status'));
    }

    public function plansIndex()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->orderBy('display_order')->get();
        return view('admin.subscriptions.plans', compact('plans'));
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subscription_plans,name',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'request_limit' => 'required|integer|min:0',
            'contact_access' => 'required|boolean',
            'priority_listing' => 'required|boolean',
            'display_order' => 'nullable|integer',
        ]);

        $featuresArray = !empty($validated['features']) 
            ? array_filter(array_map('trim', explode("\n", $validated['features'])))
            : [];

        $plan = SubscriptionPlan::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'price' => $validated['price'],
            'currency' => 'TZS',
            'duration_days' => $validated['duration_days'],
            'description' => $validated['description'],
            'features' => $featuresArray,
            'request_limit' => $validated['request_limit'],
            'contact_access' => $validated['contact_access'],
            'priority_listing' => $validated['priority_listing'],
            'is_active' => true,
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        AuditLog::log('create_subscription_plan', "Admin created subscription plan {$plan->name} ({$plan->formatted_price})", 'SubscriptionPlan', $plan->id);

        return back()->with('success', "Plan {$plan->name} created successfully.");
    }

    public function updatePlan(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subscription_plans,name,' . $plan->id,
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'request_limit' => 'required|integer|min:0',
            'contact_access' => 'required|boolean',
            'priority_listing' => 'required|boolean',
            'display_order' => 'nullable|integer',
        ]);

        $featuresArray = !empty($validated['features']) 
            ? array_filter(array_map('trim', explode("\n", $validated['features'])))
            : [];

        $plan->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'],
            'description' => $validated['description'],
            'features' => $featuresArray,
            'request_limit' => $validated['request_limit'],
            'contact_access' => $validated['contact_access'],
            'priority_listing' => $validated['priority_listing'],
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        AuditLog::log('update_subscription_plan', "Admin updated subscription plan {$plan->name}", 'SubscriptionPlan', $plan->id);

        return back()->with('success', "Plan {$plan->name} updated successfully.");
    }

    public function togglePlanStatus($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->update(['is_active' => !$plan->is_active]);

        $statusStr = $plan->is_active ? 'activated' : 'disabled';
        AuditLog::log('toggle_subscription_plan', "Admin {$statusStr} subscription plan {$plan->name}", 'SubscriptionPlan', $plan->id);

        return back()->with('success', "Plan {$plan->name} {$statusStr}.");
    }

    public function paymentsIndex(Request $request)
    {
        $status = $request->input('status');

        $query = SubscriptionPayment::with(['user', 'plan']);

        if ($status) {
            $query->where('status', $status);
        }

        $payments = $query->latest()->paginate(20, ['*'], 'tech_page')->withQueryString();
        $pendingCount = SubscriptionPayment::where('status', 'pending')->count();

        $clientRequests = \App\Models\ServiceRequest::with(['client', 'technician', 'service'])
            ->whereNotNull('connection_fee_reference')
            ->latest()
            ->paginate(20, ['*'], 'client_page');

        $pendingClientFeeCount = \App\Models\ServiceRequest::where('connection_fee_status', 'pending')->count();
        $pendingRequestsList = \App\Models\ServiceRequest::with(['client', 'technician', 'service'])
            ->latest()
            ->take(30)
            ->get();
        $allTechnicians = User::where('role', 'technician')->with('technicianProfile')->get();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('admin.subscriptions.payments', compact('payments', 'clientRequests', 'status', 'pendingCount', 'pendingClientFeeCount', 'allTechnicians', 'plans', 'pendingRequestsList'));
    }

    public function manualActivate(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'days' => 'nullable|integer|min:1|max:365',
        ]);

        $admin = Auth::user();
        $technician = User::findOrFail($validated['user_id']);
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
        $days = (int) ($validated['days'] ?? $plan->duration_days);

        $ref = 'ADM-MANUAL-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        DB::transaction(function () use ($technician, $plan, $ref, $days) {
            $payment = SubscriptionPayment::create([
                'user_id' => $technician->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'payment_reference' => $ref,
                'payment_method' => 'admin_manual',
                'status' => 'success',
                'paid_at' => now(),
                'admin_notes' => 'Admin manual activation / verified via WhatsApp or Cash',
            ]);

            Subscription::where('user_id', $technician->id)->update(['status' => 'expired']);

            $sub = Subscription::create([
                'user_id' => $technician->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => now()->addDays($days),
                'auto_renew' => false,
            ]);

            $payment->update(['subscription_id' => $sub->id]);
        });

        AuditLog::log('manual_activate_subscription', "Admin manually activated {$plan->name} for {$technician->full_name}", 'User', $technician->id);

        Notification::send(
            $technician->id,
            'subscription_activated',
            "Your {$plan->name} Subscription is Now Active!",
            "Admin has verified your payment and activated your {$plan->name} plan.",
            route('technician.dashboard')
        );

        return back()->with('success', "Subscription ya {$technician->full_name} ({$plan->name}) IMEWASHWA kikamilifu kwa siku {$days}!");
    }

    public function togglePaymentStatus(Request $request, $id)
    {
        $payment = SubscriptionPayment::with(['user', 'plan'])->findOrFail($id);
        $newStatus = $payment->status === 'success' ? 'failed' : 'success';

        DB::transaction(function () use ($payment, $newStatus) {
            $payment->update([
                'status' => $newStatus,
                'paid_at' => $newStatus === 'success' ? now() : null,
            ]);

            if ($newStatus === 'success') {
                SubscriptionService::activateSubscription(
                    $payment->user,
                    $payment->plan,
                    $payment->payment_method,
                    $payment->payment_reference
                );
            } else {
                Subscription::where('user_id', $payment->user_id)->update(['status' => 'expired']);
            }
        });

        return back()->with('success', "Hali ya malipo ya {$payment->user->full_name} imebadilishwa kuwa " . strtoupper($newStatus));
    }

    public function verifyPayment(Request $request, $id)
    {
        $admin = Auth::user();
        $payment = SubscriptionPayment::with(['user', 'plan'])->findOrFail($id);

        if ($payment->status === 'success') {
            return back()->with('info', 'Payment has already been verified.');
        }

        DB::transaction(function () use ($payment, $admin) {
            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
            ]);

            // Activate subscription
            SubscriptionService::activateSubscription(
                $payment->user,
                $payment->plan,
                $payment->payment_method,
                $payment->payment_reference
            );
        });

        AuditLog::log('verify_subscription_payment', "Admin {$admin->full_name} verified payment {$payment->payment_reference} for {$payment->user->full_name}", 'SubscriptionPayment', $payment->id);

        return back()->with('success', "Malipo ya Fundi ({$payment->payment_reference}) YAMETHIBITISHWA. Subscription yake sasa ni ACTIVE!");
    }

    public function verifyClientFee(Request $request, $id)
    {
        $admin = Auth::user();
        $serviceRequest = \App\Models\ServiceRequest::with(['client', 'technician'])->findOrFail($id);

        $serviceRequest->update([
            'connection_fee_status' => 'paid',
            'status' => 'pending',
        ]);

        AuditLog::log('verify_client_connection_fee', "Admin verified connection fee for {$serviceRequest->reference_no}", 'ServiceRequest', $serviceRequest->id);

        Notification::send(
            $serviceRequest->technician_id,
            'new_request',
            'New Verified Client Request Received!',
            "Client {$serviceRequest->client->full_name} has paid the connection fee for request ({$serviceRequest->reference_no}). You can now review and accept the job.",
            route('technician.requests.show', $serviceRequest->id)
        );

        $feeAmt = number_format((int) ($serviceRequest->connection_fee ?? 500), 0);
        Notification::send(
            $serviceRequest->client_id,
            'fee_verified',
            'Payment Verified! Request Sent to Technician',
            "Your TZS {$feeAmt} connection fee for request {$serviceRequest->reference_no} has been verified by Admin and sent to {$serviceRequest->technician->full_name}.",
            route('client.requests.show', $serviceRequest->id)
        );

        return back()->with('success', "Ada ya Mteja (TZS {$feeAmt}) kwa ombi #{$serviceRequest->reference_no} IMETHIBITISHWA na ombi limetumwa kwa fundi!");
    }

    public function rejectClientFee(Request $request, $id)
    {
        $serviceRequest = \App\Models\ServiceRequest::findOrFail($id);
        $serviceRequest->update([
            'connection_fee_status' => 'failed',
            'status' => 'declined',
        ]);

        return back()->with('info', "Ada ya mteja kwa ombi #{$serviceRequest->reference_no} imekataliwa.");
    }

    public function toggleClientFeeStatus(Request $request, $id)
    {
        $serviceRequest = \App\Models\ServiceRequest::with(['client', 'technician'])->findOrFail($id);
        $newStatus = ($serviceRequest->connection_fee_status === 'paid') ? 'pending' : 'paid';

        $serviceRequest->update([
            'connection_fee_status' => $newStatus,
        ]);

        if ($newStatus === 'paid') {
            Notification::send(
                $serviceRequest->technician_id,
                'new_request',
                'Service Request Verified by Admin',
                "Admin has verified request ({$serviceRequest->reference_no}) from client {$serviceRequest->client->full_name}.",
                route('technician.requests.show', $serviceRequest->id)
            );
        }

        AuditLog::log('toggle_client_fee_status', "Admin changed connection fee status of {$serviceRequest->reference_no} to {$newStatus}", 'ServiceRequest', $serviceRequest->id);

        return back()->with('success', "Hali ya ada ya ombi #{$serviceRequest->reference_no} imebadilishwa kuwa " . strtoupper($newStatus));
    }

    public function rejectPayment(Request $request, $id)
    {
        $payment = SubscriptionPayment::findOrFail($id);
        $payment->update(['status' => 'failed']);

        AuditLog::log('reject_subscription_payment', "Admin rejected payment {$payment->payment_reference}", 'SubscriptionPayment', $payment->id);

        Notification::send(
            $payment->user_id,
            'payment_rejected',
            'Subscription Payment Rejected',
            "Your subscription payment ({$payment->payment_reference}) was rejected. Please resubmit with valid transaction proof.",
            route('technician.subscription')
        );

        return back()->with('info', "Malipo {$payment->payment_reference} yamewekwa kama yaliyoshindikana.");
    }

    public function revenueReport()
    {
        $now = now();

        $subRevenue = SubscriptionPayment::where('status', 'success')->sum('amount');
        $clientConnectionRevenue = \App\Models\ServiceRequest::where('connection_fee_status', 'paid')->sum('connection_fee');
        $totalProfit = $subRevenue + $clientConnectionRevenue;

        $revenue = [
            'today' => SubscriptionPayment::where('status', 'success')->whereDate('created_at', $now->toDateString())->sum('amount') + \App\Models\ServiceRequest::where('connection_fee_status', 'paid')->whereDate('created_at', $now->toDateString())->sum('connection_fee'),
            'this_week' => SubscriptionPayment::where('status', 'success')->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()])->sum('amount'),
            'this_month' => SubscriptionPayment::where('status', 'success')->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('amount') + \App\Models\ServiceRequest::where('connection_fee_status', 'paid')->whereMonth('created_at', $now->month)->sum('connection_fee'),
            'this_year' => SubscriptionPayment::where('status', 'success')->whereYear('created_at', $now->year)->sum('amount'),
            'all_time' => $totalProfit,
            'sub_revenue' => $subRevenue,
            'client_connection_revenue' => $clientConnectionRevenue,
        ];

        $planBreakdown = SubscriptionPlan::withCount(['subscriptions', 'payments'])
            ->withSum('payments', 'amount')
            ->get();

        $recentPayments = SubscriptionPayment::with(['user', 'plan'])->where('status', 'success')->latest()->take(10)->get();
        $recentRequests = \App\Models\ServiceRequest::with(['client', 'technician', 'service'])->where('connection_fee_status', 'paid')->latest()->take(10)->get();

        return view('admin.subscriptions.revenue', compact('revenue', 'planBreakdown', 'recentPayments', 'recentRequests', 'subRevenue', 'clientConnectionRevenue', 'totalProfit'));
    }

    public function exportRevenueCsv()
    {
        $filename = 'fundi_financial_revenue_statement_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

            fputcsv($handle, ['FUNDI PLATFORM - FINANCIAL REVENUE STATEMENT']);
            fputcsv($handle, ['Generated At', date('Y-m-d H:i:s'), 'Generated By', Auth::user()->full_name]);
            fputcsv($handle, []);

            // Summary Totals
            $subRevenue = SubscriptionPayment::where('status', 'success')->sum('amount');
            $clientConnectionRevenue = \App\Models\ServiceRequest::where('connection_fee_status', 'paid')->sum('connection_fee');
            $totalProfit = $subRevenue + $clientConnectionRevenue;

            fputcsv($handle, ['FINANCIAL METRIC', 'AMOUNT (TZS)']);
            fputcsv($handle, ['Technician Subscription Revenue', number_format($subRevenue, 2)]);
            fputcsv($handle, ['Client Connection Fees Revenue', number_format($clientConnectionRevenue, 2)]);
            fputcsv($handle, ['Total Platform Revenue', number_format($totalProfit, 2)]);
            fputcsv($handle, []);

            // Section 1: Technician Subscriptions Transactions
            fputcsv($handle, ['STREAM 1: TECHNICIAN SUBSCRIPTION PAYMENTS']);
            fputcsv($handle, ['Date & Time', 'Technician Name', 'Phone', 'Plan', 'Amount (TZS)', 'Payment Method', 'Reference No', 'Status']);

            $payments = SubscriptionPayment::with(['user', 'plan'])->latest()->get();
            foreach ($payments as $p) {
                fputcsv($handle, [
                    $p->created_at->format('Y-m-d H:i:s'),
                    $p->user->full_name ?? 'N/A',
                    $p->user->phone ?? 'N/A',
                    $p->plan->name ?? 'N/A',
                    number_format($p->amount, 2),
                    $p->payment_method ?? 'N/A',
                    $p->payment_reference ?? 'N/A',
                    strtoupper($p->status),
                ]);
            }
            fputcsv($handle, []);

            // Section 2: Client Connection Fees Transactions
            fputcsv($handle, ['STREAM 2: CLIENT CONNECTION FEE TRANSACTIONS']);
            fputcsv($handle, ['Date & Time', 'Request Ref', 'Client Name', 'Client Phone', 'Technician Name', 'Service', 'Fee Amount (TZS)', 'Payment Ref', 'Status']);

            $requests = \App\Models\ServiceRequest::with(['client', 'technician', 'service'])
                ->whereNotNull('connection_fee_reference')
                ->latest()
                ->get();

            foreach ($requests as $r) {
                fputcsv($handle, [
                    $r->created_at->format('Y-m-d H:i:s'),
                    $r->reference_no,
                    $r->client->full_name ?? 'N/A',
                    $r->client->phone ?? 'N/A',
                    $r->technician->full_name ?? 'N/A',
                    $r->service->name ?? 'N/A',
                    number_format($r->connection_fee ?? 500, 2),
                    $r->connection_fee_reference ?? 'N/A',
                    strtoupper($r->connection_fee_status ?? 'paid'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
