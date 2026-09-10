@extends('layouts.admin')

@section('title', __('Reports & Analytics'))
@section('page_title', __('Marketplace Reporting & Business Analytics'))

@section('content')
<div class="space-y-6">

    <!-- Action Bar & Report Generator -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-sm font-bold text-slate-900">{{ __('Automated Reporting & Data Export') }}</h3>
            <p class="text-xs text-slate-500">{{ __('Download operational datasets or print executive business performance summaries.') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.reports.export.csv') }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition flex items-center space-x-2">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
                <span>{{ __('Export Analytics CSV') }}</span>
            </a>
            <a href="{{ route('admin.reports.executive-summary') }}" class="px-4 py-2.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition shadow-xs flex items-center space-x-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>{{ __('Executive PDF Report') }}</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Summary -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Total Jobs Handled') }}</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $jobStats['total'] }}</h3>
            <span class="text-[11px] text-slate-500">{{ __('Across all categories') }}</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Completion Rate') }}</p>
            @php $rate = $jobStats['total'] > 0 ? round(($jobStats['completed'] / $jobStats['total']) * 100, 1) : 100; @endphp
            <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ $rate }}%</h3>
            <span class="text-[11px] text-emerald-600 font-semibold">{{ $jobStats['completed'] }} {{ __('successfully closed') }}</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Active In-Flight') }}</p>
            <h3 class="text-2xl font-black text-indigo-600 mt-1">{{ $jobStats['active'] }}</h3>
            <span class="text-[11px] text-slate-500">{{ __('Currently executing') }}</span>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Cancellation Rate') }}</p>
            @php $cancelRate = $jobStats['total'] > 0 ? round(($jobStats['cancelled'] / $jobStats['total']) * 100, 1) : 0; @endphp
            <h3 class="text-2xl font-black text-rose-600 mt-1">{{ $cancelRate }}%</h3>
            <span class="text-[11px] text-rose-500">{{ $jobStats['cancelled'] }} {{ __('cancelled') }}</span>
        </div>
    </div>

    <!-- 2 Columns: Category Demand Analytics + Top Performing Technicians -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Service Demand Breakdown -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900">{{ __('Service Category Demand Volume') }}</h2>
            
            <div class="space-y-3">
                @foreach($serviceStats as $s)
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-slate-800">{{ __($s->name) }}</span>
                        <span class="text-teal-700">{{ $s->requests_count }} {{ __('requests') }}</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        @php $pct = $jobStats['total'] > 0 ? min(100, ($s->requests_count / $jobStats['total']) * 100) : 10; @endphp
                        <div class="h-full bg-teal-600 rounded-full" style="width: {{ max(5, $pct) }}%;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Performing Technicians -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900">{{ __('Top-Rated Active Technicians') }}</h2>
            
            <div class="divide-y divide-slate-100">
                @foreach($topTechnicians as $topTech)
                @php $tp = $topTech->technicianProfile; @endphp
                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-teal-300 font-bold text-xs flex items-center justify-center">
                            {{ $topTech->initials }}
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">{{ $topTech->full_name }}</h4>
                            <p class="text-[10px] text-slate-500">{{ __($tp->professional_title ?? 'Technician') }}</p>
                        </div>
                    </div>

                    <div class="text-right text-xs">
                        <span class="font-bold text-amber-500">★ {{ number_format($tp->average_rating ?? 5.0, 1) }}</span>
                        <span class="text-[10px] text-slate-400 block">{{ $tp->completed_jobs_count ?? 0 }} {{ __('completed jobs') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection