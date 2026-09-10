@extends('layouts.admin')

@section('title', __('System Audit Logs'))
@section('page_title', __('Security & Administrative Audit Logs'))

@section('content')
<div class="space-y-6">

    <!-- Search & Action filter -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="q" value="{{ $search }}" placeholder="{{ __('Search description, user or IP address...') }}" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
            </div>
            <div>
                <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-sm">
                    {{ __('Search Audit Logs') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">{{ __('Timestamp') }}</th>
                        <th class="p-4">{{ __('Actor') }}</th>
                        <th class="p-4">{{ __('Action') }}</th>
                        <th class="p-4">{{ __('Entity') }}</th>
                        <th class="p-4">{{ __('Description') }}</th>
                        <th class="p-4">{{ __('IP Address') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-4 text-slate-400 font-mono text-[11px] whitespace-nowrap">{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                        <td class="p-4 font-bold text-slate-900">{{ $log->user->full_name ?? __('System') }}</td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded-md font-mono text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-500 font-mono text-[11px]">{{ $log->entity_type }} #{{ $log->entity_id }}</td>
                        <td class="p-4 text-slate-800 max-w-md font-medium">{{ $log->description }}</td>
                        <td class="p-4 text-slate-400 font-mono text-[11px]">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">{{ __('No audit records found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $logs->links() }}
    </div>

</div>
@endsection