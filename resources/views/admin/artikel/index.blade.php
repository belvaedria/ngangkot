@extends('layouts.app_dashboard')
@include('layouts.menus.admin')

@section('title', 'Mengelola Panduan')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900">Edukasi Wargi.</h1>
            <p class="text-slate-500 text-sm mt-1">Panduan & Artikel Transportasi Bandung.</p>
        </div>
        <a href="{{ route('admin.artikel.create') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-blue-600 text-white font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Tambah Konten
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 mt-0.5"></i>
            <p class="font-semibold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Artikel Grid --}}
    @if($artikels->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="book-open" class="w-8 h-8 text-slate-400"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Konten</h3>
            <p class="text-slate-500">Mulai tambahkan artikel edukasi untuk wargi</p>
            <a href="{{ route('admin.artikel.create') }}" class="inline-flex items-center gap-2 px-6 py-3 mt-4 rounded-2xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Tambah Konten Pertama
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($artikels as $artikel)
            <div class="bg-slate-50 rounded-3xl p-8">
                {{-- Icon --}}
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-5">
                    <i data-lucide="book-open" class="w-8 h-8 text-blue-500"></i>
                </div>

                {{-- Content --}}
                <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $artikel->judul }}</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">
                    {{ Str::limit(strip_tags($artikel->konten), 120) }}
                </p>

                {{-- Actions --}}
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.artikel.edit', $artikel) }}" 
                       class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-800 transition">
                        <span>EDIT</span>
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                    </a>
                    <form action="{{ route('admin.artikel.destroy', $artikel) }}" method="POST" class="inline"
                          onsubmit="return confirm('Yakin hapus artikel ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center gap-2 text-sm font-bold text-rose-600 hover:text-rose-800 transition">
                            <span>HAPUS</span>
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
