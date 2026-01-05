@extends('layouts.app_dashboard')
@include('layouts.menus.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10 space-y-6">

    {{-- Header --}}
    <div>
        <p class="text-xs font-bold text-blue-600 uppercase tracking-widest flex items-center gap-2">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
            Admin Command Center
        </p>
        <h1 class="text-3xl font-black text-slate-900 mt-1">Sistem Operasional.</h1>
        <p class="text-slate-500 text-sm mt-1">Update status laporan dan kelola konten edukasi.</p>
    </div>

    {{-- 4 Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Update Status Laporan --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <div class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center mb-4">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-rose-500"></i>
            </div>
            <p class="text-xs font-bold text-rose-500 uppercase tracking-wide">Update Status Laporan</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $laporanPending }} <span class="text-base font-bold text-slate-400">Baru</span></p>
        </div>

        {{-- Mengelola Panduan --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center mb-4">
                <i data-lucide="book-open" class="w-6 h-6 text-blue-500"></i>
            </div>
            <p class="text-xs font-bold text-blue-500 uppercase tracking-wide">Mengelola Panduan & Artikel</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalArtikel }} <span class="text-base font-bold text-slate-400">Konten</span></p>
        </div>

        {{-- Kelola Trayek --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center mb-4">
                <i data-lucide="signpost" class="w-6 h-6 text-emerald-500"></i>
            </div>
            <p class="text-xs font-bold text-emerald-500 uppercase tracking-wide">Kelola Data Trayek</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalTrayek }} <span class="text-base font-bold text-slate-400">Aktif</span></p>
        </div>

        {{-- Verifikasi Akun --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center mb-4">
                <i data-lucide="shield-check" class="w-6 h-6 text-purple-500"></i>
            </div>
            <p class="text-xs font-bold text-purple-500 uppercase tracking-wide">Verifikasi Akun</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $driverPending }} <span class="text-base font-bold text-slate-400">Siap</span></p>
        </div>
    </div>

    {{-- Recent Reports Section --}}
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
        <h2 class="text-lg font-black text-slate-900 mb-4">Laporan Terbaru</h2>
        
        @if($recentLaporans->count() > 0)
            <div class="space-y-3">
                @foreach($recentLaporans as $laporan)
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-500"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ ucfirst($laporan->kategori ?? 'Umum') }} - R-{{ str_pad($laporan->id, 3, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-sm text-slate-500">{{ Str::limit($laporan->judul, 40) }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.laporan.index') }}" class="text-sm font-bold text-rose-500 hover:text-rose-700 transition">
                        DETAIL & UPDATE
                    </a>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                </div>
                <p class="text-slate-500">Belum ada laporan baru</p>
            </div>
        @endif
    </div>
</div>
@endsection
