@extends('layouts.admin')

@section('title', __('Inspect Request') . ' ' . $request->reference_no)
@section('page_title', __('Transaction File #') . $request->reference_no)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <a href="{{ route('admin.requests.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-800">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> {{ __('Back to Requests Monitor') }}
    </a>

    <!-- Top Summary Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Request Reference') }}</span>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-mono">{{ $request->reference_no }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Created on') }} {{ $request->created_at->format('d M Y, H:i') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $request->status_badge_classes }} self-start sm:self-auto">
                {{ __($request->status_label) }}
            </span>
        </div>

        <!-- Participants -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ __('Client') }}</span>
                <p class="text-sm font-bold text-slate-900 mt-1">{{ $request->client->full_name }}</p>
                <p class="text-xs text-slate-500">{{ $request->client->email }} • {{ $request->client->phone }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ __('Technician') }}</span>
                <p class="text-sm font-bold text-slate-900 mt-1">{{ $request->technician->full_name }}</p>
                <p class="text-xs text-slate-500">{{ $request->technician->phone }} • {{ __($request->technician->technicianProfile->professional_title ?? 'Tech') }}</p>
            </div>
        </div>

        <!-- Details -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">{{ __('Problem Description') }}</h3>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed">
                {{ $request->description }}
            </div>
        </div>

        <!-- Quotation if exists -->
        @if($request->latestQuotation)
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">{{ __('Quotation Audit') }}</h3>
            <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 text-xs space-y-1">
                <div class="flex justify-between">
                    <span class="text-slate-600">{{ __('Labour') }}: TZS {{ number_format($request->latestQuotation->labour_cost, 0) }}</span>
                    <span class="text-slate-600">{{ __('Materials') }}: TZS {{ number_format($request->latestQuotation->materials_cost, 0) }}</span>
                    <span class="text-slate-600">{{ __('Transport') }}: TZS {{ number_format($request->latestQuotation->transport_cost, 0) }}</span>
                    <span class="font-black text-slate-900">{{ __('Total') }}: {{ $request->latestQuotation->formatted_total }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Messages Trail -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">{{ __('Message History') }} ({{ $request->messages->count() }})</h3>
            <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar p-3 bg-slate-50 rounded-2xl border border-slate-200">
                @forelse($request->messages as $msg)
                <div class="text-xs p-2 rounded-xl bg-white border border-slate-100">
                    <span class="font-bold text-slate-900">{{ $msg->sender->full_name }}:</span>
                    <span class="text-slate-700">{{ $msg->message_text }}</span>
                    <span class="text-[10px] text-slate-400 float-right">{{ $msg->sent_at->format('H:i, d M') }}</span>
                </div>
                @empty
                <p class="text-center text-xs text-slate-400 py-4">{{ __('No messages recorded for this transaction.') }}</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection