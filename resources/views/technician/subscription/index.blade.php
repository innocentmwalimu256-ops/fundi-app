@extends('layouts.app')

@section('title', __('Technician Marketplace Subscription') . ' — FUNDI')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ __('Technician Marketplace Subscription') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ __('Manage your professional marketplace access, visibility level, and client contact privileges') }}</p>
        </div>
    </div>

    <!-- Expiry Warning Alert -->
    @php $warning = $technician->getSubscriptionExpiryWarning(); @endphp
    @if($warning)
    <div class="rounded-3xl p-4 sm:p-5 border flex flex-col sm:flex-row sm:items-center justify-between gap-3 {{ $warning['status'] === 'expired' ? 'bg-rose-50 border-rose-200 text-rose-900' : 'bg-amber-50 border-amber-200 text-amber-900' }}">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold flex-shrink-0 {{ $warning['status'] === 'expired' ? 'bg-rose-600 text-white' : 'bg-amber-500 text-white' }}">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wider">{{ $warning['status'] === 'expired' ? __('Action Required') : __('Subscription Notice') }}</p>
                <p class="text-xs font-medium mt-0.5">{{ $warning['message'] }}</p>
            </div>
        </div>
        <a href="#plans-section" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider text-center shadow transition flex-shrink-0 {{ $warning['status'] === 'expired' ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-amber-600 hover:bg-amber-700 text-white' }}">
            {{ __('Renew Plan') }}
        </a>
    </div>
    @endif

    <!-- Current Subscription Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            
            <div class="space-y-2">
                <div class="flex items-center space-x-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('Current Plan') }}</span>
                    @if($subscription && $subscription->isActive())
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase border {{ $subscription->status_badge_classes }}">
                            ● {{ $subscription->status === 'free_trial' ? __('FREE TRIAL') : __('ACTIVE') }}
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-rose-100 text-rose-800 border border-rose-200">
                            ● {{ __('EXPIRED') }}
                        </span>
                    @endif

                    @if($technician->isFeaturedTechnician())
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-amber-50 text-amber-900 border border-amber-300">
                            ⭐ {{ __('FEATURED TECHNICIAN') }}
                        </span>
                    @endif
                </div>

                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">
                    {{ $subscription && $subscription->plan ? $subscription->plan->name . ' ' . __('Plan') : __('No Active Plan') }}
                </h2>

                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">
                    @if($subscription && $subscription->isActive())
                        <span><i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1 text-slate-400"></i>{{ __('Started:') }} <strong>{{ $subscription->started_at->format('d M Y') }}</strong></span>
                        <span><i data-lucide="clock" class="w-3.5 h-3.5 inline mr-1 text-slate-400"></i>{{ __('Expires:') }} <strong>{{ $subscription->expires_at->format('d M Y') }}</strong></span>
                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-md">
                            {{ $subscription->days_remaining }} {{ __('Days Remaining') }}
                        </span>
                    @else
                        <span class="text-rose-600 font-semibold">{{ __('Your subscription has expired. Renew your plan to receive new service opportunities and unlock new client contacts.') }}</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center space-x-3 flex-shrink-0">
                <a href="#plans-section" class="px-6 py-3.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs uppercase tracking-wider shadow-xs transition text-center">
                    {{ $subscription && $subscription->isActive() ? __('Renew / Change Plan') : __('Choose a Plan') }}
                </a>
            </div>

        </div>
    </div>

    <!-- Available Subscription Plans Grid -->
    <div id="plans-section" class="space-y-4 pt-4">
        <div class="text-center max-w-xl mx-auto space-y-1">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ __('Choose Your FUNDI Plan') }}</h2>
            <p class="text-xs text-slate-500">{{ __('Pick a plan tailored to your business volume and growth goals') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
            @foreach($plans as $plan)
            @php $isCurrent = $subscription && $subscription->plan_id === $plan->id && $subscription->isActive(); @endphp
            <div class="bg-white rounded-3xl p-6 sm:p-8 border {{ $plan->slug === 'professional' ? 'border-teal-500 ring-2 ring-teal-500/20 shadow-lg' : ($plan->slug === 'premium' ? 'border-amber-400 ring-2 ring-amber-400/20 shadow-lg' : 'border-slate-200 shadow-sm') }} flex flex-col justify-between space-y-6 relative hover:shadow-md transition">
                
                @if($plan->slug === 'professional')
                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                    <span class="px-3.5 py-1 rounded-full bg-teal-600 text-white text-[10px] font-black uppercase tracking-widest shadow">
                        {{ __('MOST POPULAR') }}
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
                        <h3 class="text-lg font-black text-slate-900">{{ $plan->name }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ $plan->description }}</p>
                    </div>

                    <div class="pt-2">
                        <span class="text-3xl font-black text-slate-900 font-mono">{{ number_format($plan->price, 0) }}</span>
                        <span class="text-xs text-slate-500 font-bold">TZS / {{ $plan->duration_days }} {{ __('Days') }}</span>
                    </div>

                    <!-- Plan Feature Matrix List -->
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
                            <i data-lucide="check" class="w-4 h-4 text-teal-600 flex-shrink-0"></i>
                            <span>{{ __('Service Coverage Areas:') }} <strong>{{ $plan->service_area_limit }} {{ __('Districts') }}</strong></span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i data-lucide="{{ $plan->priority_listing ? 'check' : 'minus' }}" class="w-4 h-4 {{ $plan->priority_listing ? 'text-teal-600' : 'text-slate-300' }} flex-shrink-0"></i>
                            <span class="{{ $plan->priority_listing ? 'font-bold text-slate-900' : 'text-slate-400' }}">{{ __('Priority Matching') }}</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i data-lucide="{{ $plan->is_featured ? 'star' : 'minus' }}" class="w-4 h-4 {{ $plan->is_featured ? 'text-amber-500 fill-amber-500' : 'text-slate-300' }} flex-shrink-0"></i>
                            <span class="{{ $plan->is_featured ? 'font-black text-amber-950' : 'text-slate-400' }}">⭐ {{ __('Featured Profile Badge') }}</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i data-lucide="check" class="w-4 h-4 text-teal-600 flex-shrink-0"></i>
                            <span class="capitalize">{{ $plan->analytics_level }} {{ __('Performance Analytics') }}</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i data-lucide="{{ $plan->priority_support ? 'check' : 'minus' }}" class="w-4 h-4 {{ $plan->priority_support ? 'text-teal-600' : 'text-slate-300' }} flex-shrink-0"></i>
                            <span class="{{ $plan->priority_support ? 'text-slate-900' : 'text-slate-400' }}">{{ __('Priority Support Desk') }}</span>
                        </li>
                    </ul>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    @if($isCurrent)
                        <button disabled class="w-full py-3 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold uppercase tracking-wider cursor-default">
                            ✓ {{ __('Current Active Plan') }}
                        </button>
                    @else
                        <a href="{{ route('technician.subscription.checkout', $plan->slug) }}" class="block w-full py-3.5 rounded-2xl {{ $plan->slug === 'professional' ? 'bg-teal-600 hover:bg-teal-700 text-white' : ($plan->slug === 'premium' ? 'bg-amber-500 hover:bg-amber-600 text-slate-950 font-black' : 'bg-slate-900 hover:bg-slate-800 text-white') }} font-bold text-xs uppercase tracking-wider text-center shadow transition">
                            {{ __('Select Plan') }} ({{ $plan->name }})
                        </a>
                    @endif
                </div>

            </div>
            @endforeach
        </div>
    </div>

    <!-- Subscription Billing History Timeline -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-base font-bold text-slate-900">{{ __('Subscription History & Receipts') }}</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-3">{{ __('Reference') }}</th>
                        <th class="p-3">{{ __('Plan') }}</th>
                        <th class="p-3">{{ __('Amount') }}</th>
                        <th class="p-3">{{ __('Payment Method') }}</th>
                        <th class="p-3">{{ __('Status') }}</th>
                        <th class="p-3">{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $pay)
                    <tr>
                        <td class="p-3 font-mono font-bold text-slate-900">{{ $pay->payment_reference }}</td>
                        <td class="p-3 font-semibold text-slate-800">{{ $pay->plan ? $pay->plan->name : 'N/A' }}</td>
                        <td class="p-3 font-bold text-slate-900">{{ $pay->formatted_amount }}</td>
                        <td class="p-3 capitalize text-slate-600">{{ str_replace('_', ' ', $pay->payment_method) }}</td>
                        <td class="p-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $pay->status_badge_classes }}">
                                {{ $pay->status }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-500">{{ $pay->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-slate-400">{{ __('No payment records found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
