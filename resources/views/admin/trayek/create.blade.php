@extends('layouts.app_dashboard')
@include('layouts.menus.admin')

@section('title', 'Tambah Trayek')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 max-w-4xl">
        <h1 class="text-2xl font-black text-slate-900 mb-4">Tambah Trayek</h1>
        <form action="{{ route('admin.trayek.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-500">Kode Trayek</label>
                <input name="kode_trayek" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Nama Trayek</label>
                <input name="nama_trayek" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Lat Asal</label>
                <input type="number" step="any" name="lat_asal" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Lng Asal</label>
                <input type="number" step="any" name="lng_asal" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Lat Tujuan</label>
                <input type="number" step="any" name="lat_tujuan" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Lng Tujuan</label>
                <input type="number" step="any" name="lng_tujuan" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Warna Angkot</label>
                <input type="color" name="warna_angkot" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" value="#2563eb">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Harga</label>
                <input type="number" name="harga" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div class="md:col-span-2">
                <label class="text-xs font-bold text-slate-500">Rute GeoJSON (optional)</label>
                <textarea name="rute_json" rows="3" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" placeholder='{"type":"FeatureCollection",...}'></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="text-xs font-bold text-slate-500">Daftar Jalan (pisah koma)</label>
                <input name="daftar_jalan[]" placeholder="Jalan utama, Jalan kedua" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
            </div>
            <div class="md:col-span-2 flex gap-2">
                <a href="{{ route('admin.trayek.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold text-slate-600">Batal</a>
                <button class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
