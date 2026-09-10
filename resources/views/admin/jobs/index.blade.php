@extends('layouts.admin')

@section('title', __('Jobs Monitor'))
@section('page_title', __('Scheduled & In-Progress Jobs Monitor'))

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">{{ __('Reference') }}</th>
                        <th class="p-4">{{ __('Client') }}</th>
                        <th class="p-4">{{ __('Technician') }}</th>
                        <th class="p-4">{{ __('Service') }}</th>
                        <th class="p-4">{{ __('Quote Total') }}</th>
                        <th class="p-4">{{ __('Status') }}</th>
                        <th class="p-4 text-right">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jobs as $jobReq)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-4 font-mono font-bold text-slate-900">{{ $jobReq->reference_no }}</td>
                        <td class="p-4 font-semibold text-slate-900">{{ $jobReq->client->full_name }}</td>
                        <td class="p-4 font-semibold text-slate-900">{{ $jobReq->technician->full_name }}</td>
                        <td class="p-4 text-slate-600">{{ __($jobReq->service->name) }}</td>
                        <td class="p-4 font-bold text-slate-900">{{ $jobReq->latestQuotation?->formatted_total ?? 'N/A' }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $jobReq->status_badge_classes }}">
                                {{ __($jobReq->status_label) }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.requests.show', $jobReq->id) }}" class="text-teal-700 font-bold hover:underline">
                                {{ __('Inspect') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">{{ __('No scheduled jobs found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $jobs->links() }}
    </div>

</div>
@endsection