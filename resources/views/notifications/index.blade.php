@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Notifications</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Stay updated on your request statuses, quotations, messages, and jobs</p>
        </div>

        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                Mark All Read
            </button>
        </form>
    </div>

    <!-- Notifications Feed -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm divide-y divide-slate-100 overflow-hidden">
        @forelse($notifications as $notif)
        <div class="p-5 flex items-start justify-between gap-4 {{ $notif->is_read ? 'bg-white opacity-80' : 'bg-brand-50/40 border-l-4 border-brand-600' }} transition">
            <div class="flex items-start space-x-3.5">
                <div class="w-10 h-10 rounded-2xl bg-brand-100 text-brand-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i data-lucide="{{ str_contains($notif->type, 'message') ? 'message-square' : (str_contains($notif->type, 'quote') ? 'calculator' : (str_contains($notif->type, 'review') ? 'star' : 'bell')) }}" class="w-5 h-5"></i>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <h4 class="text-xs font-bold text-slate-900">{{ $notif->title }}</h4>
                        @if(!$notif->is_read)
                            <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $notif->message }}</p>
                    <span class="text-[10px] text-slate-400 block pt-0.5">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
            </div>

            <div class="flex items-center space-x-2 flex-shrink-0">
                @if($notif->action_url)
                <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold rounded-xl shadow-sm transition">
                        View &rarr;
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="p-12 text-center text-slate-400 text-xs">
            <i data-lucide="bell-off" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
            <p class="font-bold text-slate-700">No Notifications</p>
            <p class="text-slate-400 mt-0.5">You're all caught up!</p>
        </div>
        @endforelse
    </div>

    <div>
        {{ $notifications->links() }}
    </div>

</div>
@endsection
