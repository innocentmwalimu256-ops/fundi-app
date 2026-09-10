@extends('layouts.app')

@section('title', __('Client Reviews & Reputation') . ' — FUNDI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ __('Client Reviews & Reputation') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ __('Verified feedback from clients after completed jobs') }}</p>
        </div>
    </div>

    <!-- Rating Summary Banner -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="flex items-center space-x-6">
            <div class="text-center min-w-[120px]">
                @if(($profile->average_rating ?? 0) > 0)
                <p class="text-4xl sm:text-5xl font-black text-slate-900 flex items-center justify-center">
                    <span class="text-amber-400 mr-2">★</span> {{ number_format($profile->average_rating, 1) }}
                </p>
                <span class="text-xs font-semibold text-slate-400 mt-1 block">{{ __('Out of 5.0 Rating') }}</span>
                @else
                <div class="inline-flex flex-col items-center justify-center">
                    <p class="text-3xl sm:text-4xl font-black text-slate-400 flex items-center justify-center">
                        <span class="text-slate-300 mr-1.5">★</span> --
                    </p>
                    <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-lg mt-1 border border-amber-200">{{ __('No Active Subscription') }}</span>
                </div>
                @endif
            </div>

            <div class="h-12 w-px bg-slate-200 hidden sm:block"></div>

            <div class="space-y-1">
                @if(($profile->total_reviews ?? 0) > 0)
                    <p class="text-sm font-bold text-slate-900">
                        {{ $profile->total_reviews }} {{ __('Total Verified Reviews') }}
                    </p>
                @elseif(($profile->average_rating ?? 0) > 0)
                    <p class="text-sm font-bold text-teal-700 flex items-center space-x-1">
                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                        <span>{{ __('Subscription Plan Rating') }} ({{ optional(optional(Auth::user())->subscription)->plan->name ?? __('Active Plan') }})</span>
                    </p>
                @else
                    <p class="text-sm font-bold text-slate-900">{{ __('No Client Reviews Yet') }}</p>
                @endif
                <p class="text-xs text-slate-500">
                    {{ $profile->completed_jobs_count ?? 0 }} {{ __('successful jobs completed') }}
                </p>
                <p class="text-[11px] text-slate-400">
                    {{ __('Ratings are determined by your subscription plan tier and updated dynamically as you receive verified client reviews.') }}
                </p>
            </div>
        </div>

        <span class="px-4 py-2 rounded-2xl bg-teal-50 text-teal-800 border border-teal-200 text-xs font-bold self-start sm:self-auto flex items-center space-x-1.5">
            <i data-lucide="shield-check" class="w-4 h-4 text-teal-600"></i>
            <span>{{ __('Verified FUNDI Badge Active') }}</span>
        </span>
    </div>

    <!-- Reviews Stream -->
    <div class="space-y-4">
        @forelse($reviews as $rev)
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-200 text-slate-800 font-bold text-xs flex items-center justify-center">
                        {{ $rev->client->initials }}
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">{{ $rev->client->full_name }}</h3>
                        <p class="text-xs text-slate-400 font-mono">{{ $rev->serviceRequest->reference_no }} • {{ $rev->serviceRequest->service->name }}</p>
                    </div>
                </div>

                <div class="text-right">
                    <div class="flex items-center text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            <i data-lucide="star" class="w-4 h-4 {{ $i <= $rev->rating ? 'fill-amber-400 stroke-amber-400' : 'text-slate-200 stroke-slate-200' }}"></i>
                        @endfor
                    </div>
                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ $rev->created_at->format('d M Y') }}</span>
                </div>
            </div>

            @if($rev->comment)
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed">
                "{{ $rev->comment }}"
            </div>
            @endif

            <!-- Sub criteria -->
            <div class="flex flex-wrap gap-4 text-[11px] text-slate-500 pt-1">
                <span>{{ __('Quality') }}: <strong>★ {{ $rev->quality ?? $rev->rating }}/5</strong></span>
                <span>{{ __('Punctuality') }}: <strong>★ {{ $rev->punctuality ?? $rev->rating }}/5</strong></span>
                <span>{{ __('Professionalism') }}: <strong>★ {{ $rev->professionalism ?? $rev->rating }}/5</strong></span>
                <span>{{ __('Communication') }}: <strong>★ {{ $rev->communication ?? $rev->rating }}/5</strong></span>
            </div>
        </div>
        @empty
        <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 text-slate-400 text-xs">
            {{ __('No client reviews received yet. Reviews will automatically populate as clients confirm completed jobs.') }}
        </div>
        @endforelse
    </div>

    <div>
        {{ $reviews->links() }}
    </div>

</div>
@endsection
