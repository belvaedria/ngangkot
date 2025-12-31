@extends('layouts.app_dashboard')
@include('layouts.menus.passenger')

@section('title', 'Riwayat Perjalanan')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase">Riwayat Pencarian</p>
                    <h2 class="text-xl font-black text-slate-900">20 rute terakhir</h2>
                </div>
            </div>
            <div class="space-y-3 max-h-[520px] overflow-y-auto custom-scroll pr-1">
                @forelse($riwayat as $item)
                <form action="{{ route('navigasi.search') }}" method="POST" class="flex items-center gap-3 border border-slate-100 rounded-2xl p-4 bg-slate-50/50">
                    @csrf
                    <input type="hidden" name="lat_asal" value="{{ explode(',', $item->asal_coords)[0] }}">
                    <input type="hidden" name="lng_asal" value="{{ explode(',', $item->asal_coords)[1] }}">
                    <input type="hidden" name="lat_tujuan" value="{{ explode(',', $item->tujuan_coords)[0] }}">
                    <input type="hidden" name="lng_tujuan" value="{{ explode(',', $item->tujuan_coords)[1] }}">
                    <input type="hidden" name="nama_asal" value="{{ $item->asal_nama }}">
                    <input type="hidden" name="nama_tujuan" value="{{ $item->tujuan_nama }}">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-slate-900">{{ $item->asal_nama }} → {{ $item->tujuan_nama }}</p>
                        <p class="text-[10px] text-slate-500">{{ $item->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <button class="text-[11px] font-bold text-blue-600 hover:underline">Pakai</button>
                </form>
                @empty
                    <p class="text-sm text-slate-500">Belum ada pencarian disimpan.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase">Rute Favorit</p>
                    <h2 class="text-xl font-black text-slate-900">Cepat pakai lagi</h2>
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
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <i data-lucide="star" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-black text-amber-700 uppercase">{{ $fav->nama_label }}</p>
                        <p class="text-sm font-black text-slate-900">{{ $fav->asal_nama }} → {{ $fav->tujuan_nama }}</p>
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
