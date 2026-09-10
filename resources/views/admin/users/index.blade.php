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
                    <tr class="hover:bg-slate-50/60">
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
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize {{ $u->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ __(ucfirst($u->status)) }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-500">{{ $u->created_at->format('d M Y') }}</td>
                        <td class="p-4 text-right">
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-status', $u->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition {{ $u->status === 'active' ? 'bg-rose-50 text-rose-700 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                    {{ $u->status === 'active' ? __('Suspend') : __('Activate') }}
                                </button>
                            </form>
                            @else
                            <span class="text-slate-400 text-[10px] italic">{{ __('You') }}</span>
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