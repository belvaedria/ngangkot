@extends('layouts.app_dashboard')

@section('title', 'Daftar Trayek')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-black mb-4">Daftar Trayek</h1>

    @if($trayeks->isEmpty())
        <div class="p-4 bg-white rounded-md text-slate-500">Tidak ada data trayek.</div>
    @else
        <div class="grid gap-4">
            @foreach($trayeks as $trayek)
                <a href="{{ route('trayek.show', $trayek->kode_trayek) }}" class="p-4 bg-white rounded-lg shadow-sm hover:shadow-md flex items-center justify-between">
                    <div>
                        <div class="font-bold">{{ $trayek->nama_trayek }}</div>
                        <div class="text-xs text-slate-400">Kode: {{ $trayek->kode_trayek }}</div>
                    </div>
                    <div class="text-sm font-black" style="color: {{ $trayek->warna_angkot }}">&bull;</div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection