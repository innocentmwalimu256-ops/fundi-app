@extends('layouts.admin')

@section('title', __('Global Requests Monitor'))
@section('page_title', __('All Service Requests'))

@section('content')
<div class="space-y-6">

    <!-- Filter Bar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.requests.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="q" value="{{ $search }}" placeholder="{{ __('Search by reference, client or tech name...') }}" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
            </div>
            <div>
                <select name="status" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="accepted" {{ $status === 'accepted' ? 'selected' : '' }}>{{ __('Accepted') }}</option>
                    <option value="quotation_pending" {{ $status === 'quotation_pending' ? 'selected' : '' }}>{{ __('Quotation Sent') }}</option>
                    <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                    <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                    <option value="client_confirmed" {{ $status === 'client_confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                    <option value="reviewed" {{ $status === 'reviewed' ? 'selected' : '' }}>{{ __('Reviewed') }}</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-sm">
                    {{ __('Filter Requests') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">{{ __('Reference') }}</th>
                        <th class="p-4">{{ __('Client') }}</th>
                        <th class="p-4">{{ __('Technician') }}</th>
                        <th class="p-4">{{ __('Service') }}</th>
                        <th class="p-4">{{ __('Location') }}</th>
                        <th class="p-4">{{ __('Status') }}</th>
                        <th class="p-4 text-right">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $req)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-4 font-mono font-bold text-slate-900">{{ $req->reference_no }}</td>
                        <td class="p-4 text-slate-800 font-semibold">{{ $req->client->full_name }}</td>
                        <td class="p-4 text-slate-800 font-semibold">{{ $req->technician->full_name }}</td>
                        <td class="p-4 text-slate-600">{{ __($req->service->name) }}</td>
                        <td class="p-4 text-slate-500 max-w-[140px] truncate">{{ $req->location }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $req->status_badge_classes }}">
                                {{ __($req->status_label) }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.requests.show', $req->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-bold rounded-lg transition">
                                {{ __('Inspect') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">{{ __('No requests found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $requests->links() }}
    </div>

</div>
@endsection