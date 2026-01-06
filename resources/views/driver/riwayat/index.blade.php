@extends('layouts.app_dashboard')
@include('layouts.menus.driver')

@section('title', 'Catatan Perjalanan')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Catatan Perjalanan</p>
                <h2 class="text-2xl font-black text-slate-900">Riwayat narik</h2>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($riwayats as $riw)
                <div class="border border-slate-100 rounded-2xl p-4 bg-white shadow-sm">
                    <p class="text-xs font-bold text-slate-500">{{ $riw->waktu_mulai->format('d M Y H:i') }}</p>
                    <p class="text-sm font-black text-slate-900 mt-1">Jarak: {{ number_format($riw->jarak_tempuh_km, 2) }} km</p>
                    <p class="text-[11px] text-slate-500">Selesai: {{ $riw->waktu_selesai ? $riw->waktu_selesai->format('d M Y H:i') : 'Masih berjalan' }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">Belum ada data.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
