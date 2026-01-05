@extends('layouts.app_dashboard')
@include('layouts.menus.admin')

@section('title', 'Kelola Trayek')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900">Kelola Data Trayek.</h1>
        </div>
        <a href="{{ route('admin.trayek.create') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-blue-600 text-white font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Tambah Trayek Baru
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 mt-0.5"></i>
            <p class="font-semibold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Kode</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Trayek</th>
                    <th class="text-right px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($trayeks as $trayek)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-5">
                        <span class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-xl text-sm font-bold">
                            {{ $trayek->kode_trayek }}
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        <p class="font-bold text-slate-900">{{ $trayek->nama_trayek }}</p>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.trayek.edit', $trayek->id) }}" 
                               class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center hover:bg-blue-700 transition">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.trayek.destroy', $trayek->id) }}" method="POST" 
                                  x-data="{ showConfirm: false }"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        @click="showConfirm = true; setTimeout(() => { if(confirm('Yakin hapus trayek {{ $trayek->kode_trayek }}?')) $el.closest('form').submit(); showConfirm = false; }, 100)"
                                        class="w-10 h-10 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center hover:bg-rose-200 transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="signpost" class="w-8 h-8 text-slate-400"></i>
                        </div>
                        <p class="text-slate-500">Belum ada data trayek</p>
                        <a href="{{ route('admin.trayek.create') }}" class="text-blue-600 font-bold text-sm mt-2 inline-block hover:underline">
                            + Tambah trayek pertama
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
