@extends('layouts.app')

@section('title', __('Scheduled & Active Jobs'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ __('Scheduled & Active Jobs') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ __('Manage job lifecycle progress, start work, and mark completion') }}</p>
        </div>
    </div>

    <div class="flex border-b border-slate-200 space-x-4 text-xs font-bold">
        <a href="{{ route('technician.jobs.index', ['status' => 'all']) }}" class="py-3 border-b-2 {{ $status === 'all' ? 'border-brand-700 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('All Jobs') }}
        </a>
        <a href="{{ route('technician.jobs.index', ['status' => 'active']) }}" class="py-3 border-b-2 {{ $status === 'active' ? 'border-brand-700 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Active / In Progress') }}
        </a>
        <a href="{{ route('technician.jobs.index', ['status' => 'completed']) }}" class="py-3 border-b-2 {{ $status === 'completed' ? 'border-brand-700 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Completed History') }}
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @forelse($jobs as $jobReq)
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 font-mono">{{ $jobReq->reference_no }}</span>
                    <h3 class="text-base font-bold text-slate-900 mt-0.5">{{ __($jobReq->service->name) }}</h3>
                    <p class="text-xs text-slate-600">{{ __('Client') }}: <strong>{{ $jobReq->client->full_name }}</strong> ({{ $jobReq->client->phone }})</p>
                    <p class="text-xs text-slate-500 mt-1"><i data-lucide="map-pin" class="w-3.5 h-3.5 inline mr-1 text-slate-400"></i>{{ $jobReq->location }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $jobReq->status_badge_classes }}">
                    {{ __($jobReq->status_label) }}
                </span>
            </div>

            <!-- One-Tap Status Update Actions (Section 23 & 24) -->
            <div class="pt-3 border-t border-slate-100 space-y-2">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('Update Progress') }}:</p>
                <div class="grid grid-cols-3 gap-2">
                    <form method="POST" action="{{ route('technician.jobs.status', $jobReq->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="on_the_way">
                        <button type="submit" {{ in_array($jobReq->status, ['on_the_way', 'in_progress', 'completed', 'client_confirmed', 'reviewed']) ? 'disabled' : '' }} class="w-full py-2 px-2 rounded-xl text-center text-xs font-bold transition {{ $jobReq->status === 'on_the_way' ? 'bg-teal-700 text-white' : 'bg-teal-50 text-teal-800 hover:bg-teal-100 border border-teal-200' }}">
                            {{ __('On The Way') }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('technician.jobs.status', $jobReq->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="in_progress">
                        <button type="submit" {{ in_array($jobReq->status, ['in_progress', 'completed', 'client_confirmed', 'reviewed']) ? 'disabled' : '' }} class="w-full py-2 px-2 rounded-xl text-center text-xs font-bold transition {{ $jobReq->status === 'in_progress' ? 'bg-indigo-700 text-white' : 'bg-indigo-50 text-indigo-800 hover:bg-indigo-100 border border-indigo-200' }}">
                            {{ __('In Progress') }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('technician.jobs.status', $jobReq->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" {{ in_array($jobReq->status, ['completed', 'client_confirmed', 'reviewed']) ? 'disabled' : '' }} class="w-full py-2 px-2 rounded-xl text-center text-xs font-bold transition {{ $jobReq->status === 'completed' ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}">
                            {{ __('Completed') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-2">
                <a href="{{ route('technician.requests.show', $jobReq->id) }}" class="text-brand-700 font-bold hover:underline">
                    {{ __('View Job Details') }} &rarr;
                </a>
                <a href="{{ route('messages.index', ['request_id' => $jobReq->id]) }}" class="text-slate-500 hover:text-slate-800 flex items-center">
                    <i data-lucide="message-square" class="w-3.5 h-3.5 mr-1"></i> {{ __('Message') }}
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-2 p-12 text-center bg-white rounded-3xl border border-slate-200 text-slate-400 text-xs">
            {{ __('No jobs found in this section.') }}
        </div>
        @endforelse
    </div>

    <div>
        {{ $jobs->links() }}
    </div>

</div>
@endsection
