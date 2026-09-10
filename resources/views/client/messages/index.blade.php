@extends('layouts.app')

@section('title', __('Messages'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-3 min-h-[600px]">
        
        <!-- Left: Conversations List (Scoped to Requests) -->
        <div class="border-r border-slate-200 flex flex-col">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-base font-bold text-slate-900">{{ __('Job Conversations') }}</h2>
                <p class="text-xs text-slate-500">{{ __('Messages are tied to specific service requests') }}</p>
            </div>

            <div class="flex-1 overflow-y-auto divide-y divide-slate-100 custom-scrollbar">
                @forelse($requests as $req)
                @php $otherUser = auth()->user()->isClient() ? $req->technician : $req->client; @endphp
                <a href="{{ route('messages.index', ['request_id' => $req->id]) }}" class="block p-4 hover:bg-slate-50 transition {{ $activeRequest && $activeRequest->id === $req->id ? 'bg-brand-50/80 border-l-4 border-brand-700' : '' }}">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-900 text-white font-bold text-xs flex items-center justify-center flex-shrink-0">
                            {{ $otherUser->initials }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold text-slate-900 truncate">{{ $otherUser->full_name }}</h3>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $req->reference_no }}</span>
                            </div>
                            <p class="text-xs text-brand-700 font-medium truncate">{{ __($req->service->name) }}</p>
                            <p class="text-xs text-slate-500 truncate mt-1">
                                {{ $req->messages->last()?->message_text ?? __('Start a conversation...') }}
                            </p>
                        </div>
                    </div>
                </a>
                @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    {{ __('No active conversations yet.') }}
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Active Chat Window -->
        <div class="md:col-span-2 flex flex-col justify-between bg-slate-50/30">
            @if($activeRequest)
                @php $otherUser = auth()->user()->isClient() ? $activeRequest->technician : $activeRequest->client; @endphp
                
                <!-- Chat Header -->
                <div class="p-4 bg-white border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-900 text-white font-bold text-xs flex items-center justify-center">
                            {{ $otherUser->initials }}
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">{{ $otherUser->full_name }}</h3>
                            <p class="text-xs text-slate-500">{{ __($activeRequest->service->name) }} • <span class="font-mono">{{ $activeRequest->reference_no }}</span></p>
                        </div>
                    </div>

                    <a href="{{ auth()->user()->isClient() ? route('client.requests.show', $activeRequest->id) : route('technician.requests.show', $activeRequest->id) }}" class="text-xs font-bold text-brand-700 hover:underline">
                        {{ __('View Request Details') }} &rarr;
                    </a>
                </div>

                <!-- Messages Feed -->
                <div class="flex-1 p-6 overflow-y-auto space-y-3 custom-scrollbar">
                    @forelse($messages as $msg)
                        @php $isMe = $msg->sender_id === auth()->id(); @endphp
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            <div class="flex items-center space-x-1.5 text-[10px] text-slate-400 mb-0.5">
                                <span class="font-bold {{ $isMe ? 'text-brand-700' : 'text-slate-600' }}">{{ $msg->sender->full_name }}</span>
                                <span>• {{ $msg->sent_at->format('H:i, d M') }}</span>
                            </div>
                            <div class="p-3.5 rounded-2xl max-w-[75%] text-xs leading-relaxed {{ $isMe ? 'bg-brand-700 text-white rounded-tr-none shadow-sm' : 'bg-white text-slate-800 rounded-tl-none border border-slate-200 shadow-sm' }}">
                                {{ $msg->message_text }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-400 text-xs">
                            {{ __('No messages in this request yet. Send the first message below.') }}
                        </div>
                    @endforelse
                </div>

                <!-- Input Box -->
                @if($activeRequest->connection_fee_status === 'paid')
                    <div class="p-4 bg-white border-t border-slate-200">
                        <form method="POST" action="{{ route('messages.store', $activeRequest->id) }}" class="flex items-center space-x-2">
                            @csrf
                            <input type="text" name="message_text" required placeholder="{{ __('Type your message...') }}" class="flex-1 py-3 px-4 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-brand-500 bg-slate-50">
                            <button type="submit" class="px-6 py-3 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs shadow transition flex items-center space-x-1">
                                <span>{{ __('Send') }}</span>
                                <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="p-5 bg-amber-50 border-t border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-700 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-amber-950">{{ __('Chat Locked: TZS 2,000 Connection Fee Pending Verification') }}</p>
                                <p class="text-[11px] text-amber-800">{{ __('You cannot message the technician until Admin confirms the connection fee payment for this request.') }}</p>
                            </div>
                        </div>
                        <a href="https://wa.me/255675315279?text={{ urlencode('Habari Admin wa FUNDI, nimelipia ada ya TZS 2,000 kwa ombi ' . $activeRequest->reference_no) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center space-x-1.5 flex-shrink-0">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <span>{{ __('Contact Admin') }}</span>
                        </a>
                    </div>
                @endif
            @else
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center text-slate-400">
                    <i data-lucide="message-square" class="w-12 h-12 mb-3 text-slate-300"></i>
                    <p class="text-sm font-bold text-slate-700">{{ __('Select a Job Conversation') }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ __('Choose a conversation from the left to view messages and discuss job details.') }}</p>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
