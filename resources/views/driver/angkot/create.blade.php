@extends('layouts.app_dashboard')
@include('layouts.menus.driver')

@section('title', 'Tambah Armada')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
    <div class="max-w-xl bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
        <h1 class="text-2xl font-black text-slate-900 mb-4">Tambah Angkot</h1>
        <form action="{{ route('driver.angkot.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-500">Plat Nomor</label>
                <input name="plat_nomor" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Trayek</label>
                <select name="trayek_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
                    @foreach($trayeks as $trayek)
                        <option value="{{ $trayek->id }}">{{ $trayek->nama_trayek }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('driver.angkot.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold text-slate-600">Batal</a>
                <button class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
