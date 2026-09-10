@extends('layouts.admin')

@section('title', __('Platform Settings'))
@section('page_title', __('Marketplace Configuration & Rules'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-6">
        
        <div>
            <h1 class="text-xl font-black text-slate-900">{{ __('Platform Configuration') }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('Control regional parameters, currency, verification rules, and dispute policies') }}</p>
        </div>

        <div class="space-y-4 text-xs divide-y divide-slate-100">
            <div class="flex items-center justify-between pt-3">
                <div>
                    <p class="font-bold text-slate-900">{{ __('Platform Operating Currency') }}</p>
                    <p class="text-slate-400">{{ __('Default quotation and service pricing standard') }}</p>
                </div>
                <span class="font-bold text-slate-800 bg-slate-100 px-3 py-1.5 rounded-xl">{{ __('TZS (Tanzanian Shilling)') }}</span>
            </div>

            <div class="flex items-center justify-between pt-3">
                <div>
                    <p class="font-bold text-slate-900">{{ __('Technician Verification Requirement') }}</p>
                    <p class="text-slate-400">{{ __('Strict administrator review before appearing in marketplace') }}</p>
                </div>
                <span class="font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">{{ __('Enforced (Rule 2 & 3)') }}</span>
            </div>

            <div class="flex items-center justify-between pt-3">
                <div>
                    <p class="font-bold text-slate-900">{{ __('Review Submission Policy') }}</p>
                    <p class="text-slate-400">{{ __('Only clients who completed and confirmed jobs can submit reviews') }}</p>
                </div>
                <span class="font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">{{ __('Enforced (Rule 10)') }}</span>
            </div>

            <div class="flex items-center justify-between pt-3">
                <div>
                    <p class="font-bold text-slate-900">{{ __('Central Lifecycle Engine') }}</p>
                    <p class="text-slate-400">Requested &rarr; Quoting &rarr; Scheduled &rarr; On The Way &rarr; In Progress &rarr; Completed</p>
                </div>
                <span class="font-bold text-teal-700 bg-teal-50 px-3 py-1.5 rounded-xl border border-teal-200">{{ __('Active') }}</span>
            </div>
        </div>

    </div>

</div>
@endsection