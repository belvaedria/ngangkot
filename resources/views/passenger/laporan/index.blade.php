@extends('layouts.app_dashboard')
@include('layouts.menus.passenger')

@section('title', 'Laporan Saya - Ngangkot')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
<div class="bg-slate-50 min-h-screen py-12 px-6">
    <div class="max-w-6xl mx-auto">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Laporan Saya</h1>
                <p class="text-slate-600">Kirim dan pantau laporan terkait layanan angkot di Bandung</p>
            </div>
            <a href="{{ route('passenger.laporan.create') }}"
               class="mt-4 md:mt-0 inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Buat Laporan Baru
            </a>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-rose-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            @php
                $pending = $laporans->where('status', 'pending')->count();
                $diproses = $laporans->where('status', 'diproses')->count();
                $selesai = $laporans->where('status', 'selesai')->count();
            @endphp

            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900">{{ $pending }}</p>
                        <p class="text-sm text-slate-500">Menunggu</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="loader" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900">{{ $diproses }}</p>
                        <p class="text-sm text-slate-500">Diproses</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900">{{ $selesai }}</p>
                        <p class="text-sm text-slate-500">Selesai</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Laporan List --}}
        @if($laporans->isEmpty())
            <div class="bg-white rounded-3xl p-12 border border-slate-100 shadow-sm text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="file-text" class="w-10 h-10 text-slate-400"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Laporan</h3>
                <p class="text-slate-500 mb-6">Anda belum membuat laporan apapun. Mulai buat laporan untuk membantu meningkatkan layanan angkot.</p>
                <a href="{{ route('passenger.laporan.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    Buat Laporan Pertama
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($laporans as $laporan)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                {{-- Content --}}
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        {{-- Status Badge --}}
                                        @if($laporan->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Menunggu
                                            </span>
                                        @elseif($laporan->status === 'diproses')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                Sedang Diproses
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Selesai
                                            </span>
                                        @endif

                                        <span class="text-xs text-slate-400">
                                            {{ $laporan->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>

                                    <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $laporan->judul }}</h3>
                                    <p class="text-slate-600 text-sm line-clamp-2">{{ $laporan->isi }}</p>

                                    {{-- Tanggapan Admin --}}
                                    @if($laporan->tanggapan_admin)
                                        <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                            <p class="text-xs font-bold text-blue-600 mb-1">Tanggapan Admin:</p>
                                            <p class="text-sm text-blue-800">{{ $laporan->tanggapan_admin }}</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Bukti Foto --}}
                                @if($laporan->bukti_foto)
                                    <div class="shrink-0">
                                        <img src="{{ Storage::url($laporan->bukti_foto) }}" 
                                             alt="Bukti foto"
                                             class="w-24 h-24 md:w-32 md:h-32 object-cover rounded-xl border border-slate-200">
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-100">
                                <a href="{{ route('passenger.laporan.show', $laporan) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    Lihat Detail
                                </a>

                                @if($laporan->status === 'pending')
                                    <a href="{{ route('passenger.laporan.edit', $laporan) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                        Edit
                                    </a>

                                    <form action="{{ route('passenger.laporan.destroy', $laporan) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endpush
@endsection
