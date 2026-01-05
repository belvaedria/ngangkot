@extends('layouts.app_dashboard')
@include('layouts.menus.admin')

@section('title', 'Verifikasi Driver')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10 space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-3xl font-black text-slate-900">Verifikasi Driver.</h1>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 mt-0.5"></i>
            <p class="font-semibold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Driver Cards --}}
    <div class="space-y-4">
        @forelse($drivers as $driver)
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    {{-- Avatar --}}
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl flex items-center justify-center">
                        <i data-lucide="user" class="w-7 h-7 text-blue-400"></i>
                    </div>
                    {{-- Info --}}
                    <div>
                        <h3 class="font-bold text-lg text-slate-900">{{ $driver->nama_unit ?? 'Unit ' . $driver->user->name }}</h3>
                        <p class="text-sm text-slate-400 uppercase tracking-wide font-medium">
                            SIM {{ $driver->status_sim ?? 'AKTIF' }} • TRAYEK {{ $driver->trayek->kode_trayek ?? '-' }}
                        </p>
                    </div>
                </div>
                
                {{-- Action Buttons --}}
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.verifikasi.approve', $driver->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-6 py-2.5 bg-emerald-500 text-white rounded-xl font-bold text-sm hover:bg-emerald-600 transition shadow-lg shadow-emerald-200">
                            Terima
                        </button>
                    </form>
                    <form action="{{ route('admin.verifikasi.reject', $driver->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-6 py-2.5 bg-rose-100 text-rose-600 rounded-xl font-bold text-sm hover:bg-rose-200 transition">
                            Tolak
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-12 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="check-circle" class="w-10 h-10 text-emerald-500"></i>
            </div>
            <h3 class="font-bold text-slate-900 text-lg mb-1">Semua Terverifikasi!</h3>
            <p class="text-slate-500">Tidak ada driver yang menunggu verifikasi</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
