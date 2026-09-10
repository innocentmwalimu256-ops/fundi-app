<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Notification;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Fetch all conversations user is part of
        $requests = ServiceRequest::where(function ($q) use ($user) {
                $q->where('client_id', $user->id)
                  ->orWhere('technician_id', $user->id);
            })
            ->with(['client', 'technician.technicianProfile', 'service', 'messages'])
            ->latest('updated_at')
            ->get();

        $activeRequestId = $request->input('request_id', $requests->first()?->id);
        $activeRequest = null;
        $messages = collect();

        if ($activeRequestId) {
            $activeRequest = ServiceRequest::where('id', $activeRequestId)
                ->where(function ($q) use ($user) {
                    $q->where('client_id', $user->id)
                      ->orWhere('technician_id', $user->id);
                })
                ->with(['client', 'technician.technicianProfile', 'service', 'messages.sender'])
                ->first();

            if ($activeRequest) {
                // Mark unread messages as read
                Message::where('request_id', $activeRequest->id)
                    ->where('sender_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                $messages = $activeRequest->messages;
            }
        }

        $view = $user->isTechnician() ? 'technician.messages.index' : ($user->isAdmin() ? 'admin.messages.index' : 'client.messages.index');

        return view($view, compact('requests', 'activeRequest', 'messages'));
    }

    public function store(Request $request, $requestId)
    {
        $user = Auth::user();
        $serviceRequest = ServiceRequest::where('id', $requestId)
            ->where(function ($q) use ($user) {
                $q->where('client_id', $user->id)
                  ->orWhere('technician_id', $user->id);
            })
            ->firstOrFail();

        if ($serviceRequest->connection_fee_status !== 'paid' && !in_array($serviceRequest->status, ['accepted', 'quotation_pending', 'quotation_accepted', 'scheduled', 'on_the_way', 'in_progress', 'completed', 'client_confirmed', 'reviewed'])) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Huwezi kutuma ujumbe mpaka ada ya kuunganishwa ya TZS 2,000 ithibitishwe.',
                ], 403);
            }
            return back()->with('error', 'Huwezi kutuma ujumbe mpaka ada ya kuunganishwa ya TZS 2,000 ithibitishwe.');
        }

        $validated = $request->validate([
            'message_text' => 'required|string|max:3000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('message_attachments', 'public');
        }

        $message = Message::create([
            'request_id' => $serviceRequest->id,
            'sender_id' => $user->id,
            'message_text' => $validated['message_text'],
            'attachment_path' => $attachmentPath,
            'is_read' => false,
            'sent_at' => now(),
        ]);

        // Send notification to other party
        $recipientId = ($user->id === $serviceRequest->client_id) ? $serviceRequest->technician_id : $serviceRequest->client_id;
        $url = route('messages.index', ['request_id' => $serviceRequest->id]);

        Notification::send(
            $recipientId,
            'new_message',
            "New Message from {$user->full_name}",
            substr($validated['message_text'], 0, 80) . (strlen($validated['message_text']) > 80 ? '...' : ''),
            $url
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'text' => $message->message_text,
                    'sender' => $user->full_name,
                    'is_me' => true,
                    'time' => $message->sent_at->format('H:i'),
                ]
            ]);
        }

        return back()->with('success', 'Message sent.');
    }
}
