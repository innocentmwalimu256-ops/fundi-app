@extends('layouts.admin')

@section('title', __('Services Management'))
@section('page_title', __('Service Categories Catalog'))

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs text-slate-500">{{ __('Manage all trade categories available on FUNDI') }}</p>
        </div>
        <button type="button" @click="createModal = true" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center space-x-1 self-start sm:self-auto">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>{{ __('Add Service Category') }}</span>
        </button>
    </div>

    <!-- Services Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-4">{{ __('Category Name') }}</th>
                        <th class="p-4">{{ __('Description') }}</th>
                        <th class="p-4">{{ __('Icon Identifier') }}</th>
                        <th class="p-4">{{ __('Technicians') }}</th>
                        <th class="p-4">{{ __('Requests') }}</th>
                        <th class="p-4">{{ __('Status') }}</th>
                        <th class="p-4 text-right">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($services as $svc)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-4">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                                    <i data-lucide="{{ $svc->icon }}" class="w-4 h-4"></i>
                                </div>
                                <span class="font-bold text-slate-900">{{ __($svc->name) }}</span>
                            </div>
                        </td>
                        <td class="p-4 text-slate-600 max-w-xs truncate">{{ $svc->description }}</td>
                        <td class="p-4 font-mono text-slate-500 text-[11px]">{{ $svc->icon }}</td>
                        <td class="p-4 font-bold text-slate-900">{{ $svc->technicians_count }}</td>
                        <td class="p-4 font-bold text-slate-900">{{ $svc->requests_count }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $svc->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ __(ucfirst($svc->status)) }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <form method="POST" action="{{ route('admin.services.toggle-status', $svc->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition {{ $svc->status === 'active' ? 'bg-rose-50 text-rose-700 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                    {{ $svc->status === 'active' ? __('Disable') : __('Enable') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Service Category Modal -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-4" @click.outside="createModal = false">
            <h3 class="text-base font-bold text-slate-900">{{ __('Add New Service Category') }}</h3>
            
            <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Service Name') }}</label>
                    <input type="text" name="name" required placeholder="e.g. Solar Power Installation" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Description') }}</label>
                    <textarea name="description" rows="2" placeholder="Brief description of skills involved..." class="w-full p-3 rounded-xl border border-slate-200 text-xs bg-slate-50"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Lucide Icon Name') }}</label>
                    <input type="text" name="icon" value="wrench" required placeholder="e.g. sun, zap, droplet, hammer" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="createModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 rounded-xl hover:bg-slate-100">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white rounded-xl shadow">{{ __('Create Category') }}</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection