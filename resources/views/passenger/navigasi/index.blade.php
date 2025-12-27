@extends('layouts.app_dashboard')

@section('title', 'Navigasi - Ngangkot')

@section('content')
<!-- Container Fullscreen dengan Negative Margin agar Peta memenuhi layar di balik Navbar Glass -->
<div class="absolute inset-0 w-full h-full bg-slate-50 overflow-hidden -z-0">
    
    <!-- 1. PETA BACKGROUND -->
    <div id="map" class="absolute inset-0 w-full h-full z-0"></div>
    
    <!-- Overlay Gradient agar teks di atas peta terbaca -->
    <div class="absolute inset-0 bg-gradient-to-b from-white/90 via-transparent to-transparent pointer-events-none h-48 z-0"></div>

    <!-- 2. SEARCH PANEL (Floating) -->
    <div class="absolute top-28 left-0 right-0 z-10 px-4 flex justify-center pointer-events-none">
        <div class="w-full max-w-lg bg-white/80 backdrop-blur-xl p-6 rounded-[2rem] shadow-2xl shadow-blue-900/10 border border-white/60 pointer-events-auto animate-in fade-in slide-in-from-top-5 duration-700">
            
            <!-- Sapaan -->
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">
                        Halo, {{ Auth::check() ? strtok(Auth::user()->name, ' ') : 'Wargi' }}! 👋
                    </h1>
                    <p class="text-xs font-bold text-slate-500 mt-0.5" id="gps-status">Mendeteksi lokasi...</p>
                </div>
                <div class="bg-blue-50 p-2 rounded-full text-blue-600">
                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- FORM PENCARIAN RUTE (Ke NavigasiController) -->
            <form action="{{ route('navigasi.search') }}" method="POST" class="space-y-3 relative">
                @csrf
                
                <!-- Garis Visual -->
                <div class="absolute left-[1.35rem] top-10 bottom-10 w-0.5 border-l-2 border-dashed border-slate-300 z-0"></div>

                <!-- Input Asal -->
                <div class="relative group z-10">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 bg-white p-1 rounded-full shadow-sm">
                        <div class="w-2.5 h-2.5 bg-blue-500 rounded-full"></div>
                    </div>
                    <!-- Input Tampilan (Readonly karena auto GPS) -->
                    <input type="text" id="display_asal" placeholder="Sedang mencari lokasi..." readonly
                           value="{{ $prefill['nama_asal'] ?? '' }}"
                           class="w-full pl-10 pr-4 py-3 bg-blue-50/50 border border-blue-100 rounded-xl outline-none text-sm font-bold text-slate-700 placeholder:text-slate-400 cursor-not-allowed">
                    
                    <!-- Hidden Input Data Sebenarnya -->
                    <input type="hidden" id="lat_asal" name="lat_asal" value="{{ $prefill['lat_asal'] ?? '' }}">
                    <input type="hidden" id="lng_asal" name="lng_asal" value="{{ $prefill['lng_asal'] ?? '' }}">
                    <input type="hidden" id="nama_asal" name="nama_asal" value="{{ $prefill['nama_asal'] ?? '' }}">
                </div>

                <!-- Input Tujuan -->
                <div class="relative group z-10">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 bg-white p-1 rounded-full shadow-sm">
                        <i data-lucide="map-pin" class="w-3 h-3 text-rose-500"></i>
                    </div>
                    <input type="text" id="input_tujuan" name="nama_tujuan" placeholder="Mau kemana? (Misal: Stasiun Hall)" required
                           value="{{ $prefill['nama_tujuan'] ?? '' }}"
                           class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-rose-100 focus:border-rose-300 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400">

                    <!-- Dropdown suggestion (hidden until needed) -->
                    <div id="places_dropdown" class="absolute z-30 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-auto hidden"></div>

                    <!-- Small hint for provider source -->
                    <div id="place_source_label" class="text-xs text-slate-400 mt-1 hidden">Sumber: <span id="place_source_name" class="font-bold"></span></div>
                    <div class="text-xs text-slate-300 mt-1">Preferensi provider: <span class="font-bold">Mapbox</span> → <span class="font-bold">LocationIQ</span> → <span class="font-bold">Nominatim</span>. Untuk hasil terbaik, atur <code>MAPBOX_ACCESS_TOKEN</code> di file <code>.env</code>.</div>

                    <!-- Hidden inputs untuk menyimpan koordinat tujuan -->
                    <input type="hidden" id="lat_tujuan" name="lat_tujuan" value="{{ $prefill['lat_tujuan'] ?? '' }}">
                    <input type="hidden" id="lng_tujuan" name="lng_tujuan" value="{{ $prefill['lng_tujuan'] ?? '' }}">
                    <input type="hidden" id="place_source" name="place_source">
                </div>

                <button type="submit" id="btn-cari" disabled
                        class="w-full mt-2 py-3 bg-slate-900 text-white rounded-xl font-bold text-sm shadow-xl hover:bg-slate-800 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    Cari Rute <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- 3. PANEL TRAYEK SEKITAR (Diambil dari Database) -->
    <div class="absolute bottom-24 left-0 w-full z-10 px-4 pointer-events-none md:left-10 md:w-[450px]">
        <div class="pointer-events-auto">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 bg-white/80 backdrop-blur w-fit px-3 py-1 rounded-full border border-white/50 shadow-sm">
                Trayek Aktif
            </h3>
            
            <div class="flex gap-3 overflow-x-auto pb-4 custom-scroll snap-x">
                @forelse($trayeks as $trayek)
                <a href="{{ route('trayek.show', $trayek->kode_trayek) }}" class="snap-start min-w-[220px] bg-white/90 backdrop-blur-xl p-4 rounded-[2rem] border border-white/60 shadow-lg hover:-translate-y-1 transition-all cursor-pointer group">
                    <div class="flex justify-between mb-3">
                        <span class="text-[10px] font-black tracking-widest text-slate-400 uppercase">{{ $trayek->kode_trayek }}</span>
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    </div>
                    <h4 class="font-black text-sm text-slate-900 mb-1 truncate">{{ $trayek->nama_trayek }}</h4>
                    <div class="flex items-center gap-2 text-[10px] font-bold text-slate-500">
                        <i data-lucide="banknote" class="w-3 h-3"></i> Rp {{ number_format($trayek->harga) }}
                    </div>
                </a>
                @empty
                <div class="p-4 bg-white/80 rounded-2xl text-xs font-bold text-slate-400">
                    Tidak ada data trayek.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- same scripts as before --}} 
@endpush
@endsection