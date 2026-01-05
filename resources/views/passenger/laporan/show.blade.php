@extends('layouts.app_dashboard')
@include('layouts.menus.passenger')

@section('title', 'Detail Laporan - Ngangkot')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
<div class="bg-slate-50 min-h-screen py-12 px-6">
    <div class="max-w-3xl mx-auto">

        {{-- Back Button --}}
        <a href="{{ route('passenger.laporan.index') }}"
           class="inline-flex items-center gap-2 text-slate-600 hover:text-blue-600 font-semibold mb-6 transition">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            Kembali ke Daftar Laporan
        </a>

        {{-- Main Card --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            
            {{-- Header --}}
            <div class="p-8 border-b border-slate-100">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        {{-- Status Badge --}}
                        @if($laporan->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 mb-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu Ditinjau
                            </span>
                        @elseif($laporan->status === 'diproses')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 mb-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                Sedang Diproses
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 mb-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Selesai
                            </span>
                        @endif

                        <h1 class="text-2xl font-extrabold text-slate-900">{{ $laporan->judul }}</h1>
                        <p class="text-slate-500 text-sm mt-2">
                            Dilaporkan pada {{ $laporan->created_at->format('d F Y, H:i') }} WIB
                        </p>
                    </div>

                    {{-- Action Buttons --}}
                    @if($laporan->status === 'pending')
                        <div class="flex items-center gap-2">
                            <a href="{{ route('passenger.laporan.edit', $laporan) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold bg-amber-100 text-amber-700 hover:bg-amber-200 rounded-xl transition">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                Edit
                            </a>
                            <form action="{{ route('passenger.laporan.destroy', $laporan) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold bg-rose-100 text-rose-700 hover:bg-rose-200 rounded-xl transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Content --}}
            <div class="p-8">
                {{-- Progress Timeline --}}
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-slate-700 mb-4">Status Laporan</h3>
                    <div class="flex items-center gap-4">
                        {{-- Step 1: Pending --}}
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ in_array($laporan->status, ['pending', 'diproses', 'selesai']) ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-400' }}">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-semibold {{ in_array($laporan->status, ['pending', 'diproses', 'selesai']) ? 'text-slate-900' : 'text-slate-400' }}">Dikirim</span>
                        </div>

                        <div class="flex-1 h-1 rounded {{ in_array($laporan->status, ['diproses', 'selesai']) ? 'bg-blue-600' : 'bg-slate-200' }}"></div>

                        {{-- Step 2: Diproses --}}
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ in_array($laporan->status, ['diproses', 'selesai']) ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-400' }}">
                                <i data-lucide="loader" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-semibold {{ in_array($laporan->status, ['diproses', 'selesai']) ? 'text-slate-900' : 'text-slate-400' }}">Diproses</span>
                        </div>

                        <div class="flex-1 h-1 rounded {{ $laporan->status === 'selesai' ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>

                        {{-- Step 3: Selesai --}}
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $laporan->status === 'selesai' ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-400' }}">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-semibold {{ $laporan->status === 'selesai' ? 'text-emerald-600' : 'text-slate-400' }}">Selesai</span>
                        </div>
                    </div>
                </div>

                {{-- Isi Laporan --}}
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-slate-700 mb-3">Isi Laporan</h3>
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                        <p class="text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $laporan->isi }}</p>
                    </div>
                </div>

                {{-- Bukti Foto --}}
                @if($laporan->bukti_foto)
                    <div class="mb-8">
                        <h3 class="text-sm font-bold text-slate-700 mb-3">Bukti Foto</h3>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 inline-block">
                            <a href="{{ Storage::url($laporan->bukti_foto) }}" target="_blank">
                                <img src="{{ Storage::url($laporan->bukti_foto) }}" 
                                     alt="Bukti foto laporan"
                                     class="max-w-full max-h-80 rounded-xl border border-slate-200 hover:opacity-90 transition">
                            </a>
                            <p class="text-xs text-slate-400 mt-2">Klik gambar untuk melihat ukuran penuh</p>
                        </div>
                    </div>
                @endif

                {{-- Tanggapan Admin --}}
                @if($laporan->tanggapan_admin)
                    <div class="mt-8 pt-8 border-t border-slate-100">
                        <h3 class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                            <i data-lucide="message-circle" class="w-4 h-4 text-blue-600"></i>
                            Tanggapan dari Admin
                        </h3>
                        <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                            <p class="text-blue-800 whitespace-pre-wrap leading-relaxed">{{ $laporan->tanggapan_admin }}</p>
                            @if($laporan->updated_at != $laporan->created_at)
                                <p class="text-xs text-blue-400 mt-3">
                                    Dibalas pada {{ $laporan->updated_at->format('d F Y, H:i') }} WIB
                                </p>
                            @endif
                        </div>
                    </div>
                @elseif($laporan->status === 'pending')
                    <div class="mt-8 pt-8 border-t border-slate-100">
                        <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100 flex items-start gap-4">
                            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                                <i data-lucide="clock" class="w-5 h-5 text-amber-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-900 mb-1">Menunggu Peninjauan</h4>
                                <p class="text-sm text-amber-700">Laporan Anda sedang dalam antrian untuk ditinjau oleh admin. Kami akan memberikan tanggapan secepatnya.</p>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

        </div>

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
