@extends('layouts.admin')

@section('title', __('Complaints & Disputes'))
@section('page_title', __('Dispute Resolution Center'))

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">{{ __('Case ID') }}</th>
                        <th class="p-4">{{ __('Category') }}</th>
                        <th class="p-4">{{ __('Client') }}</th>
                        <th class="p-4">{{ __('Technician') }}</th>
                        <th class="p-4">{{ __('Request Ref') }}</th>
                        <th class="p-4">{{ __('Status') }}</th>
                        <th class="p-4 text-right">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($complaints as $comp)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-4 font-mono font-bold text-slate-900">#DIS-{{ sprintf('%04d', $comp->id) }}</td>
                        <td class="p-4 font-bold text-rose-700">{{ $comp->category_label }}</td>
                        <td class="p-4 text-slate-800">{{ $comp->client->full_name }}</td>
                        <td class="p-4 text-slate-800">{{ $comp->serviceRequest->technician->full_name }}</td>
                        <td class="p-4 font-mono text-slate-500">{{ $comp->serviceRequest->reference_no }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $comp->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : ($comp->status === 'under_review' ? 'bg-indigo-100 text-indigo-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ __(str_replace('_', ' ', $comp->status)) }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.complaints.show', $comp->id) }}" class="px-3.5 py-1.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                {{ __('Mediate & Resolve') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">{{ __('No complaints or disputes filed.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $complaints->links() }}
    </div>

</div>
@endsection