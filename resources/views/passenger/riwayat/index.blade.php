@extends('layouts.app_dashboard')
@include('layouts.menus.passenger')

@section('title', 'Riwayat Perjalanan')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-black text-slate-900">Riwayat Pencarian</h2>
                </div>
            </div>
            @if ($errors->any())
            <div class="p-3 rounded-xl bg-red-50 text-red-700 text-sm">
                <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </div>
            @endif
            <div class="space-y-3 max-h-[520px] overflow-y-auto custom-scroll pr-1">
                @forelse($riwayat as $item)
                @php
                    $key = $item->asal_coords.'|'.$item->tujuan_coords;
                    $favId = $favoritMap[$key] ?? null;
                @endphp

                <div class="flex items-center gap-3 border border-slate-100 rounded-2xl p-4 bg-slate-50/50">
                    {{-- Ikon kiri --}}
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>

                    {{-- Konten --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black text-slate-900 truncate">{{ $item->asal_nama }} → {{ $item->tujuan_nama }}</p>
                        <p class="text-[10px] text-slate-500">{{ $item->created_at->format('d M Y H:i') }}</p>
                    </div>

                    {{-- Aksi kanan: favorit --}}
                    <div class="flex items-center gap-2">
                        @if($favId)
                            <form action="{{ route('passenger.favorit.destroy', $favId) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center hover:bg-amber-100"
                                    title="Hapus dari favorit"
                                >
                                    <i data-lucide="star" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('passenger.favorit.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="label" value="{{ $item->asal_nama }} → {{ $item->tujuan_nama }}">
                                <input type="hidden" name="asal_nama" value="{{ $item->asal_nama }}">
                                <input type="hidden" name="tujuan_nama" value="{{ $item->tujuan_nama }}">
                                <input type="hidden" name="asal_coords" value="{{ $item->asal_coords }}">
                                <input type="hidden" name="tujuan_coords" value="{{ $item->tujuan_coords }}">
                                <button
                                    type="submit"
                                    class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-50"
                                    title="Tambahkan ke favorit"
                                >
                                    <i data-lucide="star" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('navigasi.search') }}" method="POST">
                            @csrf
                            <input type="hidden" name="lat_asal" value="{{ explode(',', $item->asal_coords)[0] }}">
                            <input type="hidden" name="lng_asal" value="{{ explode(',', $item->asal_coords)[1] }}">
                            <input type="hidden" name="lat_tujuan" value="{{ explode(',', $item->tujuan_coords)[0] }}">
                            <input type="hidden" name="lng_tujuan" value="{{ explode(',', $item->tujuan_coords)[1] }}">
                            <input type="hidden" name="nama_asal" value="{{ $item->asal_nama }}">
                            <input type="hidden" name="nama_tujuan" value="{{ $item->tujuan_nama }}">
                        </form>
                    </div>
                </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada pencarian disimpan.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-black text-slate-900">Rute Favorit</h2>
                </div>
            </div>
            <div class="space-y-3 max-h-[520px] overflow-y-auto custom-scroll pr-1">
                @forelse($favorit as $fav)
                <form action="{{ route('navigasi.search') }}" method="POST" class="flex items-center gap-3 border border-amber-100 rounded-2xl p-4 bg-amber-50/50">
                    @csrf
                    <input type="hidden" name="lat_asal" value="{{ explode(',', $fav->asal_coords)[0] }}">
                    <input type="hidden" name="lng_asal" value="{{ explode(',', $fav->asal_coords)[1] }}">
                    <input type="hidden" name="lat_tujuan" value="{{ explode(',', $fav->tujuan_coords)[0] }}">
                    <input type="hidden" name="lng_tujuan" value="{{ explode(',', $fav->tujuan_coords)[1] }}">
                    <input type="hidden" name="nama_asal" value="{{ $fav->asal_nama }}">
                    <input type="hidden" name="nama_tujuan" value="{{ $fav->tujuan_nama }}">
                    <div class="w-12 h-12 rounded-xl bg-yellow-200 text-amber-700 flex items-center justify-center shrink-0">
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-black text-amber-700">{{ $fav->nama_label }}</p>
                    </div>
                    <button class="text-[11px] font-bold text-blue-600 hover:underline">Pakai</button>
                </form>
                @empty
                    <p class="text-sm text-slate-500">Belum ada favorit.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
