@extends('layouts.app_dashboard')
@include('layouts.menus.admin')

@section('title', 'Kelola Laporan Pengguna')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Laporan Pengguna</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola dan tanggapi laporan dari pengguna Ngangkot</p>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 mt-0.5"></i>
            <div>
                <p class="font-semibold text-emerald-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @php
            $pending = $laporans->where('status', 'pending')->count();
            $diproses = $laporans->where('status', 'diproses')->count();
            $selesai = $laporans->where('status', 'selesai')->count();
            $total = $laporans->count();
        @endphp

        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="inbox" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $total }}</p>
                    <p class="text-xs text-slate-500">Total Laporan</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $pending }}</p>
                    <p class="text-xs text-slate-500">Menunggu</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="loader" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $diproses }}</p>
                    <p class="text-xs text-slate-500">Diproses</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $selesai }}</p>
                    <p class="text-xs text-slate-500">Selesai</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2" x-data="{ filter: 'all' }">
        <button @click="filter = 'all'" 
                :class="filter === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition">
            Semua
        </button>
        <button @click="filter = 'pending'" 
                :class="filter === 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition">
            Menunggu ({{ $pending }})
        </button>
        <button @click="filter = 'diproses'" 
                :class="filter === 'diproses' ? 'bg-blue-500 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition">
            Diproses ({{ $diproses }})
        </button>
        <button @click="filter = 'selesai'" 
                :class="filter === 'selesai' ? 'bg-emerald-500 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition">
            Selesai ({{ $selesai }})
        </button>
    </div>

    {{-- Laporan List --}}
    @if($laporans->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Laporan</h3>
            <p class="text-slate-500">Belum ada laporan dari pengguna.</p>
        </div>
    @else
        <div class="space-y-4" x-data="{ filter: 'all' }">
            @foreach($laporans as $laporan)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
                     x-show="filter === 'all' || filter === '{{ $laporan->status }}'"
                     x-data="{ expanded: false }">
                    
                    {{-- Header --}}
                    <div class="p-5 cursor-pointer" @click="expanded = !expanded">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    {{-- Status Badge --}}
                                    @if($laporan->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Menunggu
                                        </span>
                                    @elseif($laporan->status === 'diproses')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                            Diproses
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Selesai
                                        </span>
                                    @endif

                                    <span class="text-xs text-slate-400">
                                        {{ $laporan->created_at->format('d M Y, H:i') }}
                                    </span>
                                </div>

                                <h3 class="text-base font-bold text-slate-900">{{ $laporan->judul }}</h3>
                                <p class="text-sm text-slate-500 mt-1">
                                    Dilaporkan oleh: <span class="font-semibold text-slate-700">{{ $laporan->user->name ?? 'Unknown' }}</span>
                                    ({{ $laporan->user->email ?? '' }})
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($laporan->bukti_foto)
                                    <div class="w-12 h-12 rounded-lg overflow-hidden border border-slate-200">
                                        <img src="{{ Storage::url($laporan->bukti_foto) }}" 
                                             alt="Bukti"
                                             class="w-full h-full object-cover">
                                    </div>
                                @endif
                                <button class="p-2 hover:bg-slate-100 rounded-lg transition">
                                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform" :class="expanded && 'rotate-180'"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Expanded Content --}}
                    <div x-show="expanded" x-collapse class="border-t border-slate-100">
                        <div class="p-5 space-y-4">
                            {{-- Isi Laporan --}}
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase mb-2">Isi Laporan</p>
                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                    <p class="text-slate-700 text-sm whitespace-pre-wrap">{{ $laporan->isi }}</p>
                                </div>
                            </div>

                            {{-- Bukti Foto --}}
                            @if($laporan->bukti_foto)
                                <div>
                                    <p class="text-xs font-bold text-slate-500 uppercase mb-2">Bukti Foto</p>
                                    <a href="{{ Storage::url($laporan->bukti_foto) }}" target="_blank">
                                        <img src="{{ Storage::url($laporan->bukti_foto) }}" 
                                             alt="Bukti foto"
                                             class="max-h-48 rounded-xl border border-slate-200 hover:opacity-90 transition">
                                    </a>
                                </div>
                            @endif

                            {{-- Tanggapan Sebelumnya --}}
                            @if($laporan->tanggapan_admin)
                                <div>
                                    <p class="text-xs font-bold text-slate-500 uppercase mb-2">Tanggapan Sebelumnya</p>
                                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                        <p class="text-blue-800 text-sm whitespace-pre-wrap">{{ $laporan->tanggapan_admin }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Form Tanggapan --}}
                            <form action="{{ route('admin.laporan.update', $laporan) }}" method="POST" class="pt-4 border-t border-slate-100">
                                @csrf
                                @method('PUT')

                                <p class="text-xs font-bold text-slate-500 uppercase mb-3">Berikan Tanggapan</p>

                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-semibold text-slate-700 mb-2 block">Status</label>
                                        <select name="status" 
                                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-200">
                                            <option value="pending" {{ $laporan->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                            <option value="diproses" {{ $laporan->status === 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                            <option value="selesai" {{ $laporan->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-sm font-semibold text-slate-700 mb-2 block">Tanggapan Admin</label>
                                        <textarea name="tanggapan" 
                                                  rows="3"
                                                  placeholder="Tulis tanggapan untuk pengguna..."
                                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-200 resize-none">{{ $laporan->tanggapan_admin }}</textarea>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit"
                                                class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition flex items-center gap-2">
                                            <i data-lucide="send" class="w-4 h-4"></i>
                                            Kirim Tanggapan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
