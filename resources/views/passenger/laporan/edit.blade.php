@extends('layouts.app_dashboard')
@include('layouts.menus.passenger')

@section('title', 'Edit Laporan - Ngangkot')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
<div class="bg-slate-50 min-h-screen py-12 px-6">
    <div class="max-w-3xl mx-auto">

        {{-- Back Button --}}
        <a href="{{ route('passenger.laporan.index') }}"
           class="inline-flex items-center gap-2 text-slate-600 hover:text-blue-600 font-semibold mb-6 transition">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            Kembali ke Daftar Laporan
        </a>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Edit Laporan</h1>
            <p class="text-slate-600">Perbarui informasi laporan Anda sebelum diproses oleh admin.</p>
        </div>

        {{-- Warning Card --}}
        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5 mb-8">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-amber-900 mb-1">Perhatian</h4>
                    <p class="text-sm text-amber-700">Anda hanya dapat mengedit laporan selama statusnya masih "Menunggu". Setelah laporan diproses, perubahan tidak dapat dilakukan.</p>
                </div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
            <form action="{{ route('passenger.laporan.update', $laporan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Judul --}}
                <div>
                    <label for="judul" class="block text-sm font-bold text-slate-700 mb-2">
                        Judul Laporan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="judul" 
                           id="judul"
                           value="{{ old('judul', $laporan->judul) }}"
                           placeholder="Contoh: Sopir angkot berkendara ugal-ugalan"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition @error('judul') border-rose-300 bg-rose-50 @enderror">
                    @error('judul')
                        <p class="text-rose-500 text-sm mt-2 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Isi Laporan --}}
                <div>
                    <label for="isi" class="block text-sm font-bold text-slate-700 mb-2">
                        Isi Laporan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="isi" 
                              id="isi" 
                              rows="6"
                              placeholder="Jelaskan detail laporan Anda di sini..."
                              class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition resize-none @error('isi') border-rose-300 bg-rose-50 @enderror">{{ old('isi', $laporan->isi) }}</textarea>
                    @error('isi')
                        <p class="text-rose-500 text-sm mt-2 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="text-slate-400 text-xs mt-2">Minimal 20 karakter</p>
                </div>

                {{-- Bukti Foto --}}
                <div>
                    <label for="bukti_foto" class="block text-sm font-bold text-slate-700 mb-2">
                        Bukti Foto <span class="text-slate-400 font-normal">(opsional)</span>
                    </label>

                    {{-- Current Photo --}}
                    @if($laporan->bukti_foto)
                        <div class="mb-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="text-xs font-semibold text-slate-500 mb-2">Foto saat ini:</p>
                            <img src="{{ Storage::url($laporan->bukti_foto) }}" 
                                 alt="Bukti foto"
                                 class="max-h-40 rounded-xl border border-slate-200">
                        </div>
                    @endif

                    <div class="relative">
                        <input type="file" 
                               name="bukti_foto" 
                               id="bukti_foto"
                               accept="image/jpeg,image/png,image/jpg,image/gif"
                               class="hidden"
                               onchange="previewImage(this)">
                        
                        <label for="bukti_foto" 
                               class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition @error('bukti_foto') border-rose-300 bg-rose-50 @enderror"
                               id="dropzone">
                            <div class="flex flex-col items-center justify-center py-4" id="upload-prompt">
                                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center mb-2">
                                    <i data-lucide="image-plus" class="w-5 h-5 text-slate-400"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-600">{{ $laporan->bukti_foto ? 'Ganti foto' : 'Upload foto baru' }}</p>
                                <p class="text-xs text-slate-400 mt-1">JPG, PNG, GIF (Maks. 2MB)</p>
                            </div>
                            <div class="hidden" id="preview-container">
                                <img id="preview-image" src="" alt="Preview" class="max-h-24 rounded-xl">
                            </div>
                        </label>
                    </div>
                    @error('bukti_foto')
                        <p class="text-rose-500 text-sm mt-2 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Submit Buttons --}}
                <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                    <button type="submit"
                            class="flex-1 py-3 rounded-2xl bg-blue-600 text-white font-bold tracking-tight shadow-lg shadow-blue-200 hover:bg-blue-700 transition flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('passenger.laporan.index') }}"
                       class="px-6 py-3 rounded-2xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });

    function previewImage(input) {
        const preview = document.getElementById('preview-image');
        const previewContainer = document.getElementById('preview-container');
        const uploadPrompt = document.getElementById('upload-prompt');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('hidden');
                uploadPrompt.classList.add('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
