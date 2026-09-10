@extends('layouts.app')

@section('title', __('My Requests') . ' — FUNDI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Title & CTA -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ __('Service Requests & History') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ __('Track your requests, quotes, active jobs, and completion confirmations') }}</p>
        </div>
        <a href="{{ route('client.requests.create') }}" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-xs transition flex items-center justify-center space-x-1.5 flex-shrink-0">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>{{ __('New Request') }}</span>
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="flex border-b border-slate-200 overflow-x-auto space-x-2 text-xs font-bold scrollbar-none">
        <a href="{{ route('client.requests.index', ['tab' => 'all']) }}" class="py-3 px-4 border-b-2 transition whitespace-nowrap {{ $tab === 'all' ? 'border-teal-600 text-teal-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('All Requests') }} ({{ $counts['all'] }})
        </a>
        <a href="{{ route('client.requests.index', ['tab' => 'pending']) }}" class="py-3 px-4 border-b-2 transition whitespace-nowrap {{ $tab === 'pending' ? 'border-teal-600 text-teal-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Pending / Quotations') }} ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('client.requests.index', ['tab' => 'active']) }}" class="py-3 px-4 border-b-2 transition whitespace-nowrap {{ $tab === 'active' ? 'border-teal-600 text-teal-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Active Jobs') }} ({{ $counts['active'] }})
        </a>
        <a href="{{ route('client.requests.index', ['tab' => 'completed']) }}" class="py-3 px-4 border-b-2 transition whitespace-nowrap {{ $tab === 'completed' ? 'border-teal-600 text-teal-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Completed') }} ({{ $counts['completed'] }})
        </a>
        <a href="{{ route('client.requests.index', ['tab' => 'cancelled']) }}" class="py-3 px-4 border-b-2 transition whitespace-nowrap {{ $tab === 'cancelled' ? 'border-teal-600 text-teal-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Cancelled / Declined') }} ({{ $counts['cancelled'] }})
        </a>
    </div>

    <!-- Requests List -->
    <div class="space-y-4">
        @forelse($requests as $req)
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 hover:border-teal-500 hover:shadow-card transition flex flex-col md:flex-row md:items-center justify-between gap-5">
            
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-xl bg-slate-900 text-teal-300 font-black text-sm flex items-center justify-center flex-shrink-0 shadow-xs">
                    {{ $req->technician->initials }}
                </div>

                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-bold text-slate-500 font-mono">{{ $req->reference_no }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $req->status_badge_classes }}">
                            {{ $req->status_label }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $req->urgency_badge_classes }}">
                            {{ ucfirst($req->urgency) }}
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900">
                        {{ __($req->service->name) }} {{ __('Service with') }} {{ $req->technician->full_name }}
                    </h3>

                    <p class="text-xs text-slate-600 line-clamp-1 max-w-xl">
                        {{ $req->description }}
                    </p>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 pt-1">
                        <span class="flex items-center">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1 text-slate-400"></i> {{ $req->location }}
                        </span>
                        <span class="flex items-center">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 mr-1 text-slate-400"></i> {{ $req->preferred_date->format('d M Y') }}
                        </span>
                        @if($req->latestQuotation)
                        <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded">
                            {{ __('Quote:') }} {{ $req->latestQuotation->formatted_total }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center space-x-3 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100 flex-shrink-0">
                <a href="{{ route('client.requests.show', $req->id) }}" class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-xs transition flex items-center space-x-1">
                    <span>{{ __('Manage / View') }}</span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>

        </div>
        @empty
        <!-- Empty State -->
        <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <i data-lucide="clipboard-x" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">{{ __('No Service Requests Yet') }}</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                {{ __('You haven\'t requested any technician services in this category yet. Find a verified technician and send your first request!') }}
            </p>
            <div class="pt-2">
                <a href="{{ route('client.requests.create') }}" class="inline-flex items-center px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                    <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i>
                    <span>{{ __('Post First Request') }}</span>
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $requests->links() }}
    </div>

</div>
@endsection
