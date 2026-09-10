@extends('layouts.admin')

@section('title', __('Admin Profile') . ' — FUNDI')
@section('page_title', __('Admin Profile & Account Settings'))

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    <!-- Top Identity Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 sm:p-8 text-white border border-teal-500/20 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center space-x-5">
            <div class="relative">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-teal-500 to-emerald-500 flex items-center justify-center text-white text-2xl font-black shadow-lg border-2 border-white/20 overflow-hidden">
                    @if($admin->avatar)
                        <img src="{{ asset('storage/' . $admin->avatar) }}" alt="{{ $admin->full_name }}" class="w-full h-full object-cover">
                    @else
                        {{ $admin->initials }}
                    @endif
                </div>
                <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-2 border-slate-900 rounded-full"></span>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-xl sm:text-2xl font-black text-white">{{ $admin->full_name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-400/20 text-teal-300 border border-teal-400/30">{{ __('Super Admin') }}</span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5 font-mono">{{ $admin->email }} • {{ $admin->phone }}</p>
                <p class="text-[11px] text-teal-400/80 mt-1 font-medium">{{ __('Full Marketplace Control & System Override Permissions') }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-4 text-center sm:text-right border-t sm:border-t-0 sm:border-l border-white/10 pt-4 sm:pt-0 sm:pl-6">
            <div>
                <span class="text-2xl font-black text-teal-300 font-mono">{{ $auditCount }}</span>
                <span class="text-[10px] text-slate-400 block uppercase font-bold">{{ __('Audit Actions') }}</span>
            </div>
            <div>
                <span class="text-2xl font-black text-emerald-400 font-mono">100%</span>
                <span class="text-[10px] text-slate-400 block uppercase font-bold">{{ __('System Access') }}</span>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
        <p class="font-bold flex items-center"><i data-lucide="alert-circle" class="w-4 h-4 mr-1.5 text-rose-600"></i> Kuna hitilafu zifuatazo:</p>
        <ul class="list-disc list-inside space-y-0.5 pl-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Form 1: Edit Personal Details -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center">
                    <i data-lucide="user" class="w-4 h-4 mr-2 text-teal-600"></i>
                    {{ __('Badilisha Taarifa Zako (Personal Details)') }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Sasisha jina lako, barua pepe na namba ya simu ya Admin') }}</p>
            </div>

            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Jina la Kwanza (First Name)') }}</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $admin->first_name) }}" required class="w-full p-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Jina la Mwisho (Last Name)') }}</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $admin->last_name) }}" required class="w-full p-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Barua Pepe (Email Address)') }}</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" required class="w-full p-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Namba ya Simu (Phone Number)') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $admin->phone) }}" required class="w-full p-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-mono">
                    <span class="text-[11px] text-slate-400 mt-1 block">{{ __('Used for critical alerts and admin communications.') }}</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Picha ya Wasifu (Avatar Image)') }}</label>
                    <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition flex items-center justify-center space-x-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>{{ __('Save Profile Changes') }}</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Form 2: Change Password -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center">
                    <i data-lucide="lock" class="w-4 h-4 mr-2 text-rose-600"></i>
                    {{ __('Badilisha Nenosiri (Security Password)') }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Ensure your administrative password is long and secure') }}</p>
            </div>

            <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Nenosiri la Sasa (Current Password)') }}</label>
                    <input type="password" name="current_password" required class="w-full p-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Nenosiri Jipya (New Password)') }}</label>
                    <input type="password" name="password" required class="w-full p-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Rudia Nenosiri Jipya (Confirm Password)') }}</label>
                    <input type="password" name="password_confirmation" required class="w-full p-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition flex items-center justify-center space-x-2">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>{{ __('Update Password') }}</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection