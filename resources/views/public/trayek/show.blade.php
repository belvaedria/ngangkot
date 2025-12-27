@extends('layouts.app_dashboard')

@section('title', "Trayek: $trayek->nama_trayek")

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h1 class="text-xl font-black">{{ $trayek->nama_trayek }} <span class="text-sm text-slate-400">({{ $trayek->kode_trayek }})</span></h1>
        <p class="text-sm text-slate-500 mt-2">Warna: <span style="color: {{ $trayek->warna_angkot }}">●</span></p>

        @if($trayek->rute_json)
            <div class="mt-4 text-sm text-slate-600">Rute JSON tersedia. (Preview di peta belum diimplementasikan di halaman ini.)</div>
        @else
            <div class="mt-4 text-sm text-slate-400">Tidak ada data rute.</div>
        @endif

        <div class="mt-6">
            <a href="{{ route('trayek.index') }}" class="text-sm text-blue-600">&larr; Kembali ke daftar trayek</a>
        </div>
    </div>
</div>
@endsection