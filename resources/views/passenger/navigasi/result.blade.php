@extends('layouts.app_dashboard')
@section('title', 'Rute Terbaik - Ngangkot')

@section('content')
<div class="relative h-full flex flex-col bg-slate-50 overflow-hidden min-h-screen">
    
    <!-- 1. PETA BACKGROUND -->
    <div id="map" class="absolute inset-0 z-0 h-full w-full"></div>

    <!-- 2. TOMBOL KEMBALI (Floating) -->
    <!-- Kembali ke halaman pencarian navigasi -->
    <a href="{{ route('navigasi.index') }}" class="absolute top-24 left-6 z-20 bg-white/90 backdrop-blur-md p-3.5 rounded-2xl shadow-xl border border-white/50 text-slate-700 hover:bg-white hover:text-blue-600 hover:scale-105 transition-all">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
    </a>

    <!-- 3. PANEL HASIL (Bottom Sheet Accordion) -->
    <div class="absolute bottom-0 left-0 w-full z-10 flex flex-col max-h-[70vh] pointer-events-none">
        
        <div class="mx-auto w-full max-w-xl bg-white/95 backdrop-blur-2xl rounded-t-[3rem] shadow-[0_-10px_60px_-15px_rgba(0,0,0,0.15)] border-t border-white/60 pointer-events-auto flex flex-col h-full animate-in slide-in-from-bottom duration-500">
            
            <!-- Handle (Pegangan visual untuk draggable sheet) -->
            <div class="w-full flex justify-center pt-5 pb-2 cursor-grab">
                <div class="w-16 h-1.5 bg-slate-200 rounded-full"></div>
            </div>

            <!-- Header Panel -->
            <div class="px-8 pb-5 pt-2 flex justify-between items-end border-b border-slate-50">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight leading-none mb-1">Rute Tersedia</h2>
                    <p class="text-sm font-bold text-slate-400">Ditemukan {{ count($trayeks) }} opsi perjalanan.</p>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-100 mb-1">Tercepat</span>
                </div>
            </div>

            <!-- List Routes (Scrollable) -->
            <div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-4 pb-32">
                
                @forelse($trayeks as $index => $trayek)
                @php $isVariant = is_array($trayek) || (is_object($trayek) && isset($trayek['variant'])); @endphp
                <!-- CARD UTAMA (Looping Data Trayek / Variant) -->
                <div x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }" 
                     class="group bg-white rounded-[2.5rem] border border-slate-100 shadow-sm transition-all duration-500 overflow-hidden"
                     :class="open ? 'ring-2 ring-blue-600 shadow-2xl shadow-blue-200/50 scale-[1.02]' : 'hover:shadow-lg hover:-translate-y-1'">
                    
                    <div @click="open = !open; showRouteOnMap({{ $index }})" class="p-6 cursor-pointer relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-slate-50 to-transparent rounded-bl-[4rem] -mr-8 -mt-8 opacity-50"></div>

                        <div class="flex items-center gap-5 relative z-10">
                            <div class="w-16 h-16 rounded-[1.2rem] flex flex-col items-center justify-center text-white shadow-xl shadow-blue-500/20 transition-transform duration-500 group-hover:rotate-3"
                                 style="background-color: {{ $isVariant ? '#6b7280' : ($trayek->warna_angkot ?? '#6366f1') }}">
                                <i data-lucide="bus" class="w-5 h-5 mb-1 opacity-75"></i>
                                <span class="font-black text-sm tracking-widest">{{ $isVariant ? strtoupper(substr($trayek['variant'] ?? 'VAR',0,3)) : substr($trayek->kode_trayek, 0, 3) }}</span>
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="font-black text-slate-900 text-lg leading-tight mb-2">{{ $isVariant ? ucfirst(str_replace('_',' ', $trayek['variant'])) : $trayek->nama_trayek }}</h3>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-[10px] font-bold text-slate-500 bg-slate-50 border border-slate-100 px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                                        <i data-lucide="clock" class="w-3 h-3 text-blue-500"></i> {{ $isVariant ? ($trayek['result']['dist'] ? ceil($trayek['result']['dist']).' min' : 'Estimasi') : ($trayek->info_waktu ?? '') }}
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-500 bg-slate-50 border border-slate-100 px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                                        <i data-lucide="map" class="w-3 h-3 text-emerald-500"></i> {{ $isVariant ? ($trayek['distance_est'].' m' ?? '') : ($trayek->info_jarak ?? '') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-50 flex justify-between items-center relative z-10">
                            <span class="font-black text-blue-600 text-lg tracking-tight">{{ $isVariant ? 'Estimasi' : ($trayek->info_tarif ?? 'Rp -') }}</span>
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 group-hover:text-blue-500 transition-colors">
                                <span x-text="open ? 'Tutup Detail' : 'Lihat Rute'"></span>
                                <div class="w-6 h-6 rounded-full bg-slate-50 flex items-center justify-center transition-transform duration-300"
                                     :class="open ? 'rotate-180 bg-blue-50 text-blue-600' : 'text-slate-300'">
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="open" x-collapse class="bg-slate-50/50 border-t border-slate-100 p-6">
                        <div class="flex items-start gap-4 p-4 bg-white rounded-2xl border border-blue-100 shadow-sm mb-8 relative overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shrink-0">
                                <i data-lucide="rss" class="w-5 h-5 animate-pulse"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Info Armada</p>
                                <p class="text-sm font-bold text-slate-800 leading-snug">{{ $isVariant ? 'Rute hasil kombinasi. Lihat langkah-langkah.' : ($trayek->info_angkot ?? '') }}</p>
                            </div>
                        </div>

                        <div class="relative pl-4 space-y-8 ml-2">
                            <div class="absolute left-[19px] top-4 bottom-8 w-0.5 border-l-2 border-dashed border-slate-300"></div>

                            @if($isVariant)
                                @foreach($trayek['result']['path'] as $stepEntry)
                                    @php $edge = $stepEntry['edge'] ?? null; @endphp
                                    <div class="relative flex gap-5 group/step">
                                        <div class="relative z-10 w-10 h-10 rounded-full border-[3px] border-white shadow-md flex items-center justify-center transition-transform group-hover/step:scale-110 bg-slate-200 text-slate-500">
                                            @if(!$edge || $edge['type'] === 'walk') <i data-lucide="footprints" class="w-4 h-4"></i>
                                            @else <i data-lucide="bus" class="w-4 h-4"></i> @endif
                                        </div>
                                        <div class="flex-1 pt-0.5">
                                            <p class="text-sm font-black text-slate-800">{{ $edge ? ucfirst($edge['type']) : 'Start' }}</p>
                                            <p class="text-xs font-medium text-slate-400 mt-1 leading-relaxed">{{ $edge ? ($edge['type']==='walk' ? round($edge['weight']).' min jalan' : 'Angkot: '.$edge['trayek_name'] ?? '') : '' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @foreach($trayek->rute_detail as $step)
                                    <div class="relative flex gap-5 group/step">
                                        <div class="relative z-10 w-10 h-10 rounded-full border-[3px] border-white shadow-md flex items-center justify-center transition-transform group-hover/step:scale-110
                                            {{ $step['jenis'] == 'angkot' ? 'text-white' : 'bg-slate-200 text-slate-500' }}"
                                            style="{{ $step['jenis'] == 'angkot' ? 'background-color:'.$step['warna'] : '' }}">
                                            @if($step['jenis'] == 'jalan') <i data-lucide="footprints" class="w-4 h-4"></i>
                                            @elseif($step['jenis'] == 'angkot') <i data-lucide="bus" class="w-4 h-4"></i>
                                            @else <i data-lucide="map-pin" class="w-4 h-4"></i> @endif
                                        </div>
                                        
                                        <div class="flex-1 pt-0.5">
                                            <p class="text-sm font-black text-slate-800">{{ $step['instruksi'] }}</p>
                                            <p class="text-xs font-medium text-slate-400 mt-1 leading-relaxed">{{ $step['detail'] ?? '' }}</p>
                                            
                                            @if(isset($step['waktu']))
                                            <div class="mt-2 inline-flex items-center gap-1.5 text-[10px] font-bold text-slate-500 bg-white border border-slate-100 px-2.5 py-1 rounded-lg shadow-sm">
                                                <i data-lucide="timer" class="w-3 h-3 text-slate-400"></i> {{ $step['waktu'] }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <button class="w-full mt-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-sm shadow-xl shadow-slate-900/20 hover:bg-slate-800 hover:-translate-y-1 transition-all flex items-center justify-center gap-2 group/btn">
                            <i data-lucide="navigation" class="w-4 h-4 group-hover/btn:rotate-45 transition-transform"></i> Mulai Jalan
                        </button>
                    </div>
                </div>
                @empty
                <div class="text-center py-20 opacity-50 flex flex-col items-center">
                    <div class="w-20 h-20 bg-slate-100 rounded-[2rem] flex items-center justify-center mb-4">
                        <i data-lucide="map-off" class="w-8 h-8 text-slate-300"></i>
                    </div>
                    <p class="text-slate-900 font-black">{{ $message ?? 'Rute tidak ditemukan' }}</p>
                    <p class="text-slate-400 text-xs mt-1">{{ $message ? 'Silakan coba titik lokasi lain atau periksa trayek terdekat.' : 'Coba cari lokasi yang lebih umum atau dekat jalan utama.' }}</p>
                    <a href="{{ route('navigasi.index') }}" class="mt-4 text-blue-600 text-xs font-bold hover:underline">Cari Ulang</a>
                </div>
                @endforelse

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Build ruteData only for trayeks that have rute_json (legacy Trayek objects)
    const ruteData = [];
    @foreach($trayeks as $t)
        @if(is_object($t) && isset($t->rute_json) && $t->rute_json)
            ruteData.push({ id: {{ $loop->index }}, color: '{{ $t->warna_angkot }}', json: {!! $t->rute_json !!} });
        @else
            // placeholder for variant index {{ $loop->index }} -> no geojson
            ruteData.push(null);
        @endif
    @endforeach

    let currentLayer = null;

    function showRouteOnMap(index) {
        if (currentLayer) map.removeLayer(currentLayer);
        const data = ruteData[index];
        if (!data || !data.json) return; // can't draw variant without geojson
        currentLayer = L.geoJSON(data.json, {
            style: { color: data.color, weight: 8, opacity: 0.9, lineCap: 'round', lineJoin: 'round' }
        }).addTo(map);
        map.fitBounds(currentLayer.getBounds(), { paddingBottomRight: [0, 400], paddingTopLeft: [0, 100] });
    }

    // Init map if there is at least one geojson route
    document.addEventListener('DOMContentLoaded', function() {
        var hasGeo = ruteData.some(d => d && d.json);
        if (hasGeo) {
            // lazily create L.map('map') if it doesn't exist
            if (!window._ngangkot_map) {
                window._ngangkot_map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-6.917464, 107.619122], 13);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(window._ngangkot_map);
                window._ngangkot_map.invalidateSize();
                window.map = window._ngangkot_map; // compatibility
            }
            // show first geo route
            const firstIndex = ruteData.findIndex(d => d && d.json);
            if (firstIndex >= 0) showRouteOnMap(firstIndex);
        }
    });
</script>
@endpush
@endsection