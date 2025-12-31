@extends('layouts.app_dashboard')
@include('layouts.menus.admin')

@section('title', 'Edit Trayek')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 max-w-4xl">
        <h1 class="text-2xl font-black text-slate-900 mb-4">Edit Trayek</h1>
        <form action="{{ route('admin.trayek.update', $trayek->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PUT')
            <div>
                <label class="text-xs font-bold text-slate-500">Kode Trayek</label>
                <input name="kode_trayek" value="{{ $trayek->kode_trayek }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Nama Trayek</label>
                <input name="nama_trayek" value="{{ $trayek->nama_trayek }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Lat Asal</label>
                <input type="number" step="any" name="lat_asal" value="{{ $trayek->lat_asal }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Lng Asal</label>
                <input type="number" step="any" name="lng_asal" value="{{ $trayek->lng_asal }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Lat Tujuan</label>
                <input type="number" step="any" name="lat_tujuan" value="{{ $trayek->lat_tujuan }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Lng Tujuan</label>
                <input type="number" step="any" name="lng_tujuan" value="{{ $trayek->lng_tujuan }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Warna Angkot</label>
                <input type="color" name="warna_angkot" value="{{ $trayek->warna_angkot }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Harga</label>
                <input type="number" name="harga" value="{{ $trayek->harga }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div class="md:col-span-2">
                <label class="text-xs font-bold text-slate-500">Rute GeoJSON (optional)</label>
                <textarea name="rute_json" rows="3" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">{{ $trayek->rute_json }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="text-xs font-bold text-slate-500">Daftar Jalan (pisah koma)</label>
                <input name="daftar_jalan[]" value="{{ $trayek->daftar_jalan ? implode(',', $trayek->daftar_jalan) : '' }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
            </div>
            <div class="md:col-span-2 flex gap-2">
                <a href="{{ route('admin.trayek.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold text-slate-600">Batal</a>
                <button class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-bold">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
