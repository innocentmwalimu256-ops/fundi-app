@extends('layouts.admin')

@section('title', __('Technicians Directory'))
@section('page_title', __('Verified & Registered Technicians'))

@section('content')
<div class="space-y-6">

    <!-- Filters & Search -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.technicians.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="q" value="{{ $search }}" placeholder="{{ __('Search technician name or phone...') }}" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500 bg-slate-50">
            </div>
            <div>
                <select name="verification_status" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                    <option value="">{{ __('All Verification Statuses') }}</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>{{ __('Approved (Verified)') }}</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>{{ __('Pending Review') }}</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-sm">
                    {{ __('Filter Technicians') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Technicians Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">{{ __('Technician') }}</th>
                        <th class="p-4">{{ __('Specialty & Experience') }}</th>
                        <th class="p-4">{{ __('Location') }}</th>
                        <th class="p-4">{{ __('Rating') }}</th>
                        <th class="p-4">{{ __('Verification') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($technicians as $t)
                    @php $p = $t->technicianProfile; @endphp
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-900 text-teal-300 font-bold text-xs flex items-center justify-center">
                                    {{ $t->initials }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $t->full_name }}</h4>
                                    <span class="text-[10px] text-slate-400">{{ $t->phone }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-slate-700">
                            <p class="font-bold">{{ __($p->professional_title ?? 'Technician') }}</p>
                            <p class="text-[10px] text-slate-400">{{ $p->years_experience ?? 1 }} {{ __('years experience') }}</p>
                        </td>
                        <td class="p-4 text-slate-600">
                            {{ $p->location ?? 'Dar es Salaam' }}
                        </td>
                        <td class="p-4">
                            @if(($p->total_reviews ?? 0) > 0)
                                <span class="font-bold text-amber-600"><i data-lucide="star" class="w-3.5 h-3.5 inline fill-amber-400 text-amber-400"></i> {{ number_format($p->average_rating, 1) }}</span>
                                <span class="text-[10px] text-slate-400 block">({{ $p->total_reviews }} {{ __('reviews') }})</span>
                            @else
                                <span class="text-xs font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-100">{{ __('Mpya (0)') }}</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ ($p->verification_status ?? '') === 'approved' ? 'bg-teal-100 text-teal-800' : (($p->verification_status ?? '') === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ __($p->verification_status ?? 'Unapplied') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400">{{ __('No technicians found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $technicians->links() }}
    </div>

</div>
@endsection