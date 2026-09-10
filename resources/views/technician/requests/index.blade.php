@extends('layouts.app')

@section('title', __('Service Requests') . ' — FUNDI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ __('Service Requests') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ __('Review client issues, accept requests, and send quotations') }}</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex border-b border-slate-200 overflow-x-auto space-x-2 text-xs font-bold scrollbar-none">
        <a href="{{ route('technician.requests.index', ['tab' => 'new']) }}" class="py-3 px-4 border-b-2 transition whitespace-nowrap {{ $tab === 'new' ? 'border-teal-700 text-teal-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('New Incoming') }} ({{ $counts['new'] }})
        </a>
        <a href="{{ route('technician.requests.index', ['tab' => 'accepted']) }}" class="py-3 px-4 border-b-2 transition whitespace-nowrap {{ $tab === 'accepted' ? 'border-teal-700 text-teal-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Quotation Stage') }} ({{ $counts['accepted'] }})
        </a>
        <a href="{{ route('technician.requests.index', ['tab' => 'jobs']) }}" class="py-3 px-4 border-b-2 transition whitespace-nowrap {{ $tab === 'jobs' ? 'border-teal-700 text-teal-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Scheduled Jobs') }} ({{ $counts['jobs'] }})
        </a>
        <a href="{{ route('technician.requests.index', ['tab' => 'history']) }}" class="py-3 px-4 border-b-2 transition whitespace-nowrap {{ $tab === 'history' ? 'border-teal-700 text-teal-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Completed & History') }} ({{ $counts['history'] }})
        </a>
    </div>

    <!-- Requests Stream -->
    <div class="space-y-4">
        @forelse($requests as $req)
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 hover:border-teal-300 hover:shadow-md transition flex flex-col md:flex-row md:items-center justify-between gap-5">
            
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-teal-300 font-black text-sm flex items-center justify-center flex-shrink-0">
                    {{ $req->client->initials }}
                </div>

                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-bold text-slate-900 font-mono">{{ $req->reference_no }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $req->status_badge_classes }}">
                            {{ $req->status_label }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $req->urgency_badge_classes }}">
                            {{ ucfirst($req->urgency) }}
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900">
                        {{ $req->service->name }} {{ __('from') }} {{ $req->client->full_name }}
                    </h3>

                    <p class="text-xs text-slate-600 line-clamp-1 max-w-xl">
                        {{ $req->description }}
                    </p>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 pt-1">
                        <span class="flex items-center"><i data-lucide="phone" class="w-3.5 h-3.5 mr-1"></i> {{ $req->client->phone }}</span>
                        <span class="flex items-center"><i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1"></i> {{ $req->location }}</span>
                        <span class="flex items-center"><i data-lucide="calendar" class="w-3.5 h-3.5 mr-1"></i> {{ $req->preferred_date->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100 flex-shrink-0">
                <a href="{{ route('technician.requests.show', $req->id) }}" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center space-x-1">
                    <span>{{ __('Manage Request') }}</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

        </div>
        @empty
        <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 text-slate-400 text-xs">
            {{ __('No requests found in this tab.') }}
        </div>
        @endforelse
    </div>

    <div>
        {{ $requests->links() }}
    </div>

</div>
@endsection
