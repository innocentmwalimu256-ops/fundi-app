@extends('layouts.admin')

@section('title', __('Reviews Moderation'))
@section('page_title', __('Reviews & Ratings Moderation'))

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">{{ __('Client') }}</th>
                        <th class="p-4">{{ __('Technician') }}</th>
                        <th class="p-4">{{ __('Rating') }}</th>
                        <th class="p-4">{{ __('Comment') }}</th>
                        <th class="p-4">{{ __('Status') }}</th>
                        <th class="p-4 text-right">{{ __('Moderation Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reviews as $rev)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-4 font-semibold text-slate-900">{{ $rev->client->full_name }}</td>
                        <td class="p-4 font-semibold text-slate-900">{{ $rev->technician->full_name }}</td>
                        <td class="p-4 font-bold text-amber-500">★ {{ $rev->rating }}.0</td>
                        <td class="p-4 text-slate-600 max-w-sm">"{{ $rev->comment }}"</td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $rev->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ __(ucfirst($rev->status)) }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            @if($rev->status === 'published')
                            <form method="POST" action="{{ route('admin.reviews.status', $rev->id) }}" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="hidden">
                                <button type="submit" class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] rounded-lg border border-rose-200 transition">
                                    {{ __('Hide Review') }}
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.reviews.status', $rev->id) }}" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="published">
                                <button type="submit" class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[11px] rounded-lg border border-emerald-200 transition">
                                    {{ __('Restore') }}
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">{{ __('No reviews to moderate.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $reviews->links() }}
    </div>

</div>
@endsection