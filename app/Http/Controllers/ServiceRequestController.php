<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CancellationRecord;
use App\Models\Complaint;
use App\Models\ComplaintEvidence;
use App\Models\Notification;
use App\Models\Quotation;
use App\Models\RequestImage;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceJob;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->input('tab', 'all');

        $query = ServiceRequest::where('client_id', $user->id)
            ->with(['technician.technicianProfile', 'service', 'latestQuotation', 'job']);

        if ($tab === 'pending') {
            $query->whereIn('status', ['requested', 'pending', 'accepted', 'quotation_pending']);
        } elseif ($tab === 'active') {
            $query->whereIn('status', ['quotation_accepted', 'scheduled', 'on_the_way', 'in_progress', 'completed']);
        } elseif ($tab === 'completed') {
            $query->whereIn('status', ['client_confirmed', 'reviewed']);
        } elseif ($tab === 'cancelled') {
            $query->whereIn('status', ['declined', 'cancelled']);
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all' => ServiceRequest::where('client_id', $user->id)->count(),
            'pending' => ServiceRequest::where('client_id', $user->id)->whereIn('status', ['requested', 'pending', 'accepted', 'quotation_pending'])->count(),
            'active' => ServiceRequest::where('client_id', $user->id)->whereIn('status', ['quotation_accepted', 'scheduled', 'on_the_way', 'in_progress', 'completed'])->count(),
            'completed' => ServiceRequest::where('client_id', $user->id)->whereIn('status', ['client_confirmed', 'reviewed'])->count(),
            'cancelled' => ServiceRequest::where('client_id', $user->id)->whereIn('status', ['declined', 'cancelled'])->count(),
        ];

        return view('client.requests.index', compact('requests', 'tab', 'counts'));
    }

    public function create(Request $request)
    {
        $technicianId = $request->input('technician_id');
        $serviceId = $request->input('service_id');

        $technician = null;
        if ($technicianId) {
            $technician = User::where('role', 'technician')
                ->where('status', 'active')
                ->with(['technicianProfile', 'services'])
                ->findOrFail($technicianId);
        }

        $services = Service::where('status', 'active')->get();
        $selectedService = $serviceId ? Service::find($serviceId) : ($technician && $technician->services->isNotEmpty() ? $technician->services->first() : null);

        $connectionFee = 2000;
        $admin = User::where('role', 'admin')->first();
        $adminPhone = $admin ? $admin->phone : '0675315279';
        $cleanPhone = preg_replace('/[^0-9]/', '', $adminPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '255' . substr($cleanPhone, 1);
        } elseif (!str_starts_with($cleanPhone, '255')) {
            $cleanPhone = '255' . $cleanPhone;
        }

        $techName = $technician ? $technician->full_name : 'Fundi';
        $waMsg = "Habari Admin wa FUNDI, mimi ni Mteja " . Auth::user()->full_name . ". Nahitaji kulipia Ada ya Kuunganishwa na Fundi {$techName} (TZS 2,000). Naomba maelekezo ya malipo.";
        $adminWhatsappUrl = 'https://wa.me/' . $cleanPhone . '?text=' . urlencode($waMsg);

        return view('client.requests.create', compact('technician', 'services', 'selectedService', 'connectionFee', 'adminWhatsappUrl', 'adminPhone'));
    }

    public function store(Request $request)
    {
        $client = Auth::user();

        $validated = $request->validate([
            'technician_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'description' => 'required|string|min:10|max:3000',
            'location' => 'required|string|max:255',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'nullable|string',
            'urgency' => 'required|in:low,normal,high,urgent',
            'payment_reference' => 'nullable|string|max:100',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $technician = User::where('role', 'technician')->findOrFail($validated['technician_id']);

        $serviceRequest = DB::transaction(function () use ($client, $technician, $validated, $request) {
            $ref = ServiceRequest::generateReferenceNo();
            $paymentRef = $request->input('payment_reference') ?: ('REQ-PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)));

            $req = ServiceRequest::create([
                'reference_no' => $ref,
                'client_id' => $client->id,
                'technician_id' => $technician->id,
                'service_id' => $validated['service_id'],
                'description' => $validated['description'],
                'location' => $validated['location'],
                'preferred_date' => $validated['preferred_date'],
                'preferred_time' => $validated['preferred_time'],
                'urgency' => $validated['urgency'],
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'connection_fee' => 2000,
                'connection_fee_status' => 'paid',
                'connection_fee_reference' => $paymentRef,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('request_images', 'public');
                    RequestImage::create([
                        'request_id' => $req->id,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            return $req;
        });

        AuditLog::log('create_request', "Client {$client->full_name} paid connection fee TZS 2,000 and created service request {$serviceRequest->reference_no}", 'ServiceRequest', $serviceRequest->id);

        Notification::send(
            $technician->id,
            'new_request',
            'New Verified Client Service Request Received!',
            "Client {$client->full_name} has paid the connection fee and requested your service for {$serviceRequest->reference_no}.",
            route('technician.requests.show', $serviceRequest->id)
        );

        return redirect()->route('client.requests.show', $serviceRequest->id)
            ->with('success', "Ombi lako limetumwa kwa fundi {$technician->full_name} na ada ya kuunganishwa ya TZS 2,000 imelipwa moja kwa moja!");
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $request = ServiceRequest::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('client_id', $user->id)
                  ->orWhere('technician_id', $user->id);
            })
            ->with([
                'client',
                'technician.technicianProfile',
                'technician.serviceAreas',
                'service',
                'images',
                'quotations',
                'latestQuotation',
                'job',
                'messages.sender',
                'review',
                'complaint.evidences',
                'cancellationRecord.canceller'
            ])
            ->firstOrFail();

        $canViewContact = \App\Services\SubscriptionService::canViewProtectedContact($user, $request);

        return view('client.requests.show', compact('request', 'canViewContact'));
    }

    public function receipt($id)
    {
        $user = Auth::user();

        $request = ServiceRequest::where('id', $id)
            ->where(function ($q) use ($user) {
                if ($user->isAdmin()) return;
                $q->where('client_id', $user->id)
                  ->orWhere('technician_id', $user->id);
            })
            ->with(['client', 'technician.technicianProfile', 'service', 'latestQuotation', 'job'])
            ->firstOrFail();

        return view('requests.receipt', compact('request'));
    }

    public function acceptQuotation(Request $request, $id)
    {
        $client = Auth::user();
        $serviceRequest = ServiceRequest::where('client_id', $client->id)->findOrFail($id);

        $quotation = $serviceRequest->latestQuotation;
        if (!$quotation || $quotation->status !== 'pending') {
            return back()->with('error', 'No active pending quotation found to accept.');
        }

        DB::transaction(function () use ($serviceRequest, $quotation, $client) {
            $quotation->update(['status' => 'accepted']);
            $serviceRequest->update(['status' => 'scheduled']);

            ServiceJob::create([
                'request_id' => $serviceRequest->id,
                'scheduled_date' => $serviceRequest->preferred_date,
                'scheduled_time' => $serviceRequest->preferred_time ?: '09:00:00',
                'status' => 'scheduled',
            ]);
        });

        AuditLog::log('accept_quotation', "Client {$client->full_name} accepted quotation for request {$serviceRequest->reference_no}", 'Quotation', $quotation->id);

        Notification::send(
            $serviceRequest->technician_id,
            'quotation_accepted',
            'Quotation Accepted!',
            "Client {$client->full_name} accepted your quotation of {$quotation->formatted_total}. The job is now scheduled.",
            route('technician.jobs.show', $serviceRequest->id)
        );

        return back()->with('success', 'Quotation accepted! The job has been scheduled.');
    }

    public function rejectQuotation(Request $request, $id)
    {
        $client = Auth::user();
        $serviceRequest = ServiceRequest::where('client_id', $client->id)->findOrFail($id);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $quotation = $serviceRequest->latestQuotation;
        if (!$quotation || $quotation->status !== 'pending') {
            return back()->with('error', 'No active quotation found.');
        }

        $quotation->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        $serviceRequest->update(['status' => 'accepted']);

        AuditLog::log('reject_quotation', "Client rejected quotation for request {$serviceRequest->reference_no}", 'Quotation', $quotation->id);

        Notification::send(
            $serviceRequest->technician_id,
            'quotation_rejected',
            'Quotation Rejected',
            "Client declined your quotation. Reason: {$request->input('rejection_reason')}",
            route('technician.requests.show', $serviceRequest->id)
        );

        return back()->with('info', 'Quotation declined. The technician has been notified.');
    }

    public function cancelRequest(Request $request, $id)
    {
        $user = Auth::user();
        $serviceRequest = ServiceRequest::where(function ($q) use ($user) {
            $q->where('client_id', $user->id)
              ->orWhere('technician_id', $user->id);
        })->findOrFail($id);

        if (!$serviceRequest->canBeCancelled()) {
            return back()->with('error', 'This request can no longer be cancelled at its current status.');
        }

        $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $reason = $request->input('reason');
        $desc = $request->input('description');

        DB::transaction(function () use ($serviceRequest, $user, $reason, $desc) {
            $serviceRequest->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason . ($desc ? " - {$desc}" : ""),
                'cancelled_by' => $user->id,
            ]);

            CancellationRecord::create([
                'request_id' => $serviceRequest->id,
                'cancelled_by' => $user->id,
                'reason' => $reason,
                'description' => $desc,
            ]);

            if ($serviceRequest->job) {
                $serviceRequest->job->update(['status' => 'cancelled']);
            }
        });

        AuditLog::log('cancel_request', "Request {$serviceRequest->reference_no} cancelled by {$user->full_name}. Reason: {$reason}", 'ServiceRequest', $serviceRequest->id);

        $recipientId = ($user->id === $serviceRequest->client_id) ? $serviceRequest->technician_id : $serviceRequest->client_id;
        Notification::send(
            $recipientId,
            'request_cancelled',
            'Service Request Cancelled',
            "Service request {$serviceRequest->reference_no} was cancelled. Reason: {$reason}"
        );

        return back()->with('info', 'The service request has been cancelled.');
    }

    public function confirmCompletion(Request $request, $id)
    {
        $client = Auth::user();
        $serviceRequest = ServiceRequest::where('client_id', $client->id)->findOrFail($id);

        if ($serviceRequest->status !== 'completed') {
            return back()->with('error', 'The technician must mark the job as completed before you can confirm.');
        }

        $serviceRequest->update([
            'status' => 'client_confirmed',
            'payment_status' => 'paid',
        ]);

        if ($serviceRequest->job) {
            $serviceRequest->job->update(['confirmed_at' => now()]);
        }

        if ($serviceRequest->technician->technicianProfile) {
            $serviceRequest->technician->technicianProfile->recalculateRating();
        }

        AuditLog::log('confirm_completion', "Client confirmed completion and payment settlement for request {$serviceRequest->reference_no}", 'ServiceRequest', $serviceRequest->id);

        Notification::send(
            $serviceRequest->technician_id,
            'job_confirmed',
            'Job Completion & Payment Confirmed!',
            "Client {$client->full_name} has verified and confirmed completion of {$serviceRequest->reference_no}."
        );

        return back()->with('success', 'Completion confirmed! Please take a moment to rate and review your technician.');
    }

    public function storeReview(Request $request, $id)
    {
        $client = Auth::user();
        $serviceRequest = ServiceRequest::where('client_id', $client->id)->findOrFail($id);

        if ($serviceRequest->review) {
            return back()->with('info', 'Umeshawasilisha maoni na nyota kwa kazi hii tayari.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'quality' => 'nullable|integer|min:1|max:5',
            'professionalism' => 'nullable|integer|min:1|max:5',
            'communication' => 'nullable|integer|min:1|max:5',
            'punctuality' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = DB::transaction(function () use ($serviceRequest, $client, $validated) {
            $rev = Review::create([
                'request_id' => $serviceRequest->id,
                'client_id' => $client->id,
                'technician_id' => $serviceRequest->technician_id,
                'rating' => $validated['rating'],
                'quality' => $validated['quality'] ?? $validated['rating'],
                'professionalism' => $validated['professionalism'] ?? $validated['rating'],
                'communication' => $validated['communication'] ?? $validated['rating'],
                'punctuality' => $validated['punctuality'] ?? $validated['rating'],
                'comment' => $validated['comment'] ?? 'Kazi nzuri sana!',
                'status' => 'published',
            ]);

            $serviceRequest->update([
                'status' => 'completed',
                'payment_status' => 'paid',
            ]);

            if ($serviceRequest->technician && $serviceRequest->technician->technicianProfile) {
                $serviceRequest->technician->technicianProfile->recalculateRating();
            }

            return $rev;
        });

        AuditLog::log('submit_review', "Client {$client->full_name} submitted {$review->rating}-star review for technician {$serviceRequest->technician->full_name}", 'Review', $review->id);

        Notification::send(
            $serviceRequest->technician_id,
            'new_review',
            'New Verified Client Rating & Review Received!',
            "Client {$client->full_name} gave you {$review->rating} stars: \"{$review->comment}\"",
            route('technician.requests.show', $serviceRequest->id)
        );

        return back()->with('success', "Asante sana! Umempa fundi {$serviceRequest->technician->full_name} nyota {$validated['rating']} na maoni yako yamewekwa kwenye profile yake!");
    }

    public function storeComplaint(Request $request, $id)
    {
        $client = Auth::user();
        $serviceRequest = ServiceRequest::where('client_id', $client->id)->findOrFail($id);

        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'description' => 'required|string|min:20|max:3000',
            'evidence.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $complaint = Complaint::create([
            'client_id' => $client->id,
            'request_id' => $serviceRequest->id,
            'category' => $validated['category'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $path = $file->store('complaint_evidence', 'public');
                $type = str_contains($file->getMimeType(), 'pdf') ? 'document' : 'image';
                ComplaintEvidence::create([
                    'complaint_id' => $complaint->id,
                    'file_path' => $path,
                    'file_type' => $type,
                ]);
            }
        }

        AuditLog::log('file_complaint', "Client filed complaint on request {$serviceRequest->reference_no}", 'Complaint', $complaint->id);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::send(
                $admin->id,
                'new_complaint',
                'New Dispute/Complaint Filed',
                "Complaint filed for {$serviceRequest->reference_no} under category '{$complaint->category_label}'.",
                route('admin.complaints.show', $complaint->id)
            );
        }

        return back()->with('success', 'Your complaint and supporting evidence have been submitted to FUNDI administrators. We will investigate promptly.');
    }
}
