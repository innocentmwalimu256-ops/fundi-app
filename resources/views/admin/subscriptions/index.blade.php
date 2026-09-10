@extends('layouts.admin')

@section('title', __('Technician Subscriptions'))
@section('page_title', __('Technician Subscription Management'))

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('Active Subscriptions') }}</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($stats['active_subs']) }}</h3>
                <span class="text-[11px] text-slate-400">{{ __('Total verified active techs') }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('Expired Subscriptions') }}</p>
                <h3 class="text-2xl font-black text-rose-600 mt-1">{{ number_format($stats['expired_subs']) }}</h3>
                <span class="text-[11px] text-slate-400">{{ __('Paused marketplace access') }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                <i data-lucide="lock" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('Monthly Sub Revenue') }}</p>
                <h3 class="text-xl font-black text-slate-900 mt-1">TZS {{ number_format($stats['monthly_revenue'], 0) }}</h3>
                <span class="text-[11px] text-teal-700 font-bold">{{ __('Current month') }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                <i data-lucide="credit-card" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('Total Technicians') }}</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_technicians']) }}</h3>
                <span class="text-[11px] text-slate-400">{{ __('Registered on platform') }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center justify-between border-b border-slate-200 text-xs font-bold">
        <div class="flex space-x-4">
            <a href="{{ route('admin.subscriptions.index') }}" class="py-3 border-b-2 {{ !$status ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                {{ __('All Subscriptions') }}
            </a>
            <a href="{{ route('admin.subscriptions.index', ['status' => 'active']) }}" class="py-3 border-b-2 {{ $status === 'active' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                {{ __('Active Only') }}
            </a>
            <a href="{{ route('admin.subscriptions.index', ['status' => 'free_trial']) }}" class="py-3 border-b-2 {{ $status === 'free_trial' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                {{ __('Free Trial') }}
            </a>
            <a href="{{ route('admin.subscriptions.index', ['status' => 'expired']) }}" class="py-3 border-b-2 {{ $status === 'expired' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                {{ __('Expired') }}
            </a>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.subscriptions.plans') }}" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                {{ __('Manage Plans') }}
            </a>
            <a href="{{ route('admin.subscriptions.revenue') }}" class="px-3.5 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition">
                {{ __('Revenue Report') }}
            </a>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">{{ __('Technician') }}</th>
                        <th class="p-4">{{ __('Plan') }}</th>
                        <th class="p-4">{{ __('Price') }}</th>
                        <th class="p-4">{{ __('Status') }}</th>
                        <th class="p-4">{{ __('Started Date') }}</th>
                        <th class="p-4">{{ __('Expires Date') }}</th>
                        <th class="p-4">{{ __('Days Left') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subscriptions as $sub)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-4">
                            <span class="font-bold text-slate-900 block">{{ $sub->user->full_name }}</span>
                            <span class="text-[10px] text-slate-400">{{ $sub->user->phone }}</span>
                        </td>
                        <td class="p-4 font-semibold text-slate-800">{{ $sub->plan ? __($sub->plan->name) : 'N/A' }}</td>
                        <td class="p-4 font-mono font-bold text-slate-900">TZS {{ number_format($sub->plan->price ?? 0) }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $sub->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ __(ucfirst($sub->status)) }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-500">{{ $sub->starts_at ? $sub->starts_at->format('d M Y') : 'N/A' }}</td>
                        <td class="p-4 text-slate-500">{{ $sub->ends_at ? $sub->ends_at->format('d M Y') : 'N/A' }}</td>
                        <td class="p-4 font-bold {{ ($sub->days_remaining ?? 0) < 5 ? 'text-rose-600' : 'text-slate-700' }}">
                            {{ $sub->days_remaining ?? 0 }} {{ __('days') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">{{ __('No subscriptions found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $subscriptions->links() }}
    </div>

</div>
@endsection