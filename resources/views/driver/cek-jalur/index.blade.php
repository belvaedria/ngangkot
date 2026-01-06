@extends('layouts.app_dashboard')
@include('layouts.menus.driver')

@section('title', 'Cek Jalur Tugas')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Left: List Trayek -->
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h1 class="text-2xl font-black text-slate-900">Cek Jalur Tugas.</h1>
                    <p class="text-sm text-slate-500">Informasi rute angkot ter-update Kota Bandung.</p>
                </div>
            </div>
            
            <!-- Search -->
            <div class="mt-4 mb-6">
                <form action="{{ route('driver.cekjalur.index') }}" method="GET">
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari rute, kode, atau wilayah..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </form>
            </div>
            
            <!-- Trayek List -->
            <div class="space-y-3 max-h-[500px] overflow-y-auto custom-scroll">
                @forelse($trayeks as $trayek)
                <div class="trayek-item p-4 rounded-2xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all"
                     data-kode="{{ $trayek->kode_trayek }}"
                     data-nama="{{ $trayek->nama_trayek }}"
                     data-rute="{{ $trayek->rute_json }}"
                     data-warna="{{ $trayek->warna_angkot }}"
                     data-lat-asal="{{ $trayek->lat_asal }}"
                     data-lng-asal="{{ $trayek->lng_asal }}"
                     data-lat-tujuan="{{ $trayek->lat_tujuan }}"
                     data-lng-tujuan="{{ $trayek->lng_tujuan }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-lg font-black text-blue-600">{{ $trayek->kode_trayek }}</p>
                            <div class="flex items-center gap-2 text-sm text-slate-500 mt-1">
                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                <span>{{ $trayek->nama_trayek }}</span>
                            </div>
                        </div>
                        <button class="text-slate-300 hover:text-yellow-400 transition-colors">
                            <i data-lucide="star" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i data-lucide="map-pin-off" class="w-12 h-12 mx-auto text-slate-300 mb-3"></i>
                    <p class="text-slate-500">Tidak ada trayek ditemukan</p>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Right: Map Visualization -->
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 relative overflow-hidden">
            <div id="map" class="w-full h-[600px] rounded-2xl"></div>
            
            <!-- Live Map Label -->
            <div class="absolute bottom-10 right-10">
                <span class="bg-white/90 backdrop-blur-sm px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 shadow-sm border border-slate-100">
                    Live Map Visualization
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .custom-scroll::-webkit-scrollbar { width: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    .trayek-item.active {
        border-color: #3b82f6;
        background: rgba(59, 130, 246, 0.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        // Init Map centered on Bandung
        const map = L.map('map', { zoomControl: true }).setView([-6.917, 107.619], 12);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        
        // Store all route layers
        let routeLayers = [];
        let markers = [];
        
        // Show all routes initially
        const trayekItems = document.querySelectorAll('.trayek-item');
        
        trayekItems.forEach(item => {
            const rute = item.dataset.rute;
            const warna = item.dataset.warna || '#3b82f6';
            const latAsal = parseFloat(item.dataset.latAsal);
            const lngAsal = parseFloat(item.dataset.lngAsal);
            const latTujuan = parseFloat(item.dataset.latTujuan);
            const lngTujuan = parseFloat(item.dataset.lngTujuan);
            
            // Draw route if available
            if (rute) {
                try {
                    const geoJson = JSON.parse(rute);
                    const layer = L.geoJSON(geoJson, {
                        style: { color: warna, weight: 3, opacity: 0.6 }
                    }).addTo(map);
                    routeLayers.push(layer);
                } catch (e) {
                    // If not valid GeoJSON, draw simple line
                    if (latAsal && lngAsal && latTujuan && lngTujuan) {
                        const line = L.polyline([[latAsal, lngAsal], [latTujuan, lngTujuan]], {
                            color: warna,
                            weight: 3,
                            opacity: 0.6
                        }).addTo(map);
                        routeLayers.push(line);
                    }
                }
            } else if (latAsal && lngAsal && latTujuan && lngTujuan) {
                // Draw simple line between origin and destination
                const line = L.polyline([[latAsal, lngAsal], [latTujuan, lngTujuan]], {
                    color: warna,
                    weight: 3,
                    opacity: 0.6
                }).addTo(map);
                routeLayers.push(line);
            }
            
            // Add markers for origin points
            if (latAsal && lngAsal) {
                const marker = L.circleMarker([latAsal, lngAsal], {
                    radius: 6,
                    fillColor: '#fbbf24',
                    color: '#f59e0b',
                    weight: 2,
                    fillOpacity: 1
                }).addTo(map);
                markers.push(marker);
            }
        });
        
        // Click handler for trayek items
        trayekItems.forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all
                trayekItems.forEach(i => i.classList.remove('active'));
                // Add active class to clicked
                this.classList.add('active');
                
                const latAsal = parseFloat(this.dataset.latAsal);
                const lngAsal = parseFloat(this.dataset.lngAsal);
                const latTujuan = parseFloat(this.dataset.latTujuan);
                const lngTujuan = parseFloat(this.dataset.lngTujuan);
                
                // Zoom to selected route
                if (latAsal && lngAsal && latTujuan && lngTujuan) {
                    const bounds = L.latLngBounds([
                        [latAsal, lngAsal],
                        [latTujuan, lngTujuan]
                    ]);
                    map.fitBounds(bounds, { padding: [50, 50] });
                }
            });
        });
    });
</script>
@endpush
