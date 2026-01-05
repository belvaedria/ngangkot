@extends('layouts.app_dashboard')
@include('layouts.menus.driver')

@section('title', 'Dashboard Driver')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Content - Left Side --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Profile Card --}}
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-8">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-slate-900 rounded-2xl flex items-center justify-center text-white">
                        <i data-lucide="user" class="w-10 h-10"></i>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl font-black text-slate-900">{{ Auth::user()->name }}</h1>
                        <div class="flex items-center gap-4 mt-2">
                            <p class="text-sm text-slate-600">
                                <span class="font-bold text-blue-600">TRAYEK {{ $angkot?->trayek?->kode_trayek ?? '-' }}</span>
                            </p>
                            <span class="text-slate-300">•</span>
                            <p class="text-sm font-bold text-slate-600">
                                {{ $angkot?->plat_nomor ?? 'Belum ada armada' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 gap-6">
                {{-- Trip Hari Ini --}}
                <div class="bg-blue-50 border border-blue-100 rounded-3xl shadow-sm p-6">
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">Trip Hari Ini</p>
                    <h2 class="text-4xl font-black text-blue-700 mb-1">{{ $tripHariIni }}</h2>
                    <p class="text-sm text-blue-600 font-semibold">Putaran</p>
                </div>

                {{-- Pendapatan --}}
                <div class="bg-emerald-50 border border-emerald-100 rounded-3xl shadow-sm p-6">
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2">Pendapatan</p>
                    <h2 class="text-4xl font-black text-emerald-700 mb-1">Rp {{ number_format($pendapatanHariIni / 1000) }}k</h2>
                    <p class="text-sm text-emerald-600 font-semibold">Hari ini</p>
                </div>
            </div>

            {{-- Heatmap Alert --}}
            <div class="bg-slate-900 rounded-3xl shadow-lg p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-blue-500/20 to-transparent rounded-full -translate-y-20 translate-x-20"></div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-rose-400 uppercase tracking-widest mb-3">Heatmap Penumpang</p>
                    <h3 class="text-2xl font-black mb-4 leading-tight">
                        Wilayah Terminal Cicaheum sedang Ramai!
                    </h3>
                    <p class="text-slate-300 text-sm mb-2">
                        Sistem mendeteksi lonjakan calon penumpang di sektor C-3.
                    </p>
                    <p class="text-slate-400 text-sm mb-6">
                        Disarankan untuk segera menuju lokasi penjemputan terdekat.
                    </p>
                    <div class="flex items-center gap-4">
                        <button class="px-6 py-3 bg-white text-slate-900 rounded-xl font-bold text-sm hover:bg-slate-100 transition">
                            Navigasi ke Lokasi
                        </button>
                        <button class="px-6 py-3 bg-transparent border-2 border-white/20 text-white rounded-xl font-bold text-sm hover:bg-white/10 transition">
                            Abaikan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-6">
            
            {{-- Online Status Card --}}
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl shadow-lg p-8 text-center text-white">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="{{ $isOnline ? 'zap' : 'zap-off' }}" class="w-10 h-10 text-emerald-600"></i>
                </div>
                <h3 class="text-2xl font-black mb-2">{{ $isOnline ? 'ONLINE' : 'OFFLINE' }}</h3>
                <p class="text-emerald-100 text-sm mb-6">
                    {{ $isOnline ? 'Kamu sedang dalam perjalanan' : 'Mulai narik sekarang' }}
                </p>
                @if($isOnline)
                    <button class="w-full py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition">
                        Akhiri Tugas
                    </button>
                @else
                    <a href="{{ route('driver.tracking.index') }}" class="block w-full py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition">
                        Mulai Narik
                    </a>
                @endif
            </div>

            {{-- Notifikasi Card --}}
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-slate-900">Notifikasi</h3>
                    <span class="w-2 h-2 bg-rose-500 rounded-full"></span>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-blue-50 border border-blue-100">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                            <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-bold text-blue-900">Perpanjangan ijin trayek Anda</p>
                            <p class="text-xs text-blue-700 mt-1">jatuh tempo dalam 3 hari.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-xl bg-amber-50 border border-amber-100">
                        <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center shrink-0">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-bold text-amber-900">Laporan kemacetan di Pasteur.</p>
                            <p class="text-xs text-amber-700 mt-1">Gunakan rute alternatif.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
