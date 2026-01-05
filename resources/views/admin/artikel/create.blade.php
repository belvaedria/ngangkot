@extends('layouts.app_dashboard')
@include('layouts.menus.admin')

@section('title', 'Tambah Konten')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10 space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.artikel.index') }}" 
           class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center hover:bg-slate-200 transition">
            <i data-lucide="arrow-left" class="w-5 h-5 text-slate-600"></i>
        </a>
        <div>
            <h1 class="text-3xl font-black text-slate-900">Tambah Konten.</h1>
            <p class="text-slate-500 text-sm mt-1">Buat artikel edukasi baru untuk wargi</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
        <form action="{{ route('admin.artikel.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Judul --}}
            <div>
                <label class="text-sm font-bold text-slate-700 mb-2 block">Judul Artikel</label>
                <div class="relative">
                    <i data-lucide="type" class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="judul" value="{{ old('judul') }}" required
                           class="w-full pl-12 pr-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                           placeholder="Masukkan judul artikel...">
                </div>
                @error('judul')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kategori --}}
            <div>
                <label class="text-sm font-bold text-slate-700 mb-2 block">Kategori</label>
                <div class="relative">
                    <i data-lucide="tag" class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                    <select name="kategori" required
                            class="w-full pl-12 pr-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition appearance-none">
                        <option value="">Pilih kategori...</option>
                        <option value="tips" {{ old('kategori') === 'tips' ? 'selected' : '' }}>Tips & Trik</option>
                        <option value="info_traffic" {{ old('kategori') === 'info_traffic' ? 'selected' : '' }}>Info Traffic</option>
                        <option value="umum" {{ old('kategori') === 'umum' ? 'selected' : '' }}>Informasi Umum</option>
                    </select>
                </div>
                @error('kategori')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konten --}}
            <div>
                <label class="text-sm font-bold text-slate-700 mb-2 block">Konten Artikel</label>
                <textarea name="konten" rows="8" required
                          class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition resize-none"
                          placeholder="Tulis konten artikel di sini...">{{ old('konten') }}</textarea>
                @error('konten')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Gambar --}}
            <div>
                <label class="text-sm font-bold text-slate-700 mb-2 block">Gambar (Opsional)</label>
                <div class="relative">
                    <input type="file" name="gambar" accept="image/*"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-blue-100 file:text-blue-700 file:font-bold file:cursor-pointer">
                </div>
                <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                @error('gambar')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-4 pt-4">
                <button type="submit"
                        class="px-8 py-3 bg-blue-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Simpan Artikel
                </button>
                <a href="{{ route('admin.artikel.index') }}" 
                   class="px-8 py-3 bg-slate-100 text-slate-700 font-bold rounded-2xl hover:bg-slate-200 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
