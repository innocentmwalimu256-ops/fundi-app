@extends('layouts.app')

@section('title', __('Account & Profile Settings'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-6">
        
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ __('Account & Profile Settings') }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ __('Manage your personal information and contact details') }}</p>
        </div>

        <form method="POST" action="{{ route('client.profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Avatar -->
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-brand-900 text-white font-bold text-xl flex items-center justify-center shadow-md">
                    {{ $user->initials }}
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Profile Photo') }}</label>
                    <input type="file" name="avatar" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('Full Name') }}</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('Phone Number') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 bg-slate-50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">{{ __('Email Address') }}</label>
                <input type="email" value="{{ $user->email }}" disabled class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-sm bg-slate-100 text-slate-500 cursor-not-allowed">
                <p class="text-[11px] text-slate-400 mt-1">{{ __('Email cannot be changed directly for security reasons.') }}</p>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-3 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs shadow transition">
                    {{ __('Save Profile Changes') }}
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
