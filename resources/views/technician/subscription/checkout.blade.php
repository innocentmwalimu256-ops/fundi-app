@extends('layouts.app')

@section('title', __('Pay Subscription') . ' ' . $plan->name . ' - FUNDI')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">

    <a href="{{ route('technician.subscription') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-800 transition">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> {{ __('Back to Plans') }}
    </a>

    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-xl space-y-8">
        
        <div class="border-b border-slate-100 pb-6 space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-widest text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-full">{{ __('Technician Marketplace Subscription') }}</span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ __('Payment for') }} {{ __($plan->name) }} {{ __('Plan') }}</h1>
            <p class="text-xs text-slate-500">{{ __('Pay securely within the app to activate your profile and start receiving client requests.') }}</p>
        </div>

        <!-- Plan Summary Box -->
        <div class="bg-gradient-to-br from-slate-900 via-brand-950 to-slate-900 rounded-3xl p-6 text-white space-y-4 shadow-lg border border-teal-500/20">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-white">{{ __($plan->name) }} {{ __('Plan') }}</h3>
                    <p class="text-xs text-slate-300">{{ __($plan->description) }}</p>
                </div>
                <div class="text-right">
                    <span class="text-2xl sm:text-3xl font-black text-teal-400 font-mono">{{ number_format($plan->price, 0) }}</span>
                    <span class="text-xs text-slate-300 font-bold block">TZS / {{ $plan->duration_days }} {{ __('Days') }}</span>
                </div>
            </div>

            <div class="pt-3 border-t border-white/10 flex items-center justify-between text-xs text-slate-300">
                <span>{{ __('Benefits:') }}</span>
                <span class="font-bold text-teal-300"><i data-lucide="check" class="w-3.5 h-3.5 inline text-emerald-600"></i> {{ __('Direct WhatsApp & Chat Contact') }} • {{ __('Jobs') }}: {{ $plan->request_limit === 0 ? __('Unlimited') : $plan->request_limit . ' ' . __('per month') }}</span>
            </div>
        </div>

        <!-- Direct In-App Payment Component -->
        <div x-data="{
                 method: 'mpesa',
                 phone: '{{ auth()->user()->phone }}',
                 loading: false,
                 waitingPin: false,
                 verified: false,
                 reference: '',
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
                     this.statusMsg = '{{ __('Inatuma ombi kwenye mtandao wa simu...') }}';

                     try {
                         let res = await fetch('{{ route('api.payments.initiate-subscription', $plan->slug) }}', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                 'Accept': 'application/json'
                             },
                             body: JSON.stringify({
                                 payment_method: this.method,
                                 phone_number: this.phone
                             })
                         });
                         let data = await res.json();
                         if (data.success) {
                             this.reference = data.reference;
                             this.waitingPin = true;
                             this.loading = false;
                             this.statusMsg = data.message || '{{ __('Ombi limetumwa kwenye simu yako. Tafadhali ingiza PIN.') }}';
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
                             let res = await fetch('/api/payments/check-status/' + encodeURIComponent(this.reference));
                             let data = await res.json();
                             if (data.paid) {
                                 clearInterval(this.pollInterval);
                                 clearInterval(this.timerInterval);
                                 this.waitingPin = false;
                                 this.verified = true;
                                 this.statusMsg = '{{ __('Malipo Yamethibitishwa! Inakupeleka kwenye dashibodi...') }}';
                                 setTimeout(() => {
                                     window.location.href = data.redirect_url || '{{ route('technician.subscription') }}';
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

            <!-- STATE 1: SELECTION & PHONE INPUT -->
            <template x-if="!waitingPin && !verified">
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">{{ __('Select Payment Method') }} ({{ __('Mobile Money / Card') }})</label>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <label :class="method === 'mpesa' ? 'border-teal-600 bg-teal-50/50 ring-2 ring-teal-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-3.5 rounded-2xl border cursor-pointer flex flex-col items-center text-center transition">
                                <input type="radio" name="payment_method" value="mpesa" class="sr-only" x-model="method">
                                <span class="w-8 h-8 rounded-xl bg-rose-600 text-white font-black text-xs flex items-center justify-center mb-1.5 shadow-sm">M</span>
                                <span class="text-xs font-bold text-slate-900">M-Pesa</span>
                                <span class="text-[10px] text-slate-400">Vodacom</span>
                            </label>

                            <label :class="method === 'tigopesa' ? 'border-teal-600 bg-teal-50/50 ring-2 ring-teal-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-3.5 rounded-2xl border cursor-pointer flex flex-col items-center text-center transition">
                                <input type="radio" name="payment_method" value="tigopesa" class="sr-only" x-model="method">
                                <span class="w-8 h-8 rounded-xl bg-blue-600 text-white font-black text-xs flex items-center justify-center mb-1.5 shadow-sm">T</span>
                                <span class="text-xs font-bold text-slate-900">Tigo Pesa</span>
                                <span class="text-[10px] text-slate-400">Tigo / Yas</span>
                            </label>

                            <label :class="method === 'airtelmoney' ? 'border-teal-600 bg-teal-50/50 ring-2 ring-teal-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-3.5 rounded-2xl border cursor-pointer flex flex-col items-center text-center transition">
                                <input type="radio" name="payment_method" value="airtelmoney" class="sr-only" x-model="method">
                                <span class="w-8 h-8 rounded-xl bg-red-600 text-white font-black text-xs flex items-center justify-center mb-1.5 shadow-sm">A</span>
                                <span class="text-xs font-bold text-slate-900">Airtel</span>
                                <span class="text-[10px] text-slate-400">Airtel Money</span>
                            </label>

                            <label :class="method === 'card' ? 'border-teal-600 bg-teal-50/50 ring-2 ring-teal-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-3.5 rounded-2xl border cursor-pointer flex flex-col items-center text-center transition">
                                <input type="radio" name="payment_method" value="card" class="sr-only" x-model="method">
                                <span class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center mb-1.5 shadow-sm"><i data-lucide="credit-card" class="w-3.5 h-3.5 inline"></i></span>
                                <span class="text-xs font-bold text-slate-900">{{ __('Card') }}</span>
                                <span class="text-[10px] text-slate-400">Visa/Mastercard</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Payment Phone Number') }}</label>
                        <div class="relative">
                            <input type="text" x-model="phone" required placeholder="{{ __('07XXXXXXXX or 2557XXXXXXXX') }}" class="w-full py-3.5 px-4 rounded-xl border border-slate-200 text-sm font-mono font-bold bg-slate-50/60 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <span class="absolute right-3.5 top-3.5 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">Direct USSD Push</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">{{ __('Payment confirmation prompt will be sent directly to this number.') }}</p>
                    </div>

                    <div class="pt-2">
                        <button type="button" @click="startPayment()" :disabled="loading" class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-black text-sm uppercase tracking-wider shadow-xl shadow-emerald-700/25 transition flex items-center justify-center space-x-2 cursor-pointer disabled:opacity-50">
                            <template x-if="!loading">
                                <span class="flex items-center space-x-2">
                                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                                    <span>{{ __('Lipa') }} TZS {{ number_format($plan->price, 0) }} & {{ __('Washa Subscription') }}</span>
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center space-x-2">
                                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ __('Inatuma ombi kwenye simu...') }}</span>
                                </span>
                            </template>
                        </button>
                    </div>
                </div>
            </template>

            <!-- STATE 2: WAITING FOR USER PIN (LIVE RADAR PULSE) -->
            <template x-if="waitingPin && !verified">
                <div class="py-10 px-4 text-center space-y-5 bg-slate-900 rounded-3xl text-white shadow-xl">
                    <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-25 animate-ping"></span>
                        <span class="relative inline-flex rounded-full h-16 w-16 bg-teal-500/30 border-2 border-teal-400 items-center justify-center text-teal-300">
                            <i data-lucide="smartphone" class="w-8 h-8 animate-bounce"></i>
                        </span>
                    </div>

                    <div class="space-y-1">
                        <h3 class="text-lg font-black text-white">{{ __('Tafadhali Weka PIN Kwenye Simu Yako') }}</h3>
                        <p class="text-xs text-teal-300 font-mono" x-text="phone"></p>
                        <p class="text-xs text-slate-300 max-w-md mx-auto mt-1" x-text="statusMsg"></p>
                    </div>

                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-white/10 text-xs font-mono text-slate-300">
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
                <div class="py-12 px-4 text-center space-y-3 bg-slate-900 rounded-3xl text-white shadow-xl">
                    <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center mx-auto shadow-lg shadow-emerald-500/40">
                        <i data-lucide="check" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-black text-white">{{ __('Malipo Yamethibitishwa Kikamilifu!') }}</h3>
                    <p class="text-xs text-emerald-300" x-text="statusMsg"></p>
                </div>
            </template>

        </div>

    </div>

</div>
@endsection
