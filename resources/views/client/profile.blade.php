@extends('layouts.app')

@section('title', __('Account & Profile Settings') . ' — FUNDI')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ __('Account & Profile') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ __('Manage your personal details, contact number, and credentials') }}</p>
        </div>
        <a href="{{ route('client.dashboard') }}" class="btn-tap px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center self-start sm:self-auto space-x-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>{{ __('Back to Dashboard') }}</span>
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        
        <form method="POST" action="{{ route('client.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- User Avatar & Identity Header -->
            <div class="flex items-center space-x-4 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="w-16 h-16 rounded-2xl bg-slate-900 text-teal-300 font-black text-xl flex items-center justify-center shadow-xs flex-shrink-0">
                    {{ $user->initials }}
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-bold text-slate-900 truncate">{{ $user->full_name }}</h3>
                    <p class="text-xs text-teal-700 font-semibold uppercase tracking-wider mt-0.5">{{ $user->role }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ __('Member since') }} {{ $user->created_at->format('M Y') }}</p>
                </div>
            </div>

            <!-- Form Inputs -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Full Name') }}</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-slate-50/50">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Phone Number') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none bg-slate-50/50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Email Address') }}</label>
                <input type="email" value="{{ $user->email }}" disabled class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm bg-slate-100 text-slate-500 cursor-not-allowed">
                <p class="text-[11px] text-slate-400 mt-1">{{ __('Email is used for account identification and notifications.') }}</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="btn-tap px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition flex items-center space-x-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>{{ __('Save Profile Changes') }}</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
