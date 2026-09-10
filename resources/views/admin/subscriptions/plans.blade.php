@extends('layouts.admin')

@section('title', __('Subscription Plans Management'))
@section('page_title', __('Dynamic Subscription Plans'))

@section('content')
<div class="space-y-6" x-data="{ createModal: false, editModal: false, activePlan: {} }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">{{ __('Configured Marketplace Plans') }}</h2>
            <p class="text-xs text-slate-500">{{ __('Configure technician subscription tiers, pricing, duration, and feature limits') }}</p>
        </div>
        <button type="button" @click="createModal = true" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>{{ __('Create New Plan') }}</span>
        </button>
    </div>

    <!-- Plans Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-6 relative">
            <div class="space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">{{ __($plan->name) }}</h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $plan->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $plan->is_active ? __('Active') : __('Disabled') }}
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-slate-900 font-mono">{{ number_format($plan->price, 0) }}</span>
                        <span class="text-[10px] text-slate-400 block font-bold">TZS / {{ $plan->duration_days }} {{ __('days') }}</span>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">{{ __($plan->description) }}</p>

                <!-- Limits -->
                <div class="p-3 bg-slate-50 rounded-xl space-y-1.5 text-xs text-slate-700">
                    <div class="flex justify-between">
                        <span>{{ __('Request Limit:') }}</span>
                        <span class="font-bold">{{ $plan->request_limit === 0 ? __('Unlimited') : $plan->request_limit . ' / ' . __('month') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Contact Access:') }}</span>
                        <span class="font-bold text-teal-700">{{ $plan->contact_access ? __('Enabled') : __('Disabled') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Priority Listing:') }}</span>
                        <span class="font-bold {{ $plan->priority_listing ? 'text-teal-700' : 'text-slate-400' }}">{{ $plan->priority_listing ? __('Yes') : __('No') }}</span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-slate-200 text-slate-500">
                        <span>{{ __('Active Subscriptions:') }}</span>
                        <span class="font-bold text-slate-900">{{ $plan->subscriptions_count }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <form method="POST" action="{{ route('admin.subscriptions.plans.toggle', $plan->id) }}">
                    @csrf
                    <button type="submit" class="text-xs font-bold {{ $plan->is_active ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                        {{ $plan->is_active ? __('Disable Plan') : __('Activate Plan') }}
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Create Plan Modal -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto" @click.outside="createModal = false">
            <h3 class="text-base font-bold text-slate-900">{{ __('Create Subscription Plan') }}</h3>

            <form method="POST" action="{{ route('admin.subscriptions.plans.store') }}" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">{{ __('Plan Name') }}</label>
                    <input type="text" name="name" required placeholder="e.g. Starter Pro" class="w-full p-2.5 rounded-xl border border-slate-200 bg-slate-50">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">{{ __('Price (TZS)') }}</label>
                        <input type="number" name="price" required min="0" placeholder="25000" class="w-full p-2.5 rounded-xl border border-slate-200 bg-slate-50">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">{{ __('Duration (Days)') }}</label>
                        <input type="number" name="duration_days" required value="30" class="w-full p-2.5 rounded-xl border border-slate-200 bg-slate-50">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">{{ __('Description') }}</label>
                    <input type="text" name="description" placeholder="Brief plan summary" class="w-full p-2.5 rounded-xl border border-slate-200 bg-slate-50">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="createModal = false" class="px-4 py-2 font-bold text-slate-600 rounded-xl hover:bg-slate-100">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-5 py-2 font-bold bg-teal-600 hover:bg-teal-700 text-white rounded-xl shadow">{{ __('Create New Plan') }}</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection