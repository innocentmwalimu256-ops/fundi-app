@extends('layouts.admin')

@section('title', __('User Safety Reports'))
@section('page_title', __('Platform Safety & User Reports'))

@section('content')
<div class="space-y-6">

    <!-- Filter Tabs -->
    <div class="flex border-b border-slate-200 space-x-4 text-xs font-bold">
        <a href="{{ route('admin.user-reports.index') }}" class="py-3 border-b-2 {{ !$status ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('All Reports') }}
        </a>
        <a href="{{ route('admin.user-reports.index', ['status' => 'pending']) }}" class="py-3 border-b-2 {{ $status === 'pending' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Pending Review') }}
        </a>
        <a href="{{ route('admin.user-reports.index', ['status' => 'action_taken']) }}" class="py-3 border-b-2 {{ $status === 'action_taken' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Action Taken') }}
        </a>
        <a href="{{ route('admin.user-reports.index', ['status' => 'dismissed']) }}" class="py-3 border-b-2 {{ $status === 'dismissed' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Dismissed') }}
        </a>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">{{ __('Reported User') }}</th>
                        <th class="p-4">{{ __('Reported By') }}</th>
                        <th class="p-4">{{ __('Reason') }}</th>
                        <th class="p-4">{{ __('Details') }}</th>
                        <th class="p-4">{{ __('Status') }}</th>
                        <th class="p-4 text-right">{{ __('Moderation Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $rep)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-4">
                            <span class="font-bold text-slate-900 block">{{ $rep->reportedUser->full_name }}</span>
                            <span class="text-[10px] text-slate-400 capitalize">{{ __(ucfirst($rep->reportedUser->role)) }} • ID #{{ $rep->reported_user_id }}</span>
                        </td>
                        <td class="p-4">
                            <span class="font-semibold text-slate-800 block">{{ $rep->reporter->full_name }}</span>
                            <span class="text-[10px] text-slate-400">{{ $rep->created_at->format('d M Y, H:i') }}</span>
                        </td>
                        <td class="p-4 font-bold text-rose-700">{{ $rep->reason }}</td>
                        <td class="p-4 text-slate-600 max-w-sm">{{ $rep->details }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $rep->status === 'action_taken' ? 'bg-emerald-100 text-emerald-800' : ($rep->status === 'dismissed' ? 'bg-slate-100 text-slate-600' : 'bg-amber-100 text-amber-800') }}">
                                {{ __(str_replace('_', ' ', $rep->status)) }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <form method="POST" action="{{ route('admin.user-reports.update', $rep->id) }}" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="action_taken">
                                <button type="submit" class="px-3 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-[11px] rounded-lg border border-rose-200">
                                    {{ __('Action Taken') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.user-reports.update', $rep->id) }}" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="dismissed">
                                <button type="submit" class="px-3 py-1 bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-[11px] rounded-lg">
                                    {{ __('Dismiss') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">{{ __('No user safety reports found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $reports->links() }}
    </div>

</div>
@endsection