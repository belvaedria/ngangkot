@extends('layouts.app')
@section('title', 'Riwayat Perjalanan - Ngangkot')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-black mb-6">Riwayat Perjalanan</h1>

    <!-- Favorit (Quick Access) -->
    <div class="mb-8">
        <h2 class="text-sm font-black text-slate-600 mb-4">Favorit</h2>
        @if($favorit->isEmpty())
            <div class="p-4 bg-white rounded-lg text-slate-400">Belum ada favorit. Simpan rute dari riwayat Anda.</div>
        @else
            <div class="grid gap-4">
                @foreach($favorit as $f)
                <div class="bg-white p-4 rounded-2xl shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-sm font-black">{{ $f->nama_label }}</div>
                        <div class="text-xs text-slate-400">{{ $f->asal_nama }} → {{ $f->tujuan_nama }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('navigasi.index') }}?nama_asal={{ urlencode($f->asal_nama) }}&asal_coords={{ urlencode($f->asal_coords) }}&nama_tujuan={{ urlencode($f->tujuan_nama) }}&tujuan_coords={{ urlencode($f->tujuan_coords) }}" class="px-3 py-2 bg-blue-600 text-white rounded-lg font-bold text-sm">Gunakan</a>
                        <form action="{{ route('passenger.favorit.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Hapus favorit ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-2 bg-slate-100 rounded-lg text-sm">Hapus</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Riwayat Terakhir -->
    <div>
        <h2 class="text-sm font-black text-slate-600 mb-4">Riwayat Terakhir</h2>
        @if($riwayat->isEmpty())
            <div class="p-4 bg-white rounded-lg text-slate-400">Belum ada riwayat perjalanan. Cari rute dan sistem akan menyimpan riwayat jika Anda masuk.</div>
        @else
            <div class="space-y-4">
                @foreach($riwayat as $r)
                <div class="bg-white p-4 rounded-2xl shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-sm font-black">{{ $r->asal_nama }} → {{ $r->tujuan_nama }}</div>
                        <div class="text-xs text-slate-400">{{ $r->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Save to favorite (shows a small inline form) -->
                        <form action="{{ route('passenger.favorit.store') }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="asal_nama" value="{{ $r->asal_nama }}">
                            <input type="hidden" name="tujuan_nama" value="{{ $r->tujuan_nama }}">
                            <input type="hidden" name="asal_coords" value="{{ $r->asal_coords }}">
                            <input type="hidden" name="tujuan_coords" value="{{ $r->tujuan_coords }}">
                            <input type="text" name="label" placeholder="Label (Rumah, Kampus)" class="px-3 py-2 border rounded-lg text-sm" required>
                            <button class="px-3 py-2 bg-emerald-600 text-white rounded-lg font-bold text-sm">Simpan</button>
                        </form>
                        <a href="{{ route('navigasi.index') }}?nama_asal={{ urlencode($r->asal_nama) }}&asal_coords={{ urlencode($r->asal_coords) }}&nama_tujuan={{ urlencode($r->tujuan_nama) }}&tujuan_coords={{ urlencode($r->tujuan_coords) }}" class="px-3 py-2 bg-blue-600 text-white rounded-lg font-bold text-sm">Gunakan</a>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection