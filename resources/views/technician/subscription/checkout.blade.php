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

        <!-- Direct In-App Payment Form -->
        <form method="POST" action="{{ route('technician.subscription.pay', $plan->slug) }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">{{ __('Select Payment Method') }} ({{ __('Mobile Money / Card') }})</label>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" x-data="{ selectedMethod: 'mpesa' }">
                    
                    <label :class="selectedMethod === 'mpesa' ? 'border-teal-600 bg-teal-50/50 ring-2 ring-teal-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-3.5 rounded-2xl border cursor-pointer flex flex-col items-center text-center transition">
                        <input type="radio" name="payment_method" value="mpesa" class="sr-only" x-model="selectedMethod">
                        <span class="w-8 h-8 rounded-xl bg-rose-600 text-white font-black text-xs flex items-center justify-center mb-1.5 shadow-sm">M</span>
                        <span class="text-xs font-bold text-slate-900">M-Pesa</span>
                        <span class="text-[10px] text-slate-400">Vodacom</span>
                    </label>

                    <label :class="selectedMethod === 'tigopesa' ? 'border-teal-600 bg-teal-50/50 ring-2 ring-teal-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-3.5 rounded-2xl border cursor-pointer flex flex-col items-center text-center transition">
                        <input type="radio" name="payment_method" value="tigopesa" class="sr-only" x-model="selectedMethod">
                        <span class="w-8 h-8 rounded-xl bg-blue-600 text-white font-black text-xs flex items-center justify-center mb-1.5 shadow-sm">T</span>
                        <span class="text-xs font-bold text-slate-900">Tigo Pesa</span>
                        <span class="text-[10px] text-slate-400">Tigo / Yas</span>
                    </label>

                    <label :class="selectedMethod === 'airtelmoney' ? 'border-teal-600 bg-teal-50/50 ring-2 ring-teal-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-3.5 rounded-2xl border cursor-pointer flex flex-col items-center text-center transition">
                        <input type="radio" name="payment_method" value="airtelmoney" class="sr-only" x-model="selectedMethod">
                        <span class="w-8 h-8 rounded-xl bg-red-600 text-white font-black text-xs flex items-center justify-center mb-1.5 shadow-sm">A</span>
                        <span class="text-xs font-bold text-slate-900">Airtel</span>
                        <span class="text-[10px] text-slate-400">Airtel Money</span>
                    </label>

                    <label :class="selectedMethod === 'card' ? 'border-teal-600 bg-teal-50/50 ring-2 ring-teal-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-3.5 rounded-2xl border cursor-pointer flex flex-col items-center text-center transition">
                        <input type="radio" name="payment_method" value="card" class="sr-only" x-model="selectedMethod">
                        <span class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center mb-1.5 shadow-sm"><i data-lucide="credit-card" class="w-3.5 h-3.5 inline"></i></span>
                        <span class="text-xs font-bold text-slate-900">{{ __('Card') }}</span>
                        <span class="text-[10px] text-slate-400">Visa/Mastercard</span>
                    </label>

                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Payment Phone Number') }}</label>
                <div class="relative">
                    <input type="text" name="phone_number" value="{{ auth()->user()->phone }}" required placeholder="{{ __('07XXXXXXXX or 2557XXXXXXXX') }}" class="w-full py-3.5 px-4 rounded-xl border border-slate-200 text-sm font-mono font-bold bg-slate-50/60 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    <span class="absolute right-3.5 top-3.5 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">Direct In-App</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">{{ __('Payment confirmation prompt will be sent directly to this number.') }}</p>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-black text-sm uppercase tracking-wider shadow-xl shadow-emerald-700/25 transition flex items-center justify-center space-x-2">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                    <span>{{ __('Pay') }} TZS {{ number_format($plan->price, 0) }} & {{ __('Activate Subscription Instantly') }}</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
