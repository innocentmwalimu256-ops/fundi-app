@extends('layouts.app')

@section('title', __('Subscription Expired') . ' - FUNDI')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- Expired Notification Banner -->
    <div class="bg-rose-50 border border-rose-200 rounded-3xl p-6 sm:p-10 text-center space-y-4 shadow-sm">
        <div class="w-14 h-14 rounded-2xl bg-rose-600 text-white font-black flex items-center justify-center mx-auto shadow-lg shadow-rose-600/30">
            <i data-lucide="lock" class="w-7 h-7"></i>
        </div>

        <div class="max-w-xl mx-auto space-y-2">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ __('Your Subscription Has Expired') }}</h1>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-semibold">
                {{ __('Your subscription has expired. Renew your plan to receive new service opportunities and unlock new client contacts.') }}
            </p>
            <p class="text-xs text-slate-500">
                {{ __('Your profile, previous reviews, and completed job history remain completely saved and safe.') }}
            </p>
        </div>
    </div>

    <!-- Plans Selector -->
    <div class="space-y-4">
        <div class="text-center">
            <h2 class="text-xl font-black text-slate-900">{{ __('Select a Plan to Reactivate') }}</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
            @foreach($plans as $plan)
            <div class="bg-white rounded-3xl p-6 sm:p-8 border {{ $plan->slug === 'professional' ? 'border-teal-500 ring-2 ring-teal-500/20 shadow-lg' : ($plan->slug === 'premium' ? 'border-amber-400 ring-2 ring-amber-400/20 shadow-lg' : 'border-slate-200 shadow-sm') }} flex flex-col justify-between space-y-6 relative hover:shadow-md transition">
                
                @if($plan->slug === 'professional')
                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                    <span class="px-3.5 py-1 rounded-full bg-teal-600 text-white text-[10px] font-black uppercase tracking-widest shadow">
                        {{ __('RECOMMENDED') }}
                    </span>
                </div>
                @elseif($plan->slug === 'premium')
                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                    <span class="px-3.5 py-1 rounded-full bg-amber-500 text-slate-950 text-[10px] font-black uppercase tracking-widest shadow">
                        ⭐ {{ __('FEATURED PRO') }}
                    </span>
                </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">{{ __($plan->name) }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ __($plan->description) }}</p>
                    </div>

                    <div>
                        <span class="text-3xl font-black text-slate-900 font-mono">{{ number_format($plan->price, 0) }}</span>
                        <span class="text-xs text-slate-500 font-bold">TZS / {{ $plan->duration_days }} {{ __('Days') }}</span>
                    </div>

                    <ul class="space-y-2.5 text-xs text-slate-700 pt-2 border-t border-slate-100">
                        <li class="flex items-center space-x-2">
                            <i data-lucide="check" class="w-4 h-4 text-teal-600 flex-shrink-0"></i>
                            <span>{{ __('Receive Client Requests') }}</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i data-lucide="check" class="w-4 h-4 text-teal-600 flex-shrink-0"></i>
                            <span>{{ __('Direct WhatsApp & Chat Contact') }}</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i data-lucide="check" class="w-4 h-4 text-teal-600 flex-shrink-0"></i>
                            <span>{{ __('Portfolio:') }} <strong>{{ $plan->portfolio_limit === 0 ? __('Unlimited Projects') : $plan->portfolio_limit . ' ' . __('Projects') }}</strong></span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i data-lucide="{{ $plan->is_featured ? 'star' : 'minus' }}" class="w-4 h-4 {{ $plan->is_featured ? 'text-amber-500 fill-amber-500' : 'text-slate-300' }} flex-shrink-0"></i>
                            <span class="{{ $plan->is_featured ? 'font-black text-amber-950' : 'text-slate-400' }}">⭐ {{ __('Featured Profile Badge') }}</span>
                        </li>
                    </ul>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('technician.subscription.checkout', $plan->slug) }}" class="block w-full py-3.5 rounded-2xl {{ $plan->slug === 'professional' ? 'bg-teal-600 hover:bg-teal-700 text-white' : ($plan->slug === 'premium' ? 'bg-amber-500 hover:bg-amber-600 text-slate-950 font-black' : 'bg-slate-900 hover:bg-slate-800 text-white') }} font-bold text-xs uppercase tracking-wider text-center shadow transition">
                        {{ __('Select') }} {{ __($plan->name) }}
                    </a>
                </div>

            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
