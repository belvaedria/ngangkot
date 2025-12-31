@extends('layouts.app_dashboard')
@include('layouts.menus.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10 space-y-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase">Trayek</p>
            <h3 class="text-2xl font-black text-slate-900">{{ $totalTrayek }}</h3>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase">Armada</p>
            <h3 class="text-2xl font-black text-slate-900">{{ $totalAngkot }}</h3>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase">Laporan pending</p>
            <h3 class="text-2xl font-black text-slate-900">{{ $laporanPending }}</h3>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase">Total pengguna</p>
            <h3 class="text-2xl font-black text-slate-900">{{ $totalPengguna }}</h3>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
        <h2 class="text-xl font-black text-slate-900 mb-3">Tindakan cepat</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.trayek.index') }}" class="p-4 rounded-2xl border border-slate-100 bg-slate-50/70 flex items-center gap-3 hover:-translate-y-1 transition">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center"><i data-lucide="signpost-big" class="w-5 h-5"></i></div>
                <div>
                    <p class="text-sm font-black text-slate-900">Kelola Trayek</p>
                    <p class="text-[11px] text-slate-500">Tambah atau ubah rute</p>
                </div>
            </a>
            <a href="{{ route('admin.verifikasi.index') }}" class="p-4 rounded-2xl border border-slate-100 bg-slate-50/70 flex items-center gap-3 hover:-translate-y-1 transition">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center"><i data-lucide="badge-check" class="w-5 h-5"></i></div>
                <div>
                    <p class="text-sm font-black text-slate-900">Verifikasi Driver</p>
                    <p class="text-[11px] text-slate-500">Setujui atau tolak</p>
                </div>
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="p-4 rounded-2xl border border-slate-100 bg-slate-50/70 flex items-center gap-3 hover:-translate-y-1 transition">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center"><i data-lucide="inbox" class="w-5 h-5"></i></div>
                <div>
                    <p class="text-sm font-black text-slate-900">Laporan</p>
                    <p class="text-[11px] text-slate-500">Tanggapi laporan pengguna</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
