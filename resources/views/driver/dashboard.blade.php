@extends('layouts.app_dashboard')
@include('layouts.menus.driver')

@section('title', 'Dashboard Driver')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10 space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase">Status Akun</p>
                    <h1 class="text-2xl font-black text-slate-900">Halo, {{ strtok(Auth::user()->name, ' ') }}!</h1>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusAkun === 'verified' ? 'bg-emerald-50 text-emerald-700' : ($statusAkun === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                    {{ $statusAkun }}
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/60">
                    <p class="text-xs font-bold text-slate-500 mb-1">Armada aktif</p>
                    <h3 class="text-lg font-black text-slate-900">{{ $angkot ? $angkot->plat_nomor : 'Belum pilih' }}</h3>
                    <p class="text-xs text-slate-500">{{ $angkot ? 'Trayek: '.$angkot->trayek?->nama_trayek : 'Silakan pilih di menu Tracking' }}</p>
                </div>
                <div class="p-4 rounded-2xl border border-slate-100 bg-blue-50/60">
                    <p class="text-xs font-bold text-blue-700 mb-1">Mulai berbagi lokasi</p>
                    <a href="{{ route('driver.tracking.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-blue-700">
                        Buka Tracking <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-black text-slate-900">Riwayat narik</h3>
                <a href="{{ route('driver.riwayat.index') }}" class="text-[10px] font-bold text-blue-600">Lihat semua</a>
            </div>
            <div class="space-y-3 max-h-[280px] overflow-y-auto custom-scroll pr-1">
                @forelse($riwayatTerbaru as $riw)
                    <div class="border border-slate-100 rounded-xl p-3 bg-slate-50/80">
                        <p class="text-xs font-bold text-slate-500">{{ $riw->waktu_mulai->format('d M Y H:i') }}</p>
                        <p class="text-sm font-black text-slate-900">Jarak: {{ number_format($riw->jarak_tempuh_km, 2) }} km</p>
                        <p class="text-[11px] text-slate-500">{{ $riw->waktu_selesai ? 'Selesai' : 'Masih berjalan' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada riwayat.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
