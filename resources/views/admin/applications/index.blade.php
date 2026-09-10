@extends('layouts.admin')

@section('title', __('Technician Applications'))
@section('page_title', __('Technician Verification Queue'))

@section('content')
<div class="space-y-6">

    <!-- Filter Tabs -->
    <div class="flex border-b border-slate-200 space-x-4 text-xs font-bold">
        <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}" class="py-3 border-b-2 flex items-center space-x-1.5 {{ $status === 'pending' ? 'border-brand-600 text-brand-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <span>{{ __('Pending Review') }}</span>
            @if($pendingCount > 0)
                <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 text-[10px] font-black">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'approved']) }}" class="py-3 border-b-2 {{ $status === 'approved' ? 'border-brand-600 text-brand-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Approved (Verified)') }}
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'rejected']) }}" class="py-3 border-b-2 {{ $status === 'rejected' ? 'border-brand-600 text-brand-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('Rejected') }}
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'all']) }}" class="py-3 border-b-2 {{ $status === 'all' ? 'border-brand-600 text-brand-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ __('All Applications') }}
        </a>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">{{ __('App ID') }}</th>
                        <th class="p-4">{{ __('Applicant') }}</th>
                        <th class="p-4">{{ __('Trade & Title') }}</th>
                        <th class="p-4">{{ __('Experience & Location') }}</th>
                        <th class="p-4">{{ __('Submitted') }}</th>
                        <th class="p-4">{{ __('Status') }}</th>
                        <th class="p-4 text-right">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-4 font-mono font-bold text-slate-900">#{{ sprintf('%03d', $app->id) }}</td>
                        <td class="p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center">
                                    {{ $app->user->initials }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $app->user->full_name }}</h4>
                                    <span class="text-[10px] text-slate-400">{{ $app->user->phone }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-slate-700">
                            <p class="font-bold">{{ __($app->professional_title) }}</p>
                            <span class="text-[10px] text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">{{ __($app->service->name ?? 'General') }}</span>
                        </td>
                        <td class="p-4 text-slate-600">
                            <p>{{ $app->years_experience }} {{ __('yrs exp') }}</p>
                            <span class="text-[10px] text-slate-400">{{ $app->location }}</span>
                        </td>
                        <td class="p-4 text-slate-400">{{ $app->created_at->format('d M Y') }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $app->status === 'approved' ? 'bg-teal-100 text-teal-800' : ($app->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ __(ucfirst($app->status)) }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.applications.show', $app->id) }}" class="px-3.5 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                                {{ __('Inspect & Verify') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">{{ __('No applications found in this status.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $applications->links() }}
    </div>

</div>
@endsection