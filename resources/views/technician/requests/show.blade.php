@extends('layouts.app')

@section('title', __('Manage Request') . ' ' . $request->reference_no)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{ quoteModal: false, declineModal: false }">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('technician.requests.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-800 mb-2">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> {{ __('Back to Requests') }}
            </a>
            <div class="flex items-center space-x-3">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-mono">{{ $request->reference_no }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $request->status_badge_classes }}">
                    {{ __($request->status_label) }}
                </span>
            </div>
        </div>

        <!-- Quick actions based on status -->
        <div class="flex items-center space-x-2">
            @if(in_array($request->status, ['requested', 'pending']))
                <form method="POST" action="{{ route('technician.requests.accept', $request->id) }}">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center space-x-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>{{ __('Accept Request') }}</span>
                    </button>
                </form>
                <button type="button" @click="declineModal = true" class="px-4 py-2.5 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs rounded-xl border border-rose-200 transition">
                    {{ __('Decline') }}
                </button>
            @elseif(in_array($request->status, ['accepted', 'quotation_pending']))
                <button type="button" @click="quoteModal = true" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center space-x-1.5">
                    <i data-lucide="calculator" class="w-4 h-4"></i>
                    <span>{{ $request->latestQuotation ? __('Update / Re-Send Quotation') : __('Create Itemized Quotation') }}</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Details, Quotation, Status Actions, Chat -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Problem Description & Photos (Section 19) -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                <h2 class="text-base font-bold uppercase tracking-wider text-slate-900 flex items-center">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2 text-brand-700"></i> {{ __('Client Problem Description') }}
                </h2>
                
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-sm text-slate-800 leading-relaxed">
                    {{ $request->description }}
                </div>

                @if($request->images->isNotEmpty())
                <div>
                    <p class="text-xs font-bold text-slate-700 mb-2">{{ __('Attached Problem Photos') }} ({{ $request->images->count() }})</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($request->images as $img)
                        <a href="{{ asset('storage/' . $img->file_path) }}" target="_blank" class="block aspect-square rounded-2xl overflow-hidden border border-slate-200 hover:opacity-90 transition">
                            <img src="{{ asset('storage/' . $img->file_path) }}" alt="Problem Photo" class="w-full h-full object-cover">
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Current Quotation Preview (Section 22) -->
            @if($request->latestQuotation)
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold uppercase tracking-wider text-slate-900 flex items-center">
                        <i data-lucide="calculator" class="w-4 h-4 mr-2 text-indigo-600"></i> {{ __('Quotation Breakdown') }}
                    </h2>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ $request->latestQuotation->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($request->latestQuotation->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ __($request->latestQuotation->status) }}
                    </span>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs divide-y divide-slate-200">
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-600">{{ __('Labour Cost') }}</span>
                        <span class="font-bold text-slate-900">TZS {{ number_format($request->latestQuotation->labour_cost, 0) }}</span>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-600">{{ __('Materials Cost') }}</span>
                        <span class="font-bold text-slate-900">TZS {{ number_format($request->latestQuotation->materials_cost, 0) }}</span>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-600">{{ __('Transport / Logistics') }}</span>
                        <span class="font-bold text-slate-900">TZS {{ number_format($request->latestQuotation->transport_cost, 0) }}</span>
                    </div>
                    @if($request->latestQuotation->discount > 0)
                    <div class="flex justify-between py-1.5 text-emerald-700 font-bold">
                        <span>{{ __('Discount') }}</span>
                        <span>- TZS {{ number_format($request->latestQuotation->discount, 0) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between pt-3 text-base font-black text-slate-900">
                        <span>{{ __('Total Cost') }}</span>
                        <span class="text-brand-700">{{ $request->latestQuotation->formatted_total }}</span>
                    </div>
                </div>

                @if($request->latestQuotation->notes)
                <p class="text-xs text-slate-600"><strong>{{ __('Notes') }}:</strong> {{ $request->latestQuotation->notes }}</p>
                @endif
            </div>
            @endif

            <!-- Job Execution Stepper (Section 23 & 24) -->
            @if(in_array($request->status, ['quotation_accepted', 'scheduled', 'on_the_way', 'in_progress', 'completed', 'client_confirmed', 'reviewed']))
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-5">
                <h2 class="text-base font-bold uppercase tracking-wider text-slate-900 flex items-center">
                    <i data-lucide="activity" class="w-4 h-4 mr-2 text-teal-600"></i> {{ __('Job Execution Stepper') }}
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Step 1: On the way -->
                    <form method="POST" action="{{ route('technician.jobs.status', $request->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="on_the_way">
                        <button type="submit" {{ in_array($request->status, ['on_the_way', 'in_progress', 'completed', 'client_confirmed', 'reviewed']) ? 'disabled' : '' }} class="w-full p-4 rounded-2xl text-center text-xs font-bold border transition {{ in_array($request->status, ['on_the_way', 'in_progress', 'completed', 'client_confirmed', 'reviewed']) ? 'bg-teal-50 border-teal-200 text-teal-800' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                            <i data-lucide="navigation" class="w-5 h-5 mx-auto mb-1 text-teal-600"></i>
                            <span>1. {{ __("I'm On The Way") }}</span>
                        </button>
                    </form>

                    <!-- Step 2: In Progress -->
                    <form method="POST" action="{{ route('technician.jobs.status', $request->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="in_progress">
                        <button type="submit" {{ in_array($request->status, ['in_progress', 'completed', 'client_confirmed', 'reviewed']) ? 'disabled' : '' }} class="w-full p-4 rounded-2xl text-center text-xs font-bold border transition {{ in_array($request->status, ['in_progress', 'completed', 'client_confirmed', 'reviewed']) ? 'bg-indigo-50 border-indigo-200 text-indigo-800' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                            <i data-lucide="play" class="w-5 h-5 mx-auto mb-1 text-indigo-600"></i>
                            <span>2. {{ __('Start Job (In Progress)') }}</span>
                        </button>
                    </form>

                    <!-- Step 3: Completed -->
                    <form method="POST" action="{{ route('technician.jobs.status', $request->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" {{ in_array($request->status, ['completed', 'client_confirmed', 'reviewed']) ? 'disabled' : '' }} class="w-full p-4 rounded-2xl text-center text-xs font-bold border transition {{ in_array($request->status, ['completed', 'client_confirmed', 'reviewed']) ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                            <i data-lucide="check-circle" class="w-5 h-5 mx-auto mb-1 text-emerald-600"></i>
                            <span>3. {{ __('Mark as Completed') }}</span>
                        </button>
                    </form>
                </div>

                @if($request->status === 'completed')
                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-center">
                    <i data-lucide="clock" class="w-4 h-4 mr-2 flex-shrink-0 text-amber-600"></i>
                    <span>{{ __('Job marked complete! Waiting for client inspection and completion confirmation.') }}</span>
                </div>
                @endif
            </div>
            @endif

            <!-- Request Scoped Chat Box -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="message-square" class="w-5 h-5 text-brand-700"></i>
                        <h2 class="text-base font-bold text-slate-900">{{ __('Direct Chat with Client') }}</h2>
                    </div>
                </div>

                <div class="space-y-3 max-h-72 overflow-y-auto custom-scrollbar p-2">
                    @forelse($request->messages as $msg)
                        @php $isMe = $msg->sender_id === auth()->id(); @endphp
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            <div class="text-[10px] text-slate-400 mb-0.5">
                                <span class="font-bold {{ $isMe ? 'text-teal-700' : 'text-slate-600' }}">{{ $msg->sender->full_name }}</span> • {{ $msg->sent_at->format('H:i') }}
                            </div>
                            <div class="p-3.5 rounded-2xl max-w-[80%] text-xs leading-relaxed {{ $isMe ? 'bg-teal-700 text-white rounded-tr-none shadow-sm' : 'bg-slate-100 text-slate-800 rounded-tl-none border border-slate-200' }}">
                                {{ $msg->message_text }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            {{ __('No messages yet. Send a message to communicate with the client.') }}
                        </div>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('messages.store', $request->id) }}" class="flex items-center space-x-2 pt-3 border-t border-slate-100">
                    @csrf
                    <input type="text" name="message_text" required placeholder="{{ __('Type your message to client...') }}" class="flex-1 py-3 px-4 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-slate-50 text-slate-900 placeholder:text-slate-400 shadow-inner">
                    <button type="submit" class="flex-shrink-0 px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-bold text-xs transition shadow flex items-center space-x-1.5 cursor-pointer">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>{{ __('Send') }}</span>
                    </button>
                </form>
            </div>

        </div>

        <!-- Right Col: Client Information & Location -->
        <div class="space-y-6">
            
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Client Information') }}</h3>
                
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-brand-900 text-white font-bold text-sm flex items-center justify-center">
                        {{ $request->client->initials }}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $request->client->full_name }}</h4>
                        @if($canViewContact)
                            <p class="text-xs text-slate-700 font-mono mt-0.5">{{ $request->client->phone }}</p>
                        @else
                            <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $request->client->masked_phone }}</p>
                        @endif
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">{{ __('Service') }}:</span>
                        <span class="font-bold text-slate-900">{{ __($request->service->name) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">{{ __('Urgency') }}:</span>
                        <span class="font-bold capitalize {{ $request->urgency === 'urgent' ? 'text-red-600' : 'text-slate-900' }}">{{ __($request->urgency) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">{{ __('Preferred Date') }}:</span>
                        <span class="font-bold text-slate-900">{{ $request->preferred_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">{{ __('Job Location') }}:</span>
                        <span class="font-bold text-slate-900 text-right max-w-[160px] truncate">{{ $request->location }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">{{ __('Service Payment') }}:</span>
                        <span class="font-bold text-emerald-700">{{ __('Direct / Offline') }}</span>
                    </div>
                </div>

                <!-- Direct WhatsApp / Contact Unlock Box (Section 12 & 14) -->
                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Direct Client Contact') }}</p>
                    @if($canViewContact)
                        <div class="p-3 bg-teal-50/60 rounded-2xl border border-teal-100 space-y-2 text-xs">
                            <a href="{{ $request->client->getWhatsappUrl('Hello ' . $request->client->full_name . ', this is ' . auth()->user()->full_name . ' regarding your FUNDI request ' . $request->reference_no . '.') }}" target="_blank" class="block w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl text-center shadow transition flex items-center justify-center space-x-1.5">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                                <span>{{ __('Message on WhatsApp') }}</span>
                            </a>
                        </div>
                    @else
                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-2">
                            <div class="flex items-center space-x-1.5 text-slate-700 font-bold">
                                <i data-lucide="lock" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>{{ __('Contact Access Protected') }}</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                @if(!auth()->user()->hasActiveSubscription())
                                    {{ __('Your subscription has expired. Renew your plan to receive new service opportunities and unlock new client contacts.') }}
                                @else
                                    {{ __('Direct client phone & WhatsApp access unlock as soon as you accept this service request.') }}
                                @endif
                            </p>
                            @if(!auth()->user()->hasActiveSubscription())
                            <a href="{{ route('technician.subscription') }}" class="block text-center py-2.5 px-3 bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                {{ __('Renew Subscription Plan') }}
                            </a>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Digital Job Receipt Link -->
                @if(in_array($request->status, ['completed', 'client_confirmed', 'reviewed']))
                <div class="pt-2">
                    <a href="{{ route('requests.receipt', $request->id) }}" target="_blank" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center space-x-2 transition">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                        <span>{{ __('View Official Receipt') }}</span>
                    </a>
                </div>
                @endif
            </div>

        </div>

    </div>

    <!-- Itemized Quotation Builder Modal (Section 22) -->
    <div x-show="quoteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-data="{ 
            labour: {{ $request->latestQuotation->labour_cost ?? 50000 }}, 
            materials: {{ $request->latestQuotation->materials_cost ?? 25000 }}, 
            transport: {{ $request->latestQuotation->transport_cost ?? 5000 }}, 
            other: 0, 
            discount: 0,
            get total() { return Math.max(0, (parseFloat(this.labour || 0) + parseFloat(this.materials || 0) + parseFloat(this.transport || 0) + parseFloat(this.other || 0)) - parseFloat(this.discount || 0)); }
         }">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto" @click.outside="quoteModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">{{ __('Create Itemized Quotation') }}</h3>
                <button type="button" @click="quoteModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('technician.requests.quotation', $request->id) }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Labour Cost') }} (TZS)</label>
                        <input type="number" name="labour_cost" x-model="labour" required min="0" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs font-bold focus:ring-2 focus:ring-brand-500 bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Materials Cost') }} (TZS)</label>
                        <input type="number" name="materials_cost" x-model="materials" min="0" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs font-bold focus:ring-2 focus:ring-brand-500 bg-slate-50">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Transport / Logistics') }} (TZS)</label>
                        <input type="number" name="transport_cost" x-model="transport" min="0" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs font-bold focus:ring-2 focus:ring-brand-500 bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Discount') }} (TZS)</label>
                        <input type="number" name="discount" x-model="discount" min="0" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs font-bold focus:ring-2 focus:ring-brand-500 bg-slate-50">
                    </div>
                </div>

                <!-- Live Auto-Summed Total Display (Section 22) -->
                <div class="p-4 rounded-2xl bg-brand-50 border border-brand-200 text-center">
                    <p class="text-xs font-bold uppercase tracking-wider text-brand-700">{{ __('Total Calculated Quotation') }}</p>
                    <p class="text-2xl font-black text-brand-950 mt-0.5">
                        TZS <span x-text="new Intl.NumberFormat().format(total)"></span>
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Estimated Duration') }}</label>
                        <input type="text" name="estimated_duration" value="3 Hours" placeholder="e.g. 3 Hours / 1 Day" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Valid Until') }}</label>
                        <input type="date" name="valid_until" value="{{ now()->addDays(7)->format('Y-m-d') }}" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Scope & Notes') }}</label>
                    <textarea name="notes" rows="2" placeholder="{{ __('Replacement of damaged socket and general electrical inspection...') }}" class="w-full p-3 rounded-xl border border-slate-200 text-xs bg-slate-50"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="quoteModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 rounded-xl hover:bg-slate-100">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold bg-brand-700 hover:bg-brand-800 text-white rounded-xl shadow">{{ __('Send Quotation') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Decline Request Modal -->
    <div x-show="declineModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-100 space-y-4" @click.outside="declineModal = false">
            <h3 class="text-base font-bold text-slate-900">{{ __('Decline Service Request') }}</h3>
            <p class="text-xs text-slate-500">{{ __('Please provide a reason why you cannot accept this request.') }}</p>
            <form method="POST" action="{{ route('technician.requests.decline', $request->id) }}" class="space-y-3">
                @csrf
                <textarea name="cancellation_reason" rows="3" required placeholder="{{ __('e.g. Schedule fully booked on this date / outside my current service area') }}" class="w-full p-3 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-rose-500"></textarea>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="declineModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 rounded-xl hover:bg-slate-100">{{ __('Back') }}</button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold bg-rose-600 text-white rounded-xl shadow hover:bg-rose-700">{{ __('Decline Request') }}</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
