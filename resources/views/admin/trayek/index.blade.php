@extends('layouts.app_dashboard')
@include('layouts.menus.admin')

@section('title', 'Kelola Trayek')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-slate-500 uppercase">Trayek</p>
            <h1 class="text-2xl font-black text-slate-900">Data Trayek</h1>
        </div>
        <a href="{{ route('admin.trayek.create') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-bold">Tambah Trayek</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($trayeks as $trayek)
            <div class="border border-slate-100 rounded-2xl p-4 bg-white shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold text-slate-500 uppercase">{{ $trayek->kode_trayek }}</p>
                    <span class="w-3 h-3 rounded-full" style="background: {{ $trayek->warna_angkot }}"></span>
                </div>
                <p class="text-sm font-black text-slate-900">{{ $trayek->nama_trayek }}</p>
                <p class="text-xs text-slate-500">Harga: Rp {{ number_format($trayek->harga) }}</p>
                <div class="flex items-center gap-2 pt-2">
                    <a href="{{ route('admin.trayek.edit', $trayek->id) }}" class="text-xs font-bold text-blue-600">Edit</a>
                    <form action="{{ route('admin.trayek.destroy', $trayek->id) }}" method="POST" onsubmit="return confirm('Hapus trayek?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs font-bold text-rose-600">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500">Belum ada data.</p>
        @endforelse
    </div>
</div>
@endsection
