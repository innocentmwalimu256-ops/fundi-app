@extends('layouts.app')

@section('title', __('Work Portfolio') . ' - FUNDI')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" 
     x-data="{ 
        uploadModal: false, 
        imagePreview: null,
        handleImageSelect(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => { this.imagePreview = e.target.result; };
                reader.readAsDataURL(file);
            } else {
                this.imagePreview = null;
            }
        },
        removeImage() {
            this.imagePreview = null;
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        }
     }">

    <!-- Clean Modern Header -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 flex-shrink-0 shadow-sm">
                <i data-lucide="camera" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ __('Work Photos') }}</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ __('Upload photos of previous installations and repairs to build trust with clients.') }}</p>
            </div>
        </div>

        <button type="button" @click="uploadModal = true" class="inline-flex items-center justify-center space-x-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-sm hover:shadow transition transform active:scale-95 self-start sm:self-auto cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>{{ __('Add Portfolio Project') }}</span>
        </button>
    </div>

    <!-- Portfolio Content -->
    @if(count($portfolios) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($portfolios as $port)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md hover:border-slate-300 transition duration-200 group">
            <div>
                @if($port->image_path)
                <div class="aspect-[16/10] bg-slate-100 overflow-hidden relative">
                    <img src="{{ asset('storage/' . $port->image_path) }}" alt="{{ $port->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @if($port->service)
                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-slate-900/80 text-teal-300 font-semibold text-[10px] backdrop-blur-sm border border-slate-700/50">
                        {{ $port->service->name }}
                    </span>
                    @endif
                </div>
                @endif

                <div class="p-4 sm:p-5 space-y-1.5">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-sm font-bold text-slate-900 truncate">{{ $port->title }}</h3>
                        <span class="text-[10px] text-slate-400 font-medium whitespace-nowrap">{{ $port->project_date }}</span>
                    </div>
                    <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">{{ $port->description }}</p>
                </div>
            </div>

            <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-[11px] text-slate-400">{{ $port->created_at->format('d M Y') }}</span>
                <form method="POST" action="{{ route('technician.portfolios.destroy', $port->id) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this photo?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-700 transition cursor-pointer">
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Beautiful & Clean Empty State Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-8 sm:p-14 text-center space-y-5 shadow-sm">
        <div class="relative w-20 h-20 mx-auto">
            <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-teal-50 to-teal-100/60 border border-teal-200/80 flex items-center justify-center text-teal-600 shadow-sm">
                <i data-lucide="image-plus" class="w-9 h-9 text-teal-600"></i>
            </div>
            <span class="absolute -top-1 -right-1 w-4 h-4 bg-teal-500 rounded-full border-2 border-white"></span>
        </div>

        <div class="space-y-2 max-w-md mx-auto">
            <h3 class="text-lg sm:text-xl font-bold text-slate-900">{{ __('No Portfolio Photos Yet') }}</h3>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                {{ __('Upload photos of past house wirings, pipe fittings, AC installations, or carpentry projects to showcase your skills to clients.') }}
            </p>
        </div>

        <div>
            <button type="button" @click="uploadModal = true" class="inline-flex items-center space-x-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-teal-700/20 hover:shadow-lg transition transform hover:-translate-y-0.5 cursor-pointer">
                <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                <span>{{ __('Upload First Photo') }}</span>
            </button>
        </div>

        <div class="pt-2 flex flex-wrap items-center justify-center gap-4 text-[11px] text-slate-400 font-medium">
            <span class="flex items-center space-x-1">
                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-teal-600"></i>
                <span>{{ __('Visible to Clients') }}</span>
            </span>
            <span class="flex items-center space-x-1">
                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-teal-600"></i>
                <span>{{ __('Verified FUNDI Professional') }}</span>
            </span>
            <span class="flex items-center space-x-1">
                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-teal-600"></i>
                <span>JPG / PNG / WEBP</span>
            </span>
        </div>
    </div>
    @endif

    <!-- Simple & Clean Upload Modal -->
    <div x-show="uploadModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition>

        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 text-left" 
             @click.outside="uploadModal = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">{{ __('Upload Work Photo') }}</h3>
                </div>
                <button type="button" @click="uploadModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('technician.portfolios.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Project Title') }} <span class="text-rose-500">*</span></label>
                    <input type="text" 
                           name="title" 
                           required 
                           placeholder="{{ __('Project Title') }}" 
                           class="w-full py-2.5 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 bg-slate-50 focus:bg-white focus:border-teal-600 focus:outline-none transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Service Category') }}</label>
                        <select name="service_id" class="w-full py-2.5 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 bg-slate-50 focus:bg-white focus:border-teal-600 focus:outline-none transition">
                            <option value="">{{ __('General Trade') }}</option>
                            @foreach($services as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Project Date') }}</label>
                        <input type="text" 
                               name="project_date" 
                               placeholder="e.g. {{ date('F Y') }}" 
                               value="{{ date('F Y') }}"
                               class="w-full py-2.5 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 bg-slate-50 focus:bg-white focus:border-teal-600 focus:outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Project Description') }} <span class="text-rose-500">*</span></label>
                    <textarea name="description" 
                              rows="2" 
                              required 
                              placeholder="{{ __('Project Description') }}..." 
                              class="w-full p-3 rounded-xl border border-slate-200 text-xs text-slate-900 bg-slate-50 focus:bg-white focus:border-teal-600 focus:outline-none transition"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">{{ __('Select Work Photo') }} <span class="text-rose-500">*</span></label>
                    
                    <div x-show="imagePreview" class="relative rounded-2xl overflow-hidden border-2 border-teal-500 mb-2 bg-slate-100 aspect-video max-h-44">
                        <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover">
                        <button type="button" 
                                @click="removeImage()" 
                                class="absolute top-2 right-2 px-2.5 py-1 bg-rose-600 text-white rounded-lg text-xs font-bold shadow cursor-pointer">
                            {{ __('Change Photo') }}
                        </button>
                    </div>

                    <input type="file" 
                           x-ref="fileInput"
                           name="image" 
                           required 
                           accept="image/*" 
                           @change="handleImageSelect($event)"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-600 file:text-white hover:file:bg-teal-700 file:cursor-pointer transition">
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" 
                            @click="uploadModal = false" 
                            class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white rounded-xl shadow-sm transition cursor-pointer">
                        {{ __('Save Photo') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

