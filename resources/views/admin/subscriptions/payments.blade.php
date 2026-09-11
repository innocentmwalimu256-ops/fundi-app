@extends('layouts.admin')

@section('title', __('Payment Verification Desk') . ' — FUNDI')
@section('page_title', __('Payment Verification & Manual Activation'))

@section('content')
<div class="space-y-6" x-data="{ tab: 'technicians', showTechModal: false, showClientModal: false }">

    <!-- Top Action Banner & Dynamic Buttons -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-black text-slate-900">{{ __('Dawati la Uhakiki & Kuwasha Malipo') }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Washa subscriptions za mafundi au thibitisha ada za wateja moja kwa moja') }}</p>
            </div>

            <div class="flex items-center space-x-3">
                <!-- 1. BUTTON FOR TAB 1: ADD/ACTIVATE TECHNICIAN SUBSCRIPTION -->
                <button type="button" x-show="tab === 'technicians'" @click="showTechModal = !showTechModal" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-bold text-xs rounded-2xl shadow-lg shadow-emerald-700/20 transition flex items-center space-x-2">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>{{ __('+ Washa Subscription ya Fundi Papo Hapo') }}</span>
                </button>

                <!-- 2. BUTTON FOR TAB 2: ADD/APPROVE CLIENT REQUEST FEE -->
                <button type="button" x-show="tab === 'clients'" x-cloak @click="showClientModal = !showClientModal" class="px-4 py-2.5 bg-gradient-to-r from-teal-600 to-indigo-700 hover:from-teal-700 hover:to-indigo-800 text-white font-bold text-xs rounded-2xl shadow-lg shadow-teal-700/20 transition flex items-center space-x-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    <span>{{ __('+ Thibitisha Ada ya Ombi la Mteja (TZS 500)') }}</span>
                </button>
            </div>
        </div>

        <!-- FORM 1: MANUAL TECHNICIAN SUBSCRIPTION ACTIVATION -->
        <div x-show="tab === 'technicians' && showTechModal" x-cloak class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-teal-950 text-white border border-teal-500/30 space-y-4 shadow-xl">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <div class="flex items-center space-x-2">
                    <i data-lucide="zap" class="w-4 h-4 text-teal-400"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-teal-300">{{ __('Washa Subscription ya Fundi Moja kwa Moja') }}</h3>
                </div>
                <button type="button" @click="showTechModal = false" class="text-slate-400 hover:text-white text-xs font-bold"><i data-lucide="x" class="w-3.5 h-3.5 inline text-rose-500"></i> {{ __('Funga') }}</button>
            </div>

            <form method="POST" action="{{ route('admin.subscriptions.manual-activate') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                @csrf
                
                <div>
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">{{ __('Chagua Fundi') }}</label>
                    <select name="user_id" required class="w-full py-2.5 px-3 rounded-xl bg-white/10 border border-white/20 text-white font-medium focus:ring-2 focus:ring-teal-400">
                        <option value="" class="text-slate-900">-- {{ __('Chagua Fundi') }} --</option>
                        @foreach($allTechnicians as $tech)
                            <option value="{{ $tech->id }}" class="text-slate-900">
                                {{ $tech->full_name }} ({{ $tech->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">{{ __('Chagua Kifurushi (Plan)') }}</label>
                    <select name="plan_id" required class="w-full py-2.5 px-3 rounded-xl bg-white/10 border border-white/20 text-white font-medium focus:ring-2 focus:ring-teal-400">
                        @foreach($plans as $p)
                            <option value="{{ $p->id }}" class="text-slate-900" {{ $p->slug === 'professional' ? 'selected' : '' }}>
                                {{ __($p->name) }} (TZS {{ number_format($p->price, 0) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">{{ __('Idadi ya Siku') }}</label>
                    <input type="number" name="days" value="30" min="1" max="365" class="w-full py-2.5 px-3 rounded-xl bg-white/10 border border-white/20 text-white font-medium focus:ring-2 focus:ring-teal-400">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full py-2.5 px-4 bg-teal-400 hover:bg-teal-300 text-slate-950 font-black text-xs uppercase tracking-wider rounded-xl shadow-lg transition flex items-center justify-center space-x-1.5">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>{{ __('Washa Subscription') }}</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- FORM 2: MANUAL CLIENT REQUEST FEE VERIFICATION -->
        <div x-show="tab === 'clients' && showClientModal" x-cloak class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-indigo-950 text-white border border-teal-500/30 space-y-4 shadow-xl">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <div class="flex items-center space-x-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-teal-400"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-teal-300">{{ __('Thibitisha Ada ya Ombi la Mteja (TZS 500)') }}</h3>
                </div>
                <button type="button" @click="showClientModal = false" class="text-slate-400 hover:text-white text-xs font-bold"><i data-lucide="x" class="w-3.5 h-3.5 inline text-rose-500"></i> {{ __('Funga') }}</button>
            </div>

            <div class="space-y-3">
                <p class="text-xs text-slate-300">{{ __('Chagua ombi la mteja hapa chini ili kulithibitisha mara moja na kuliruhusu liende kwa fundi:') }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($pendingRequestsList as $pr)
                        <div class="p-3.5 rounded-xl bg-white/10 border border-white/15 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <div class="font-bold text-white truncate">{{ $pr->client->full_name }} &rarr; {{ $pr->technician->full_name }}</div>
                                <div class="text-[10px] text-slate-300 font-mono">{{ $pr->reference_no }} • {{ __($pr->service->name) }}</div>
                                <div class="text-[10px] text-emerald-400 font-bold">{{ __('Ada') }}: TZS {{ number_format($pr->connection_fee ?? 500, 0) }} ({{ strtoupper($pr->connection_fee_status) }})</div>
                            </div>
                            @if($pr->connection_fee_status !== 'paid')
                                <form method="POST" action="{{ route('admin.subscriptions.client-payments.verify', $pr->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-[11px] rounded-lg shadow whitespace-nowrap flex items-center space-x-1">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        <span>{{ __('Thibitisha') }}</span>
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] text-emerald-300 font-bold bg-emerald-500/20 px-2 py-1 rounded inline-flex items-center space-x-1">
                                    <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400"></i>
                                    <span>{{ __('Tayari') }}</span>
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 col-span-3">{{ __('Hakuna maombi ya wateja yaliyopo kwa sasa.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tabs Navigation Switcher -->
        <div class="flex items-center space-x-2 bg-slate-100 p-1.5 rounded-2xl border border-slate-200">
            <button type="button" @click="tab = 'technicians'; showClientModal = false;" :class="tab === 'technicians' ? 'bg-white text-slate-900 shadow-sm font-black' : 'text-slate-600 font-bold hover:text-slate-900'" class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl text-xs transition flex items-center justify-center space-x-2">
                <i data-lucide="wrench" class="w-4 h-4 text-teal-600"></i>
                <span>{{ __('1. Subscriptions za Mafundi') }}</span>
                @if($pendingCount > 0)
                <span class="w-5 h-5 rounded-full bg-amber-500 text-white text-[10px] flex items-center justify-center font-bold">{{ $pendingCount }}</span>
                @endif
            </button>

            <button type="button" @click="tab = 'clients'; showTechModal = false;" :class="tab === 'clients' ? 'bg-white text-slate-900 shadow-sm font-black' : 'text-slate-600 font-bold hover:text-slate-900'" class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl text-xs transition flex items-center justify-center space-x-2">
                <i data-lucide="user" class="w-4 h-4 text-teal-700"></i>
                <span>{{ __('2. Ada za Wateja (TZS 500)') }}</span>
                @if($pendingClientFeeCount > 0)
                <span class="w-5 h-5 rounded-full bg-emerald-500 text-white text-[10px] flex items-center justify-center font-bold">{{ $pendingClientFeeCount }}</span>
                @endif
            </button>
        </div>

    </div>

    <!-- TAB 1: TECHNICIAN SUBSCRIPTION PAYMENTS -->
    <div x-show="tab === 'technicians'" class="space-y-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Uhakiki wa Malipo ya Subscriptions za Mafundi') }}</h3>
                </div>
                <span class="text-xs font-bold text-slate-500">{{ $payments->total() }} {{ __('Miamala') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 text-[10px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="p-4">{{ __('Reference No') }}</th>
                            <th class="p-4">{{ __('Fundi (Technician)') }}</th>
                            <th class="p-4">{{ __('Kifurushi (Plan)') }}</th>
                            <th class="p-4">{{ __('Kiasi (TZS)') }}</th>
                            <th class="p-4">{{ __('Mtandao') }}</th>
                            <th class="p-4">{{ __('Hali (Status)') }}</th>
                            <th class="p-4">{{ __('Tarehe') }}</th>
                            <th class="p-4 text-right">{{ __('Vitendo vya Admin (Actions)') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($payments as $pay)
                        <tr class="hover:bg-slate-50/60">
                            <td class="p-4 font-mono font-bold text-slate-900">{{ $pay->payment_reference }}</td>
                            <td class="p-4">
                                <span class="font-bold text-slate-900 block">{{ $pay->user->full_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $pay->user->phone }}</span>
                            </td>
                            <td class="p-4 font-semibold text-slate-800">{{ $pay->plan ? __($pay->plan->name) : 'N/A' }}</td>
                            <td class="p-4 font-mono font-bold text-slate-900">{{ $pay->formatted_amount }}</td>
                            <td class="p-4 capitalize text-slate-600">{{ str_replace('_', ' ', $pay->payment_method) }}</td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $pay->status === 'success' ? 'bg-emerald-100 text-emerald-800' : ($pay->status === 'failed' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                    @if($pay->status === 'success')
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        <span>{{ __('ACTIVE') }}</span>
                                    @elseif($pay->status === 'failed')
                                        <i data-lucide="x" class="w-3.5 h-3.5 text-rose-600"></i>
                                        <span>{{ __('INACTIVE') }}</span>
                                    @else
                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-600"></i>
                                        <span>{{ __('PENDING') }}</span>
                                    @endif
                                </span>
                            </td>
                            <td class="p-4 text-slate-500">{{ $pay->created_at->format('d M Y, H:i') }}</td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    @if($pay->status === 'pending')
                                        <form method="POST" action="{{ route('admin.subscriptions.payments.verify', $pay->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-xl shadow-sm flex items-center space-x-1">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                                <span>{{ __('Ruhusu & Washa') }}</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.subscriptions.payments.reject', $pay->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-bold text-[11px] rounded-xl">
                                                {{ __('Kataa') }}
                                            </button>
                                        </form>
                                    @elseif($pay->status === 'success')
                                        <form method="POST" action="{{ route('admin.subscriptions.payments.toggle', $pay->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-[11px] rounded-xl transition">
                                                {{ __('Sitisha / Zima') }}
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.subscriptions.payments.toggle', $pay->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-xl shadow-sm transition flex items-center space-x-1">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                                <span>{{ __('Washa Tena') }}</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">{{ __('Hakuna taarifa za malipo ya mafundi kwa sasa.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: CLIENT REQUEST CONNECTION FEES (TZS 500) -->
    <div x-show="tab === 'clients'" class="space-y-4" style="display: none;">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-teal-600"></span>
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Uhakiki wa Ada za Wateja Kuunganishwa na Mafundi (TZS 500)') }}</h3>
                </div>
                <span class="text-xs font-bold text-slate-500">{{ $clientRequests->total() }} {{ __('Maombi') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 text-[10px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="p-4">{{ __('Request No / Ref') }}</th>
                            <th class="p-4">{{ __('Mteja (Client)') }}</th>
                            <th class="p-4">{{ __('Fundi Aliyeombwa') }}</th>
                            <th class="p-4">{{ __('Huduma') }}</th>
                            <th class="p-4">{{ __('Ada (Fee)') }}</th>
                            <th class="p-4">{{ __('Hali ya Ada') }}</th>
                            <th class="p-4">{{ __('Tarehe') }}</th>
                            <th class="p-4 text-right">{{ __('Uamuzi wa Admin') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($clientRequests as $req)
                        <tr class="hover:bg-slate-50/60">
                            <td class="p-4 font-mono font-bold text-slate-900">
                                {{ $req->reference_no }}
                                <span class="block text-[10px] text-slate-400">{{ $req->connection_fee_reference }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-slate-900 block">{{ $req->client->full_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $req->client->phone }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-teal-800 block">{{ $req->technician->full_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $req->technician->phone }}</span>
                            </td>
                            <td class="p-4 font-semibold text-slate-800">{{ __($req->service->name) }}</td>
                            <td class="p-4 font-mono font-bold text-emerald-700">TZS {{ number_format($req->connection_fee ?? 500, 0) }}</td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $req->connection_fee_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    @if($req->connection_fee_status === 'paid')
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        <span>{{ __('PAID') }}</span>
                                    @else
                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-600"></i>
                                        <span>{{ __('PENDING') }}</span>
                                    @endif
                                </span>
                            </td>
                            <td class="p-4 text-slate-500">{{ $req->created_at->format('d M Y, H:i') }}</td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    @if($req->connection_fee_status === 'paid')
                                        <form method="POST" action="{{ route('admin.subscriptions.client-payments.toggle', $req->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-[11px] rounded-xl transition">
                                                {{ __('Sitisha / Zuia') }}
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.subscriptions.client-payments.verify', $req->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-xl shadow-sm transition flex items-center space-x-1">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                                <span>{{ __('Washa / Ruhusu Ombi') }}</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.subscriptions.client-payments.reject', $req->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-bold text-[11px] rounded-xl">
                                                {{ __('Kataa') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">{{ __('Hakuna maombi ya wateja yenye ada yaliyosajiliwa.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection