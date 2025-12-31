@extends('layouts.app_dashboard')
@include('layouts.menus.driver')

@section('title', 'Armada Saya')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-slate-500 uppercase">Armada</p>
            <h1 class="text-2xl font-black text-slate-900">Angkot terdaftar</h1>
        </div>
        <a href="{{ route('driver.angkot.create') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-bold">Tambah Armada</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($angkots as $item)
            <div class="border border-slate-100 rounded-2xl p-4 bg-white shadow-sm space-y-2">
                <p class="text-xs font-bold text-slate-500 uppercase">{{ $item->plat_nomor }}</p>
                <p class="text-sm font-black text-slate-900">Trayek: {{ $item->trayek?->nama_trayek }}</p>
                <div class="flex items-center gap-2">
                    <a href="{{ route('driver.angkot.edit', $item) }}" class="text-xs font-bold text-blue-600">Edit</a>
                    <form action="{{ route('driver.angkot.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus armada?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs font-bold text-rose-600">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500">Belum ada armada.</p>
        @endforelse
    </div>
</div>
@endsection
