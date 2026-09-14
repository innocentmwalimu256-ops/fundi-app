@extends('layouts.app')

@section('title', __('Thibitisha Barua Pepe') . ' - FUNDI')

@section('content')
<div class="max-w-md mx-auto px-4 py-12" x-data="{
    digits: ['', '', '', '', '', ''],
    cooldown: {{ session('resend_cooldown', 0) ? (session('resend_cooldown') - time() > 0 ? session('resend_cooldown') - time() : 0) : 0 }},
    timer: null,
    changeEmailModal: false,
    newEmail: '{{ auth()->user()->email }}',

    init() {
        if (this.cooldown > 0) {
            this.startTimer();
        }
        this.$nextTick(() => {
            const firstInput = document.getElementById('digit-0');
            if (firstInput) firstInput.focus();
        });
    },

    startTimer() {
        if (this.timer) clearInterval(this.timer);
        this.timer = setInterval(() => {
            if (this.cooldown > 0) {
                this.cooldown--;
            } else {
                clearInterval(this.timer);
            }
        }, 1000);
    },

    handleInput(index, event) {
        const val = event.target.value;
        if (val.length > 1) {
            this.digits[index] = val.slice(-1);
        }
        if (val && index < 5) {
            const next = document.getElementById('digit-' + (index + 1));
            if (next) next.focus();
        }
    },

    handleKeyDown(index, event) {
        if (event.key === 'Backspace' && !this.digits[index] && index > 0) {
            const prev = document.getElementById('digit-' + (index - 1));
            if (prev) {
                prev.focus();
                this.digits[index - 1] = '';
            }
        }
    },

    handlePaste(event) {
        event.preventDefault();
        const text = (event.clipboardData || window.clipboardData).getData('text').trim();
        const cleanDigits = text.replace(/[^0-9]/g, '').slice(0, 6).split('');
        cleanDigits.forEach((d, i) => {
            if (i < 6) this.digits[i] = d;
        });
        const targetIndex = Math.min(cleanDigits.length, 5);
        const el = document.getElementById('digit-' + targetIndex);
        if (el) el.focus();
    },

    get fullOtp() {
        return this.digits.join('');
    }
}">

    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-xl space-y-6">

        <!-- Top Icon and Header -->
        <div class="text-center space-y-2">
            <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-200 text-teal-700 flex items-center justify-center mx-auto shadow-sm">
                <i data-lucide="mail-check" class="w-8 h-8"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900">{{ __('Uthibitisho wa Barua Pepe') }}</h1>
            <p class="text-xs text-slate-500 leading-relaxed">
                {{ __('Tumetuma nambari ya siri ya tarakimu 6 (OTP) kwenye anwani yako ya barua pepe:') }}
            </p>
            <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-slate-100 text-xs font-mono font-bold text-slate-800">
                <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>{{ auth()->user()->email }}</span>
            </div>
        </div>

        @if(session('info'))
            <div class="p-3.5 rounded-2xl bg-teal-50 border border-teal-200 text-teal-800 text-xs text-center font-medium">
                {{ session('info') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs text-center font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                @foreach($errors->all() as $err)
                    <p class="text-center font-medium">{{ $err }}</p>
                @endforeach
            </div>
        @endif

        @if(session('last_verification_otp') && (app()->environment('local') || config('app.debug')))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs text-center">
                <span class="font-bold">{{ __('Mazingira ya Majaribio (Local OTP):') }}</span>
                <span class="font-mono font-black text-sm ml-1 text-slate-900">{{ session('last_verification_otp') }}</span>
            </div>
        @endif

        <!-- OTP Submission Form -->
        <form method="POST" action="{{ route('verification.verify') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="otp_code" :value="fullOtp">

            <div>
                <label class="block text-center text-xs font-bold uppercase tracking-wider text-slate-600 mb-3">
                    {{ __('Ingiza Nambari ya Siri (OTP)') }}
                </label>
                
                <div class="flex justify-center items-center gap-2 sm:gap-3" @paste="handlePaste($event)">
                    <template x-for="(digit, idx) in digits" :key="idx">
                        <input type="text"
                               :id="'digit-' + idx"
                               x-model="digits[idx]"
                               maxlength="1"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               autocomplete="off"
                               @input="handleInput(idx, $event)"
                               @keydown="handleKeyDown(idx, $event)"
                               class="w-11 h-14 sm:w-12 sm:h-14 text-center text-xl sm:text-2xl font-black font-mono rounded-2xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-slate-900 shadow-sm transition">
                    </template>
                </div>
                <p class="text-[11px] text-center text-slate-400 mt-2">
                    {{ __('Msimbo unaisha muda wake baada ya dakika 15.') }}
                </p>
            </div>

            <button type="submit" 
                    :disabled="fullOtp.length !== 6" 
                    class="w-full py-4 rounded-2xl bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 disabled:opacity-50 disabled:cursor-not-allowed text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-teal-700/25 transition flex items-center justify-center space-x-2 cursor-pointer">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>{{ __('Thibitisha na Uwashe Akaunti') }}</span>
            </button>
        </form>

        <!-- Resend & Change Email Actions -->
        <div class="pt-4 border-t border-slate-100 flex flex-col items-center space-y-3">
            
            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" 
                        :disabled="cooldown > 0"
                        class="text-xs font-bold text-teal-700 hover:text-teal-900 disabled:text-slate-400 transition cursor-pointer flex items-center space-x-1.5">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5" :class="cooldown > 0 ? '' : 'text-teal-600'"></i>
                    <template x-if="cooldown === 0">
                        <span>{{ __('Hujapokea nambari? Tuma Upya OTP') }}</span>
                    </template>
                    <template x-if="cooldown > 0">
                        <span>{{ __('Unaweza kutuma tena baada ya') }} <strong x-text="cooldown + 's'"></strong></span>
                    </template>
                </button>
            </form>

            <button type="button" 
                    @click="changeEmailModal = true" 
                    class="text-xs font-medium text-slate-500 hover:text-slate-800 underline transition cursor-pointer">
                {{ __('Ulikosea barua pepe? Badilisha anwani hapa') }}
            </button>

            <div class="pt-2">
                <a href="{{ route('logout') }}" class="text-[11px] font-bold text-rose-600 hover:text-rose-800 transition">
                    {{ __('Ondoka kwenye akaunti (Logout)') }}
                </a>
            </div>

        </div>

    </div>

    <!-- Change Email Modal -->
    <div x-show="changeEmailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-4" @click.outside="changeEmailModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-black text-slate-900">{{ __('Badilisha Barua Pepe') }}</h3>
                <button type="button" @click="changeEmailModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed">
                {{ __('Weka barua pepe yako halisi na inayofanya kazi. Mfumo utatuma nambari mpya ya OTP kwenye anwani hii.') }}
            </p>

            <form method="POST" action="{{ route('verification.change-email') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        {{ __('Anwani Mpya ya Barua Pepe') }}
                    </label>
                    <input type="email" 
                           name="email" 
                           x-model="newEmail" 
                           required 
                           placeholder="mfano: jina@gmail.com" 
                           class="w-full py-3 px-4 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="changeEmailModal = false" class="px-4 py-2.5 text-xs font-bold text-slate-600 rounded-xl hover:bg-slate-100 transition">
                        {{ __('Ghairi') }}
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold bg-teal-600 text-white rounded-xl shadow hover:bg-teal-700 transition">
                        {{ __('Hifadhi na Tuma OTP') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
