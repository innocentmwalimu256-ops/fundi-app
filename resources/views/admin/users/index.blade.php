@extends('layouts.admin')

@section('title', __('User Management'))
@section('page_title', __('Users Directory & Access Control'))

@section('content')
<div class="space-y-6">

    <!-- Filters & Search -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="q" value="{{ $search }}" placeholder="{{ __('Search by name, email, phone...') }}" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500 bg-slate-50">
            </div>
            <div>
                <select name="role" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                    <option value="">{{ __('All Roles') }}</option>
                    <option value="client" {{ $role === 'client' ? 'selected' : '' }}>{{ __('Clients') }}</option>
                    <option value="technician" {{ $role === 'technician' ? 'selected' : '' }}>{{ __('Technicians') }}</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>{{ __('Administrators') }}</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <select name="status" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                </select>
                <button type="submit" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-sm">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">{{ __('User') }}</th>
                        <th class="p-4">{{ __('Contact') }}</th>
                        <th class="p-4">{{ __('Role') }}</th>
                        <th class="p-4">{{ __('Status') }}</th>
                        <th class="p-4">{{ __('Joined') }}</th>
                        <th class="p-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                    <tr x-data="{
                            userStatus: '{{ $u->status }}',
                            loading: false,
                            deleted: false,
                            async toggleStatus() {
                                this.loading = true;
                                try {
                                    let res = await fetch('{{ route('admin.users.toggle-status', $u->id) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        }
                                    });
                                    let data = await res.json();
                                    if (data.success) {
                                        this.userStatus = data.status;
                                        this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                                    }
                                } catch (e) {
                                    window.location.reload();
                                } finally {
                                    this.loading = false;
                                }
                            },
                            async deleteUser() {
                                if (!confirm('{{ __('Je, una uhakika unataka kumfuta kabisa mtumiaji huyu (:name)? Taarifa zake zitaondolewa na email yake (:email) itakuwa huru kusajiliwa upya.', ['name' => $u->full_name, 'email' => $u->email]) }}')) {
                                    return;
                                }
                                this.loading = true;
                                try {
                                    let res = await fetch('{{ route('admin.users.delete', $u->id) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        }
                                    });
                                    let data = await res.json();
                                    if (data.success) {
                                        this.deleted = true;
                                    }
                                } catch (e) {
                                    window.location.reload();
                                } finally {
                                    this.loading = false;
                                }
                            }
                        }" 
                        x-show="!deleted"
                        x-transition.duration.300ms
                        class="hover:bg-slate-50/60 transition">
                        <td class="p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center">
                                    {{ $u->initials }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $u->full_name }}</h4>
                                    <span class="text-[10px] text-slate-400">ID #{{ $u->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-slate-600">
                            <p>{{ $u->email }}</p>
                            <p class="text-[10px] text-slate-400">{{ $u->phone }}</p>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize {{ $u->role === 'admin' ? 'bg-purple-100 text-purple-800' : ($u->role === 'technician' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800') }}">
                                {{ __(ucfirst($u->role)) }}
                            </span>
                        </td>
                        <td class="p-4">
                            <template x-if="userStatus === 'active'">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize bg-emerald-100 text-emerald-800">
                                    {{ __('Active') }}
                                </span>
                            </template>
                            <template x-if="userStatus !== 'active'">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize bg-rose-100 text-rose-800">
                                    {{ __('Suspended') }}
                                </span>
                            </template>
                        </td>
                        <td class="p-4 text-slate-500">{{ $u->created_at->format('d M Y') }}</td>
                        <td class="p-4 text-right">
                            @if($u->id !== auth()->id())
                            <div class="flex items-center justify-end space-x-2">
                                <button type="button" 
                                        @click="toggleStatus()" 
                                        :disabled="loading"
                                        :class="userStatus === 'active' ? 'bg-amber-50 hover:bg-amber-100 text-amber-800 border-amber-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border-emerald-300 shadow-xs'"
                                        class="px-3 py-1.5 rounded-xl text-[11px] font-bold border transition flex items-center space-x-1 cursor-pointer disabled:opacity-50">
                                    <template x-if="!loading && userStatus === 'active'">
                                        <span class="flex items-center space-x-1">
                                            <i data-lucide="pause-circle" class="w-3.5 h-3.5 text-amber-600"></i>
                                            <span>{{ __('Suspend') }}</span>
                                        </span>
                                    </template>
                                    <template x-if="!loading && userStatus !== 'active'">
                                        <span class="flex items-center space-x-1">
                                            <i data-lucide="play-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
                                            <span>{{ __('Ruhusu / Unsuspend') }}</span>
                                        </span>
                                    </template>
                                    <template x-if="loading">
                                        <span class="flex items-center space-x-1">
                                            <svg class="animate-spin h-3.5 w-3.5 text-slate-700" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>{{ __('Inabadilisha...') }}</span>
                                        </span>
                                    </template>
                                </button>

                                <button type="button" 
                                        @click="deleteUser()" 
                                        :disabled="loading"
                                        class="px-2.5 py-1.5 rounded-xl text-[11px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition flex items-center space-x-1 cursor-pointer disabled:opacity-50"
                                        title="{{ __('Futa kabisa mtumiaji na weka email yake huru') }}">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                    <span>{{ __('Delete') }}</span>
                                </button>
                            </div>
                            @else
                            <span class="text-slate-400 text-[10px] italic">{{ __('Akaunti Yako (You)') }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">{{ __('No users found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $users->links() }}
    </div>

</div>
@endsection