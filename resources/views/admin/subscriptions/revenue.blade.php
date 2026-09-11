@extends('layouts.admin')

@section('title', __('Platform Monetization & Revenue Analytics'))
@section('page_title', __('Marketplace Monetization & Profits'))

@section('content')
<div class="space-y-8">

    <!-- Action Bar & Financial Export -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-sm font-bold text-slate-900">{{ __('Financial Statements & Audit Export') }}</h3>
            <p class="text-xs text-slate-500">{{ __('Download detailed financial statements with technician subscriptions and client connection fees.') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.subscriptions.revenue.export.csv') }}" class="px-4 py-2.5 rounded-2xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition flex items-center space-x-2 border border-emerald-200">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
                <span>{{ __('Export Financial CSV') }}</span>
            </a>
            <a href="{{ route('admin.reports.executive-summary') }}" class="px-4 py-2.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition shadow-xs flex items-center space-x-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>{{ __('Executive PDF Report') }}</span>
            </a>
        </div>
    </div>

    <!-- Revenue KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Total Platform Profit') }}</p>
            <h3 class="text-2xl font-black text-teal-700 mt-1">TZS {{ number_format($revenue['all_time'], 0) }}</h3>
            <span class="text-[11px] text-teal-700 font-bold">{{ __('Combined Business Revenue') }}</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Tech Subscriptions') }}</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">TZS {{ number_format($revenue['sub_revenue'], 0) }}</h3>
            <span class="text-[11px] text-slate-500">Starter, Pro, Premium</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Client Connection Fees') }}</p>
            <h3 class="text-2xl font-black text-emerald-700 mt-1">TZS {{ number_format($revenue['client_connection_revenue'], 0) }}</h3>
            <span class="text-[11px] text-emerald-600 font-bold">{{ __('TZS 500 per request') }}</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('This Month') }}</p>
            <h3 class="text-2xl font-black text-teal-700 mt-1">TZS {{ number_format($revenue['this_month'], 0) }}</h3>
            <span class="text-[11px] text-teal-600 font-bold">{{ __('Current month total') }}</span>
        </div>

    </div>

    <!-- Revenue Breakdown by Stream -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Stream 1: Technician Subscriptions -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900">{{ __('1. Technician Subscription Tiers') }}</h3>
            
            <div class="space-y-4 pt-1">
                @foreach($planBreakdown as $p)
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-slate-900">{{ __($p->name) }} {{ __('Plan') }}</span>
                        <span class="text-slate-600">TZS {{ number_format($p->payments_sum_amount ?: 0, 0) }} ({{ $p->subscriptions_count }} subs)</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-teal-600 rounded-full" style="width: {{ $revenue['all_time'] > 0 ? min(100, (($p->payments_sum_amount ?: 0) / $revenue['all_time']) * 100) : 0 }}%;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Stream 2: Recent Client Connection Fees -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">{{ __('2. Client Request Connection Fees') }}</h3>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-xl">{{ __('TZS 500 / Request') }}</span>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                @forelse($recentRequests as $req)
                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900">{{ $req->client->full_name }} &rarr; {{ $req->technician->full_name }}</p>
                        <p class="text-slate-500 text-[11px]">{{ __($req->service->name) }} • {{ $req->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <span class="font-mono font-bold text-emerald-700">+ TZS {{ number_format($req->connection_fee ?? 500, 0) }}</span>
                        <span class="text-[10px] text-slate-400 block font-mono">{{ $req->connection_fee_reference ?: $req->reference_no }}</span>
                    </div>
                </div>
                @empty
                <div class="py-6 text-center text-slate-400 text-xs">
                    {{ __('No client connection fee transactions yet.') }}
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection