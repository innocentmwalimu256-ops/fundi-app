@extends('layouts.admin')

@section('title', __('Platform & API Settings'))
@section('page_title', __('System Configuration & API Credentials'))

@section('content')
<div class="max-w-5xl mx-auto space-y-8" x-data="{ showApiKey: false, showWebhookSecret: false, copiedWebhook: false }">

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl flex items-center space-x-3 text-xs font-bold shadow-xs">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl text-xs font-medium space-y-1 shadow-xs">
            @foreach($errors->all() as $err)
                <div class="flex items-center space-x-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
                    <span>{{ $err }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Payment Gateway API Management Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
            <div class="space-y-1">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-extrabold bg-teal-50 text-teal-800 border border-teal-200">
                    <i data-lucide="key" class="w-3.5 h-3.5 text-teal-600"></i>
                    <span>{{ __('Mobile Money Payment Gateway (Snippe API)') }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ __('Payment Gateway API Credentials') }}</h1>
                <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
                    {{ __('Configure your live payment gateway keys directly from this control panel. If your API keys leak or change, you can update them here immediately without altering application source code.') }}
                </p>
            </div>
            <div class="flex-shrink-0">
                <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>{{ __('Gateway Engine Active') }}</span>
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.payment.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Profile ID / Merchant ID -->
                <div class="space-y-2">
                    <label for="profile_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        {{ __('Merchant Profile ID') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                        </div>
                        <input type="text" id="profile_id" name="profile_id" value="{{ old('profile_id', $profileId) }}" required
                            placeholder="prof_4a8df29e81b67c94"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/60 transition">
                    </div>
                    <p class="text-[11px] text-slate-400">{{ __('Your unique merchant profile reference issued by the payment gateway.') }}</p>
                </div>

                <!-- API Base URL -->
                <div class="space-y-2">
                    <label for="base_url" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        {{ __('Gateway API Base URL') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="globe" class="w-4 h-4"></i>
                        </div>
                        <input type="url" id="base_url" name="base_url" value="{{ old('base_url', $baseUrl) }}" required
                            placeholder="https://api.snippe.sh/api/v1"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-xs font-mono font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/60 transition">
                    </div>
                    <p class="text-[11px] text-slate-400">{{ __('Live endpoint for USSD push and mobile money transaction requests.') }}</p>
                </div>

            </div>

            <!-- API Key -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label for="api_key" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        {{ __('API Key (Bearer Token)') }} <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" @click="showApiKey = !showApiKey" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                        <i data-lucide="eye" x-show="!showApiKey" class="w-3.5 h-3.5"></i>
                        <i data-lucide="eye-off" x-show="showApiKey" class="w-3.5 h-3.5" x-cloak></i>
                        <span x-text="showApiKey ? '{{ __('Hide Key') }}' : '{{ __('Show Key') }}'"></span>
                    </button>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    <input :type="showApiKey ? 'text' : 'password'" id="api_key" name="api_key" value="{{ old('api_key', $apiKey) }}" required
                        placeholder="snp_d72351fa5858490448258a2d515e5a8ad2439fca3ddf64a2123737fbc1c28ce8"
                        class="w-full pl-10 pr-12 py-3.5 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/60 transition">
                </div>
                <p class="text-[11px] text-slate-400">{{ __('Encrypted private key used to authenticate all outbound payment initialization calls.') }}</p>
            </div>

            <!-- Webhook Secret -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label for="webhook_secret" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        {{ __('Webhook Signing Secret (HMAC-SHA256)') }} <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" @click="showWebhookSecret = !showWebhookSecret" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                        <i data-lucide="eye" x-show="!showWebhookSecret" class="w-3.5 h-3.5"></i>
                        <i data-lucide="eye-off" x-show="showWebhookSecret" class="w-3.5 h-3.5" x-cloak></i>
                        <span x-text="showWebhookSecret ? '{{ __('Hide Secret') }}' : '{{ __('Show Secret') }}'"></span>
                    </button>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="fingerprint" class="w-4 h-4"></i>
                    </div>
                    <input :type="showWebhookSecret ? 'text' : 'password'" id="webhook_secret" name="webhook_secret" value="{{ old('webhook_secret', $webhookSecret) }}" required
                        placeholder="whsec_0836d02c3d08337fe6597f9c199e2d82c98fea6c718cf3669bc08dc9b6485cf6"
                        class="w-full pl-10 pr-12 py-3.5 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/60 transition">
                </div>
                <p class="text-[11px] text-slate-400">{{ __('Secret key used by the application to cryptographically verify incoming payment webhook signatures.') }}</p>
            </div>

            <!-- Webhook Callback URL Notice Box -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-900 text-white space-y-3 shadow-md border border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-xs font-bold text-teal-400">
                        <i data-lucide="radio" class="w-4 h-4"></i>
                        <span>{{ __('Live Webhook Callback Endpoint (Copy to Gateway Dashboard)') }}</span>
                    </div>
                    <button type="button" @click="navigator.clipboard.writeText('{{ $webhookUrl }}'); copiedWebhook = true; setTimeout(() => copiedWebhook = false, 2500)"
                        class="px-3 py-1 bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 text-xs font-bold rounded-lg border border-teal-400/30 transition flex items-center space-x-1">
                        <i data-lucide="copy" class="w-3.5 h-3.5" x-show="!copiedWebhook"></i>
                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400" x-show="copiedWebhook" x-cloak></i>
                        <span x-text="copiedWebhook ? '{{ __('Copied!') }}' : '{{ __('Copy URL') }}'"></span>
                    </button>
                </div>
                <div class="font-mono text-xs text-slate-200 bg-black/40 p-3 rounded-xl border border-white/10 break-all select-all">
                    {{ $webhookUrl }}
                </div>
                <input type="hidden" name="webhook_url" value="{{ $webhookUrl }}">
                <p class="text-[11px] text-slate-400">
                    {{ __('Paste this URL into your payment gateway webhook settings to receive instant payment confirmations for M-Pesa, Tigo Pesa, and Airtel Money.') }}
                </p>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md active:scale-[0.98] transition flex items-center justify-center space-x-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>{{ __('Save & Apply API Credentials') }}</span>
                </button>
            </div>

        </form>

    </div>

    <!-- SMTP Email Gateway Configuration Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-8" x-data="{ showMailPassword: false }">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
            <div class="space-y-1">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-800 border border-blue-200">
                    <i data-lucide="mail" class="w-3.5 h-3.5 text-blue-600"></i>
                    <span>{{ __('Barua Pepe & Uthibitisho wa OTP (SMTP Mail Server)') }}</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ __('Mipangilio ya Seva ya Barua Pepe (SMTP)') }}</h2>
                <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
                    {{ __('Sanidi seva ya barua pepe inayotumika kutuma nambari za OTP za uthibitisho kwa watumiaji wapya, arifa za mfumo, na risiti za kazi (inasaidia Gmail SMTP, Brevo, Resend, Mailgun, n.k.).') }}
                </p>
            </div>
            <div class="flex-shrink-0">
                <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200 shadow-xs">
                    <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i>
                    <span>{{ __('Active Email Engine') }}</span>
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.mail.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- SMTP Host -->
                <div class="space-y-2 md:col-span-2">
                    <label for="mail_host" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        {{ __('SMTP Server Host') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="server" class="w-4 h-4"></i>
                        </div>
                        <input type="text" id="mail_host" name="mail_host" value="{{ old('mail_host', $mailHost) }}" required
                            placeholder="smtp.gmail.com au smtp-relay.brevo.com"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/60 transition">
                    </div>
                    <p class="text-[11px] text-slate-400">{{ __('Anwani ya seva ya SMTP (mfano: smtp.gmail.com).') }}</p>
                </div>

                <!-- SMTP Port -->
                <div class="space-y-2">
                    <label for="mail_port" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        {{ __('Port') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="hash" class="w-4 h-4"></i>
                        </div>
                        <input type="number" id="mail_port" name="mail_port" value="{{ old('mail_port', $mailPort) }}" required
                            placeholder="587"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/60 transition">
                    </div>
                    <p class="text-[11px] text-slate-400">{{ __('Kawaida ni 587 (TLS) au 465 (SSL).') }}</p>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- SMTP Username -->
                <div class="space-y-2">
                    <label for="mail_username" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        {{ __('SMTP Username / Email') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input type="text" id="mail_username" name="mail_username" value="{{ old('mail_username', $mailUsername) }}" required
                            placeholder="anwani@gmail.com"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/60 transition">
                    </div>
                    <p class="text-[11px] text-slate-400">{{ __('Jina la mtumiaji au barua pepe inayotuma.') }}</p>
                </div>

                <!-- SMTP Password -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="mail_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            {{ __('SMTP Password / App Password') }}
                        </label>
                        <button type="button" @click="showMailPassword = !showMailPassword" class="text-xs font-bold text-teal-700 hover:text-teal-800 transition flex items-center space-x-1">
                            <i data-lucide="eye" x-show="!showMailPassword" class="w-3.5 h-3.5"></i>
                            <i data-lucide="eye-off" x-show="showMailPassword" class="w-3.5 h-3.5" x-cloak></i>
                            <span x-text="showMailPassword ? '{{ __('Ficha') }}' : '{{ __('Onyesha') }}'"></span>
                        </button>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="key-round" class="w-4 h-4"></i>
                        </div>
                        <input :type="showMailPassword ? 'text' : 'password'" id="mail_password" name="mail_password" value="{{ old('mail_password', $mailPassword) }}"
                            placeholder="Nenosiri la SMTP au App Password"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/60 transition">
                    </div>
                    <p class="text-[11px] text-slate-400">{{ __('Kwa Gmail, tumia 16-character Google App Password.') }}</p>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Encryption -->
                <div class="space-y-2">
                    <label for="mail_encryption" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        {{ __('Encryption') }}
                    </label>
                    <select id="mail_encryption" name="mail_encryption" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/60 transition">
                        <option value="tls" {{ old('mail_encryption', $mailEncryption) === 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                        <option value="ssl" {{ old('mail_encryption', $mailEncryption) === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                    </select>
                </div>

                <!-- From Address -->
                <div class="space-y-2">
                    <label for="mail_from_address" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        {{ __('From Address (Mtumaji)') }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $mailFromAddress) }}" required
                        placeholder="no-reply@fundiapp.co.tz"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/60 transition">
                </div>

                <!-- From Name -->
                <div class="space-y-2">
                    <label for="mail_from_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        {{ __('From Name (Jina la Mtumaji)') }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $mailFromName) }}" required
                        placeholder="FUNDI App"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50/60 transition">
                </div>

            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md active:scale-[0.98] transition flex items-center justify-center space-x-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>{{ __('Hifadhi Mipangilio ya Seva ya Barua Pepe') }}</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection