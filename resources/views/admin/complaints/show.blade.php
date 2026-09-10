@extends('layouts.admin')

@section('title', __('Resolve Dispute #') . 'DIS-' . sprintf('%04d', $complaint->id))
@section('page_title', __('Dispute Investigation & Evidence #') . 'DIS-' . sprintf('%04d', $complaint->id))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <a href="{{ route('admin.complaints.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-800">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> {{ __('Back to Disputes') }}
    </a>

    <!-- Complaint Case Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-rose-600 font-mono">{{ __('Dispute #') }}DIS-{{ sprintf('%04d', $complaint->id) }}</span>
                <h1 class="text-xl font-black text-slate-900 mt-0.5">{{ $complaint->category_label }}</h1>
                <p class="text-xs text-slate-400 mt-0.5">{{ __('Connected to Request Ref:') }} {{ $complaint->serviceRequest->reference_no }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $complaint->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                {{ __(str_replace('_', ' ', $complaint->status)) }}
            </span>
        </div>

        <!-- Participants Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ __('Complainant (Client)') }}</span>
                <p class="font-bold text-slate-900 mt-0.5">{{ $complaint->client->full_name }}</p>
                <p class="text-slate-500">{{ $complaint->client->phone }} • {{ $complaint->client->email }}</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ __('Reported Technician') }}</span>
                <p class="font-bold text-slate-900 mt-0.5">{{ $complaint->serviceRequest->technician->full_name }}</p>
                <p class="text-slate-500">{{ $complaint->serviceRequest->technician->phone }}</p>
            </div>
        </div>

        <!-- Problem Statement -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">{{ __('Client\'s Complaint Statement') }}</h3>
            <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-100 text-xs text-rose-950 leading-relaxed">
                {{ $complaint->description }}
            </div>
        </div>

        <!-- Attached Physical / Digital Evidence -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">{{ __('Attached Dispute Evidence') }}</h3>
            @if($complaint->evidences->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($complaint->evidences as $ev)
                    <div class="bg-slate-50 rounded-2xl p-2 border border-slate-200 text-center space-y-1">
                        @if($ev->file_type === 'image')
                        <a href="{{ asset('storage/' . $ev->file_path) }}" target="_blank" class="block aspect-square rounded-xl overflow-hidden bg-slate-200">
                            <img src="{{ asset('storage/' . $ev->file_path) }}" alt="Evidence" class="w-full h-full object-cover">
                        </a>
                        @else
                        <div class="aspect-square rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
                            <i data-lucide="file-text" class="w-8 h-8"></i>
                        </div>
                        @endif
                        <a href="{{ asset('storage/' . $ev->file_path) }}" target="_blank" class="text-[10px] font-bold text-teal-700 hover:underline block truncate">
                            {{ __('View File') }} &rarr;
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 italic">{{ __('No external photo or screenshot evidence was attached to this complaint.') }}</p>
            @endif
        </div>

        <!-- Resolution Form -->
        <div class="pt-4 border-t border-slate-100 space-y-4">
            <h3 class="text-base font-bold text-slate-900">{{ __('Administrator Resolution Finding') }}</h3>
            
            <form method="POST" action="{{ route('admin.complaints.resolve', $complaint->id) }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Set Resolution Status') }}</label>
                    <select name="status" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50">
                        <option value="under_review" {{ $complaint->status === 'under_review' ? 'selected' : '' }}>{{ __('Under Investigation') }}</option>
                        <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : '' }}>{{ __('Resolved (Formal Finding Recorded)') }}</option>
                        <option value="closed" {{ $complaint->status === 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Resolution Summary & Official Notes') }}</label>
                    <textarea name="resolution" rows="4" required placeholder="State findings, corrective actions taken (e.g. re-work scheduled, warning issued)..." class="w-full p-3.5 rounded-xl border border-slate-200 text-xs bg-slate-50">{{ old('resolution', $complaint->resolution) }}</textarea>
                </div>

                <button type="submit" class="px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                    {{ __('Save Resolution & Notify Parties') }}
                </button>
            </form>
        </div>

    </div>

</div>
@endsection