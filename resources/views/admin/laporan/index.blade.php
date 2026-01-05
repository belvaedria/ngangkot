@extends('layouts.app_dashboard')
@include('layouts.menus.admin')

@section('title', 'Pusat Pengaduan')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10 space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-3xl font-black text-slate-900">Pusat Pengaduan.</h1>
        <p class="text-slate-500 text-sm mt-1">Menerima & Update Status Laporan</p>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 mt-0.5"></i>
            <p class="font-semibold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

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
        <div class="space-y-4">
            @foreach($laporans as $laporan)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ expanded: false }">
                
                {{-- Card Header --}}
                <div class="p-6 cursor-pointer" @click="expanded = !expanded">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-sm text-slate-400 font-medium">{{ $laporan->created_at->format('Y-m-d') }}</span>
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold uppercase">
                                    {{ $laporan->kategori ?? 'PENGEMUDI' }}
                                </span>
                            </div>
                            <h3 class="font-bold text-lg text-slate-900">{{ $laporan->judul }}</h3>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            {{-- Status Badge --}}
                            @if($laporan->status === 'pending' || $laporan->status === 'diproses')
                                <span class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl text-sm font-bold border border-blue-200">
                                    PROSES
                                </span>
                            @else
                                <span class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-sm font-bold border border-emerald-200">
                                    SELESAI
                                </span>
                            @endif
                            
                            {{-- Action Icons --}}
                            <div class="flex items-center gap-2">
                                <button class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center hover:bg-slate-200 transition">
                                    <i data-lucide="activity" class="w-5 h-5 text-slate-500"></i>
                                </button>
                                <button class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center hover:bg-emerald-200 transition">
                                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Expanded Content --}}
                <div x-show="expanded" x-collapse class="border-t border-slate-100">
                    <div class="p-6 space-y-4">
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
                                <p class="text-xs font-bold text-slate-500 uppercase mb-2">Tanggapan Admin</p>
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 mb-2 block">Status</label>
                                    <select name="status" 
                                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-200">
                                        <option value="pending" {{ $laporan->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="diproses" {{ $laporan->status === 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                        <option value="selesai" {{ $laporan->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-semibold text-slate-700 mb-2 block">Tanggapan Admin</label>
                                <textarea name="tanggapan" 
                                          rows="3"
                                          placeholder="Tulis tanggapan untuk pengguna..."
                                          class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-200 resize-none">{{ $laporan->tanggapan_admin }}</textarea>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                        class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition flex items-center gap-2">
                                    <i data-lucide="send" class="w-4 h-4"></i>
                                    Kirim Tanggapan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
