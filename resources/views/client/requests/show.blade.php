@extends('layouts.app')

@section('title', 'Request ' . $request->reference_no . ' - FUNDI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{ cancelModal: false, complaintModal: false, reviewModal: false }">

    <!-- Header & Status Stepper Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('client.requests.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-800 mb-2 transition">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> {{ __('Back to My Requests') }}
            </a>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-mono">{{ $request->reference_no }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $request->status_badge_classes }}">
                    {{ __($request->status_label) }}
                </span>
                @if($request->connection_fee_status === 'paid')
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm flex items-center space-x-1">
                        <span>{{ __('Connection Fee (TZS 2,000): PAID') }}</span>
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        {{ __('Connection Fee: Pending Payment') }}
                    </span>
                @endif
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                    {{ __('Job Payment: Direct with Fundi (Offline)') }}
                </span>
            </div>
        </div>

        <!-- Cancellation Action -->
        @if($request->canBeCancelled())
            <button type="button" @click="cancelModal = true" class="px-4 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 text-xs font-bold rounded-xl transition self-start sm:self-auto">
                {{ __('Cancel Request') }}
            </button>
        @endif
    </div>

    @if($request->connection_fee_status === 'pending')
    <div class="p-6 rounded-3xl bg-gradient-to-br from-slate-900 via-slate-950 to-teal-950 text-white border border-teal-500/20 shadow-xl space-y-5"
         x-data="{
             feeMethod: 'mpesa',
             phone: '{{ auth()->user()->phone }}',
             loading: false,
             waitingPin: false,
             verified: false,
             reference: '{{ $request->connection_fee_reference }}',
             statusMsg: '',
             countdown: 60,
             timerInterval: null,
             pollInterval: null,

             async startPayment() {
                 if (!this.phone) {
                     alert('{{ __('Tafadhali weka namba yako ya simu ya kulipia') }}');
                     return;
                 }
                 this.loading = true;
                 this.statusMsg = '{{ __('Inawasiliana na mtandao wa simu...') }}';

                 try {
                     let res = await fetch('{{ route('payments.initiate-fee', $request->id) }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                             'Accept': 'application/json'
                         },
                         body: JSON.stringify({
                             payment_method: this.feeMethod,
                             phone_number: this.phone
                         })
                     });
                     let data = await res.json();
                     if (data.success) {
                         this.reference = data.reference;
                         this.waitingPin = true;
                         this.loading = false;
                         this.statusMsg = data.message || '{{ __('Ombi la malipo limetumwa kwenye simu yako. Tafadhali ingiza PIN.') }}';
                         this.startPolling();
                     } else {
                         this.loading = false;
                         alert(data.message || '{{ __('Hitilafu ya kuanzisha malipo. Tafadhali jaribu tena.') }}');
                     }
                 } catch (e) {
                     this.loading = false;
                     alert('{{ __('Hitilafu ya mtandao. Tafadhali jaribu tena.') }}');
                 }
             },

             startPolling() {
                 this.countdown = 60;
                 if (this.timerInterval) clearInterval(this.timerInterval);
                 if (this.pollInterval) clearInterval(this.pollInterval);

                 this.timerInterval = setInterval(() => {
                     if (this.countdown > 0) this.countdown--;
                 }, 1000);

                 this.pollInterval = setInterval(async () => {
                     if (!this.reference) return;
                     try {
                         let res = await fetch('/payments/check-status/' + encodeURIComponent(this.reference));
                         let data = await res.json();
                         if (data.paid) {
                             clearInterval(this.pollInterval);
                             clearInterval(this.timerInterval);
                             this.waitingPin = false;
                             this.verified = true;
                             this.statusMsg = '{{ __('Malipo Yamethibitishwa! Inafungua huduma...') }}';
                             setTimeout(() => {
                                 window.location.href = data.redirect_url || window.location.href;
                             }, 1200);
                         }
                     } catch (e) {}
                 }, 2000);
             },

             resetPayment() {
                 if (this.pollInterval) clearInterval(this.pollInterval);
                 if (this.timerInterval) clearInterval(this.timerInterval);
                 this.waitingPin = false;
                 this.loading = false;
             }
         }">

        <!-- STATE 1: INITIAL SELECTION & PHONE INPUT -->
        <template x-if="!waitingPin && !verified">
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-teal-400 text-slate-950 font-black flex items-center justify-center flex-shrink-0">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">{{ __('Pay Connection Fee (TZS 2,000) via Mobile Money') }}</h4>
                            <p class="text-xs text-slate-300 mt-0.5">{{ __('Direct USSD Push: Weka namba yako, utapokea ombi la kuingiza PIN kwenye simu papo hapo.') }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-2xl font-black text-teal-300 font-mono">TZS 2,000</span>
                        <span class="text-[10px] text-slate-400 block">{{ __('Instant Activation') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <label :class="feeMethod === 'mpesa' ? 'bg-teal-500/20 border-teal-400 text-teal-300 ring-2 ring-teal-400/30' : 'bg-white/5 border-white/10 text-slate-300'" class="p-2.5 rounded-xl border cursor-pointer flex items-center space-x-2 text-xs font-bold transition">
                        <input type="radio" name="payment_method" value="mpesa" class="sr-only" x-model="feeMethod">
                        <span class="w-6 h-6 rounded-lg bg-rose-600 text-white flex items-center justify-center font-black text-[10px]">M</span>
                        <span>M-Pesa</span>
                    </label>

                    <label :class="feeMethod === 'tigopesa' ? 'bg-teal-500/20 border-teal-400 text-teal-300 ring-2 ring-teal-400/30' : 'bg-white/5 border-white/10 text-slate-300'" class="p-2.5 rounded-xl border cursor-pointer flex items-center space-x-2 text-xs font-bold transition">
                        <input type="radio" name="payment_method" value="tigopesa" class="sr-only" x-model="feeMethod">
                        <span class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center font-black text-[10px]">T</span>
                        <span>Tigo Pesa</span>
                    </label>

                    <label :class="feeMethod === 'airtelmoney' ? 'bg-teal-500/20 border-teal-400 text-teal-300 ring-2 ring-teal-400/30' : 'bg-white/5 border-white/10 text-slate-300'" class="p-2.5 rounded-xl border cursor-pointer flex items-center space-x-2 text-xs font-bold transition">
                        <input type="radio" name="payment_method" value="airtelmoney" class="sr-only" x-model="feeMethod">
                        <span class="w-6 h-6 rounded-lg bg-red-600 text-white flex items-center justify-center font-black text-[10px]">A</span>
                        <span>Airtel</span>
                    </label>

                    <label :class="feeMethod === 'card' ? 'bg-teal-500/20 border-teal-400 text-teal-300 ring-2 ring-teal-400/30' : 'bg-white/5 border-white/10 text-slate-300'" class="p-2.5 rounded-xl border cursor-pointer flex items-center space-x-2 text-xs font-bold transition">
                        <input type="radio" name="payment_method" value="card" class="sr-only" x-model="feeMethod">
                        <span class="w-6 h-6 rounded-lg bg-slate-800 text-white flex items-center justify-center font-black text-[10px]"><i data-lucide="credit-card" class="w-3.5 h-3.5"></i></span>
                        <span>{{ __('Card') }}</span>
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="text" x-model="phone" placeholder="{{ __('07XXXXXXXX or 2557XXXXXXXX') }}" class="flex-1 py-3 px-4 rounded-xl bg-white/10 border border-white/20 text-xs text-white placeholder-slate-400 font-mono font-bold focus:ring-2 focus:ring-teal-400 focus:outline-none">
                    <button type="button" @click="startPayment()" :disabled="loading" class="btn-tap px-6 py-3 bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs rounded-xl shadow-lg shadow-teal-500/25 transition flex items-center justify-center space-x-2 cursor-pointer disabled:opacity-50">
                        <template x-if="!loading">
                            <span class="flex items-center space-x-1.5">
                                <span>{{ __('Lipa TZS 2,000 Moja kwa Moja') }}</span>
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </span>
                        </template>
                        <template x-if="loading">
                            <span class="flex items-center space-x-2">
                                <svg class="animate-spin h-4 w-4 text-slate-950" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ __('Inatuma ombi...') }}</span>
                            </span>
                        </template>
                    </button>
                </div>
            </div>
        </template>

        <!-- STATE 2: WAITING FOR USER PIN (LIVE RADAR PULSE) -->
        <template x-if="waitingPin && !verified">
            <div class="py-6 px-4 text-center space-y-4">
                <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-25 animate-ping"></span>
                    <span class="relative inline-flex rounded-full h-16 w-16 bg-teal-500/30 border-2 border-teal-400 items-center justify-center text-teal-300">
                        <i data-lucide="smartphone" class="w-8 h-8 animate-bounce"></i>
                    </span>
                </div>

                <div class="space-y-1">
                    <h3 class="text-base font-black text-white">{{ __('Tafadhali Ingiza PIN Kwenye Simu Yako') }}</h3>
                    <p class="text-xs text-teal-300 font-mono" x-text="phone"></p>
                    <p class="text-xs text-slate-300 max-w-md mx-auto mt-1" x-text="statusMsg"></p>
                </div>

                <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-white/10 text-xs font-mono text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>{{ __('Inasubiri uthibitisho...') }} <strong x-text="countdown + 's'"></strong></span>
                </div>

                <div class="pt-2">
                    <button type="button" @click="resetPayment()" class="text-xs text-slate-400 hover:text-white underline font-semibold transition cursor-pointer">
                        {{ __('Hukupokea ombi? Bonyeza hapa kujaribu tena') }}
                    </button>
                </div>
            </div>
        </template>

        <!-- STATE 3: VERIFIED / AUTO-REDIRECTING -->
        <template x-if="verified">
            <div class="py-8 px-4 text-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center mx-auto shadow-lg shadow-emerald-500/40">
                    <i data-lucide="check" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-black text-white">{{ __('Malipo Yamethibitishwa Kikamilifu!') }}</h3>
                <p class="text-xs text-emerald-300" x-text="statusMsg"></p>
            </div>
        </template>

    </div>
    @endif

    <!-- 3-Step Simple Progress Stepper -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Service Progress') }}</h3>
            <span class="text-xs font-bold text-teal-700 bg-teal-50 px-3 py-1 rounded-full">
                @if(in_array($request->status, ['completed', 'client_confirmed', 'reviewed']))
                    {{ __('Job Completed') }}
                @elseif(in_array($request->status, ['in_progress', 'scheduled', 'on_the_way', 'accepted']))
                    {{ __('Technician In Progress') }}
                @else
                    {{ __('Request Sent & Paid') }}
                @endif
            </span>
        </div>

        @php
            $currentStepIdx = match($request->status) {
                'pending', 'requested' => 1,
                'accepted', 'quotation_pending', 'quotation_accepted', 'scheduled', 'on_the_way', 'in_progress' => 2,
                'completed', 'client_confirmed', 'reviewed' => 3,
                default => 1,
            };
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            
            <!-- Step 1 -->
            <div class="p-4 rounded-2xl border transition {{ $currentStepIdx >= 1 ? 'bg-teal-50/70 border-teal-200 shadow-sm' : 'bg-slate-50 border-slate-100' }}">
                <div class="flex items-center space-x-2.5 mb-1.5">
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-black {{ $currentStepIdx > 1 ? 'bg-teal-600 text-white' : 'bg-teal-700 text-white ring-2 ring-teal-200 shadow-sm' }}">
                        @if($currentStepIdx > 1) <i data-lucide="check" class="w-4 h-4"></i> @else 1 @endif
                    </div>
                    <h4 class="text-xs font-bold text-slate-900">{{ __('1. Request & Fee') }}</h4>
                </div>
                <p class="text-[11px] text-slate-500">{{ __('Request sent and fee (TZS 2,000) verified') }}</p>
            </div>

            <!-- Step 2 -->
            <div class="p-4 rounded-2xl border transition {{ $currentStepIdx >= 2 ? 'bg-teal-50/70 border-teal-200 shadow-sm' : 'bg-slate-50 border-slate-100' }}">
                <div class="flex items-center space-x-2.5 mb-1.5">
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-black {{ $currentStepIdx > 2 ? 'bg-teal-600 text-white' : ($currentStepIdx === 2 ? 'bg-teal-700 text-white ring-2 ring-teal-200 shadow-sm' : 'bg-slate-200 text-slate-500') }}">
                        @if($currentStepIdx > 2) <i data-lucide="check" class="w-4 h-4"></i> @else 2 @endif
                    </div>
                    <h4 class="text-xs font-bold text-slate-900">{{ __('2. Fundi at Work') }}</h4>
                </div>
                <p class="text-[11px] text-slate-500">{{ __('Direct contact unlocked and fundi executing work') }}</p>
            </div>

            <!-- Step 3 -->
            <div class="p-4 rounded-2xl border transition {{ $currentStepIdx >= 3 ? 'bg-emerald-50 border-emerald-300 ring-2 ring-emerald-400/20 shadow-sm' : 'bg-slate-50 border-slate-100' }}">
                <div class="flex items-center space-x-2.5 mb-1.5">
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-black {{ $currentStepIdx === 3 ? 'bg-emerald-600 text-white ring-2 ring-emerald-200 shadow-sm' : 'bg-slate-200 text-slate-500' }}">
                        @if($currentStepIdx === 3) <i data-lucide="check" class="w-4 h-4"></i> @else 3 @endif
                    </div>
                    <h4 class="text-xs font-bold text-slate-900">{{ __('3. Work Completed') }}</h4>
                </div>
                <p class="text-[11px] text-slate-500">{{ __('Direct settlement and rate your fundi') }}</p>
            </div>

        </div>
    </div>

    <!-- Main 2-Column Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Quotations, Workflows, Messages -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Itemized Quotation Review (Section 37) -->
            @if($request->latestQuotation)
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold uppercase tracking-wider text-slate-900 flex items-center">
                        <i data-lucide="calculator" class="w-4 h-4 mr-2 text-indigo-600"></i> {{ __('Itemized Service Quotation') }}
                    </h2>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ $request->latestQuotation->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($request->latestQuotation->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ __('Quotation') }} {{ __($request->latestQuotation->status) }}
                    </span>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5 text-xs divide-y divide-slate-200">
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
                        <span>{{ __('Total Quotation Cost') }}</span>
                        <span class="text-brand-700">{{ $request->latestQuotation->formatted_total }}</span>
                    </div>
                </div>

                @if($request->latestQuotation->notes)
                <p class="text-xs text-slate-600"><strong>{{ __('Notes from Technician') }}:</strong> {{ $request->latestQuotation->notes }}</p>
                @endif

                <!-- Client Decision Actions -->
                @if($request->status === 'quotation_pending')
                <div class="pt-4 border-t border-slate-100 flex items-center space-x-3">
                    <form method="POST" action="{{ route('client.requests.accept-quotation', $request->id) }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-teal-600/20 transition">
                             {{ __('Accept Quotation & Schedule Job') }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('client.requests.reject-quotation', $request->id) }}">
                        @csrf
                        <input type="hidden" name="rejection_reason" value="Pricing adjustment requested">
                        <button type="submit" class="py-3.5 px-5 rounded-2xl bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-bold border border-rose-200 transition">
                            {{ __('Decline Quote') }}
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endif

            <!-- Completion Confirmation & Review CTA (Section 41 & 42) -->
            @if($request->status === 'completed')
            <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl space-y-4">
                <div class="space-y-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold">{{ __('Action Required') }}</span>
                    <h3 class="text-lg sm:text-xl font-black">{{ __('Technician has finished the work!') }}</h3>
                    <p class="text-xs text-slate-300">{{ __('Please inspect the completed work and confirm satisfaction to close this job.') }}</p>
                </div>

                <form method="POST" action="{{ route('client.requests.confirm-completion', $request->id) }}">
                    @csrf
                    <button type="submit" class="py-3.5 px-6 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg transition">
                         {{ __('Confirm Completion & Unlock Review') }}
                    </button>
                </form>
            </div>
            @endif

            <!-- Rate & Review Section -->
            @if(!$request->review)
            <div class="bg-gradient-to-br from-white via-amber-50/30 to-white rounded-3xl p-6 sm:p-8 border border-amber-200 shadow-sm space-y-4" x-data="{ star: 5 }">
                <div class="space-y-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black uppercase">{{ __('Feedback & Rating') }}</span>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">{{ __('Rate & Review') }} {{ $request->technician->full_name }}</h3>
                    <p class="text-xs text-slate-500">{{ __('Your feedback helps other clients find trustworthy fundis.') }}</p>
                </div>

                <form method="POST" action="{{ route('client.requests.review', $request->id) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ __('Select Star Rating') }}</label>
                        <div class="flex items-center space-x-2">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button" @click="star = {{ $i }}" class="p-1 text-3xl transition transform active:scale-125" :class="star >= {{ $i }} ? 'text-amber-400' : 'text-slate-200'">
                                <i data-lucide="star" class="w-3.5 h-3.5 inline fill-amber-400 text-amber-400"></i>
                            </button>
                            @endfor
                            <input type="hidden" name="rating" x-model="star">
                            <span class="text-xs font-black text-amber-700 ml-2" x-text="star + ' / 5 ' + '{{ __('Stars') }}'"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Your Feedback on Completed Job') }}</label>
                        <textarea name="comment" rows="2" placeholder="{{ __('Briefly describe your experience with this technician...') }}" class="w-full p-3.5 rounded-xl border border-slate-200 text-xs bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center justify-center space-x-2">
                        <i data-lucide="star" class="w-4 h-4"></i>
                        <span>{{ __('Submit Rating & Review') }}</span>
                    </button>
                </form>
            </div>
            @else
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-emerald-200 shadow-sm space-y-3 bg-emerald-50/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-emerald-800 uppercase flex items-center space-x-1">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        <span>{{ __('You have reviewed this technician') }}</span>
                    </span>
                    <div class="flex items-center text-amber-400 text-sm">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= $request->review->rating ? '<i data-lucide="star" class="w-3.5 h-3.5 inline fill-amber-400 text-amber-400"></i>' : '<i data-lucide="star" class="w-3.5 h-3.5 inline text-slate-300"></i>' }}</span>
                        @endfor
                    </div>
                </div>
                <p class="text-xs text-slate-700 italic bg-white p-3 rounded-xl border border-slate-100">
                    "{{ $request->review->comment }}"
                </p>
            </div>
            @endif

            <!-- Problem Description & Photos -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                <h2 class="text-base font-bold uppercase tracking-wider text-slate-900 flex items-center">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2 text-brand-700"></i> {{ __('Problem Details') }}
                </h2>
                
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 leading-relaxed">
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

            <!-- Request-Scoped Direct Chat -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="message-square" class="w-5 h-5 text-brand-700"></i>
                        <h2 class="text-base font-bold text-slate-900">{{ __('Direct Chat with Technician') }}</h2>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono">{{ __('Job Reference') }} #{{ $request->reference_no }}</span>
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
                            {{ __('No messages yet. Send a message to communicate with the technician.') }}
                        </div>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('messages.store', $request->id) }}" class="flex items-center space-x-2 pt-3 border-t border-slate-100">
                    @csrf
                    <input type="text" name="message_text" required placeholder="{{ __('Type your message to technician...') }}" class="flex-1 py-3 px-4 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-slate-50 text-slate-900 placeholder:text-slate-400 shadow-inner">
                    <button type="submit" class="flex-shrink-0 px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-bold text-xs transition shadow flex items-center space-x-1.5 cursor-pointer">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>{{ __('Send') }}</span>
                    </button>
                </form>
            </div>

        </div>

        <!-- Right Col: Technician Card, Schedule, Dispute Filing -->
        <div class="space-y-6">
            
            <!-- Assigned Technician Summary -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Assigned Technician') }}</h3>
                
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-brand-900 text-white font-black text-sm flex items-center justify-center shadow-md">
                        {{ $request->technician->initials }}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $request->technician->full_name }}</h4>
                        <p class="text-xs text-brand-700 font-medium">{{ __($request->technician->technicianProfile->professional_title ?? 'Master Technician') }}</p>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">{{ __('Service') }}:</span>
                        <span class="font-bold text-slate-900">{{ __($request->service->name) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">{{ __('Location') }}:</span>
                        <span class="font-bold text-slate-900 text-right max-w-[160px] truncate">{{ $request->location }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">{{ __('Scheduled Date') }}:</span>
                        <span class="font-bold text-slate-900">{{ $request->preferred_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">{{ __('Service Payment') }}:</span>
                        <span class="font-bold text-emerald-700">{{ __('Direct / Offline') }}</span>
                    </div>
                </div>

                <!-- Contact & WhatsApp Section (Section 10 & 14) -->
                <div class="pt-3 border-t border-slate-100 space-y-2.5">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Direct Communication') }}</p>
                    @if(!empty($canViewContact))
                        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 space-y-3 text-xs">
                            <div class="flex items-center justify-between border-b border-emerald-200/60 pb-2">
                                <span class="text-emerald-900 font-bold flex items-center space-x-1">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    <span>{{ __('Contact Unlocked') }}</span>
                                </span>
                                <span class="text-[10px] font-black text-emerald-700 uppercase bg-emerald-200/60 px-2 py-0.5 rounded">{{ __('UNLOCKED') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-600">{{ __('Phone Number') }}:</span>
                                <a href="tel:{{ $request->technician->phone }}" class="font-bold text-slate-950 font-mono text-sm hover:text-emerald-700 transition flex items-center space-x-1">
                                    <i data-lucide="phone-call" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    <span>{{ $request->technician->phone }}</span>
                                </a>
                            </div>
                            <div class="grid grid-cols-2 gap-2 pt-1">
                                <a href="tel:{{ $request->technician->phone }}" class="py-2.5 px-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl text-center shadow transition flex items-center justify-center space-x-1.5">
                                    <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                    <span>{{ __('Call') }}</span>
                                </a>
                                <a href="{{ $request->technician->getWhatsappUrl('Habari ' . $request->technician->full_name . ', nimekupata kwenye FUNDI kuhusu ombi langu la kazi ' . $request->reference_no . '.') }}" target="_blank" class="py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl text-center shadow transition flex items-center justify-center space-x-1.5">
                                    <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                    <span>{{ __('WhatsApp') }}</span>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-1.5">
                            <div class="flex items-center justify-between text-slate-500">
                                <span>{{ __('Phone Number') }}:</span>
                                <span class="font-mono font-bold">{{ $request->technician->masked_phone }}</span>
                            </div>
                            <p class="text-[11px] text-slate-400 leading-tight">
                                <i data-lucide="lock" class="w-3.5 h-3.5 inline"></i> {{ __('Direct phone & WhatsApp unlock once technician confirms and accepts your request.') }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Digital Job Receipt Button (Section 9) -->
                @if(in_array($request->status, ['completed', 'client_confirmed', 'reviewed']))
                <div class="pt-2">
                    <a href="{{ route('requests.receipt', $request->id) }}" target="_blank" class="w-full py-3 px-4 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center space-x-2 shadow-sm transition">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                        <span>{{ __('Download / Print Receipt') }}</span>
                    </a>
                </div>
                @endif

                <div class="pt-1">
                    <a href="{{ route('client.technicians.show', $request->technician_id) }}" class="text-xs font-bold text-brand-700 hover:underline flex items-center">
                        <span>{{ __('View Full Technician Profile') }}</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Dispute Resolution Filing Button -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Need Help or Dispute?') }}</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    {{ __('If there is an issue with service delivery, no-show, or agreement disagreement, file a formal complaint.') }}
                </p>
                <button type="button" @click="complaintModal = true" class="text-xs font-bold text-rose-600 hover:text-rose-800 flex items-center">
                    <i data-lucide="alert-octagon" class="w-3.5 h-3.5 mr-1"></i>
                    <span>{{ __('File Complaint') }}</span>
                </button>
            </div>

        </div>

    </div>

    <!-- Multi-Option Cancellation Modal (Feature 5) -->
    <div x-show="cancelModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-4" @click.outside="cancelModal = false">
            <h3 class="text-base font-bold text-slate-900">{{ __('Cancel Service Request') }}</h3>
            <p class="text-xs text-slate-500">{{ __('Please select why you need to cancel this service request:') }}</p>

            <form method="POST" action="{{ route('client.requests.cancel', $request->id) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Reason for Cancellation') }}</label>
                    <select name="reason" required class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                        <option value="Found another technician">{{ __('Found another technician') }}</option>
                        <option value="Price quotation too high">{{ __('Price quotation too high') }}</option>
                        <option value="No longer need service">{{ __('No longer need service') }}</option>
                        <option value="Technician delayed / unavailable">{{ __('Technician delayed / unavailable') }}</option>
                        <option value="Emergency resolved">{{ __('Emergency resolved') }}</option>
                        <option value="Other reason">{{ __('Other reason') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Additional Details (Optional)') }}</label>
                    <textarea name="description" rows="2" placeholder="{{ __('Provide extra context...') }}" class="w-full p-3 rounded-xl border border-slate-200 text-xs bg-slate-50"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="cancelModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 rounded-xl hover:bg-slate-100">{{ __('Back') }}</button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold bg-rose-600 text-white rounded-xl shadow hover:bg-rose-700">{{ __('Confirm Cancellation') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Dispute & Evidence Filing Modal (Feature 7) -->
    <div x-show="complaintModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto" @click.outside="complaintModal = false">
            <h3 class="text-base font-bold text-slate-900">{{ __('File Formal Dispute & Evidence') }}</h3>
            <p class="text-xs text-slate-500">{{ __('Provide details and attach photo/document evidence for administrator mediation.') }}</p>

            <form method="POST" action="{{ route('client.requests.complaint', $request->id) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Issue Category') }}</label>
                    <select name="category" required class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                        <option value="technician_no_show">{{ __('Technician Did Not Arrive') }}</option>
                        <option value="poor_service">{{ __('Poor Service Quality / Fault Unresolved') }}</option>
                        <option value="misconduct">{{ __('Professional Misconduct / Unsafe Behaviour') }}</option>
                        <option value="payment_issue">{{ __('Payment Disagreement / Overcharging') }}</option>
                        <option value="unfinished">{{ __('Job Left Incomplete') }}</option>
                        <option value="other">{{ __('Other Issue') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Detailed Description of What Happened') }}</label>
                    <textarea name="description" rows="4" required placeholder="{{ __('Explain the incident clearly...') }}" class="w-full p-3.5 rounded-xl border border-slate-200 text-xs bg-slate-50"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Attach Supporting Photos / Evidence (Optional)') }}</label>
                    <input type="file" name="evidence[]" multiple accept="image/*,.pdf" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="complaintModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 rounded-xl hover:bg-slate-100">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold bg-rose-600 text-white rounded-xl shadow hover:bg-rose-700">{{ __('Submit Dispute') }}</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
