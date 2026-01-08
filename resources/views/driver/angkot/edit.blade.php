@extends('layouts.app_dashboard')
@include('layouts.menus.driver')

@section('title', 'Ubah Armada')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
    <div class="max-w-xl bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
        <h1 class="text-2xl font-black text-slate-900 mb-4">Ubah Angkot</h1>
        <form action="{{ route('driver.angkot.update', $angkot) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="text-xs font-bold text-slate-500">Plat Nomor</label>
                <input name="plat_nomor" value="{{ $angkot->plat_nomor }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500">Trayek</label>
                <select name="trayek_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm" required>
                    @foreach($trayeks as $trayek)
                        <option value="{{ $trayek->id }}" @selected($trayek->id == $angkot->trayek_id)>{{ $trayek->nama_trayek }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-200">
    <div>
        <p class="text-xs font-bold text-slate-500 uppercase">Status</p>
        <p class="text-sm font-bold text-slate-900">Aktifkan angkot</p>
    </div>

    {{-- penting: checkbox butuh hidden biar kalau unchecked tetap ngirim 0 --}}
    <input type="hidden" name="is_active" value="0">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" class="w-5 h-5"
                @checked(old('is_active', $angkot->is_active))>
            <span class="text-sm font-semibold text-slate-700">
                {{ $angkot->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </label>
    </div>

            <div class="flex gap-2">
                <a href="{{ route('driver.angkot.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold text-slate-600">Batal</a>
                <button class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-bold">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
