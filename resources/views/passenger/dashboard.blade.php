@extends('layouts.app_dashboard')
@section('title', 'Beranda — Penumpang')
@section('page-title', 'Halo, Wargi Bandung!')

@section('content')
<div class="relative min-h-[70vh] bg-slate-50 overflow-hidden rounded-2xl">
    <!-- Background map (soft) -->
    <div id="map" class="absolute inset-0 w-full h-full z-0 filter brightness-95 opacity-90"></div>
    
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
                           class="w-full pl-10 pr-4 py-3 bg-blue-50/50 border border-blue-100 rounded-xl outline-none text-sm font-bold text-slate-700 placeholder:text-slate-400 cursor-not-allowed">
                    
                    <!-- Hidden Input Data Sebenarnya -->
                    <input type="hidden" id="lat_asal" name="lat_asal">
                    <input type="hidden" id="lng_asal" name="lng_asal">
                    <input type="hidden" id="nama_asal" name="nama_asal">
                </div>

                <!-- Input Tujuan -->
                <div class="relative group z-10">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 bg-white p-1 rounded-full shadow-sm">
                        <i data-lucide="map-pin" class="w-3 h-3 text-rose-500"></i>
                    </div>
                    <input type="text" id="input_tujuan" name="nama_tujuan" placeholder="Mau kemana? (Misal: Stasiun Hall)" required
                           class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-rose-100 focus:border-rose-300 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400">

                    <!-- Dropdown suggestion (hidden until needed) -->
                    <div id="places_dropdown" class="absolute z-30 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-auto hidden"></div>

                    <!-- Small hint for provider source -->
                    <div id="place_source_label" class="text-xs text-slate-400 mt-1 hidden">Sumber: <span id="place_source_name" class="font-bold"></span></div>
                    <div class="text-xs text-slate-300 mt-1">Preferensi provider: <span class="font-bold">Mapbox</span> → <span class="font-bold">LocationIQ</span> → <span class="font-bold">Nominatim</span>. Untuk hasil terbaik, atur <code>MAPBOX_ACCESS_TOKEN</code> di file <code>.env</code>.</div>

                    <!-- Hidden inputs untuk menyimpan koordinat tujuan -->
                    <input type="hidden" id="lat_tujuan" name="lat_tujuan">
                    <input type="hidden" id="lng_tujuan" name="lng_tujuan">
                    <input type="hidden" id="place_source" name="place_source">
                </div>

                <button type="submit" id="btn-cari" disabled
                        class="w-full mt-3 py-4 bg-slate-900 text-white rounded-2xl font-black text-base shadow-2xl hover:bg-slate-800 transition-transform transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                    Cari Rute Tercepat
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
<script>
    // 1. Inisialisasi Peta (Center Bandung)
    var map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-6.917464, 107.619122], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(map);

    // 2. Logika Geolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                // Update Peta ke Lokasi User
                map.setView([lat, lng], 15);
                L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Kamu").openPopup();
                L.circle([lat, lng], {radius: 500, color: '#2563eb', fillOpacity: 0.1, weight: 1}).addTo(map);

                // Isi Form Hidden
                document.getElementById('lat_asal').value = lat;
                document.getElementById('lng_asal').value = lng;
                document.getElementById('nama_asal').value = "Lokasi Saya (" + lat.toFixed(4) + ")";
                
                // Update UI Display
                document.getElementById('display_asal').value = "Lokasi Saya Saat Ini";
                document.getElementById('display_asal').classList.remove('cursor-not-allowed', 'bg-blue-50/50');
                document.getElementById('display_asal').classList.add('bg-blue-50', 'text-blue-700');
                
                document.getElementById('gps-status').innerText = "Lokasi ditemukan akurat.";
                document.getElementById('gps-status').className = "text-[10px] font-bold text-emerald-600 mt-0.5 bg-emerald-50 px-2 py-0.5 rounded w-fit";
                
                // Aktifkan Tombol Cari
                document.getElementById('btn-cari').disabled = false;
                document.getElementById('btn-cari').classList.remove('opacity-50', 'cursor-not-allowed');
            },
            () => {
                document.getElementById('gps-status').innerText = "GPS tidak aktif. Menggunakan lokasi default.";
                // Fallback (Alun-alun Bandung) agar tidak error
                document.getElementById('lat_asal').value = -6.921;
                document.getElementById('lng_asal').value = 107.610;
                document.getElementById('btn-cari').disabled = false;
            }
        );
    }

    // 3. Autocomplete tujuan via Nominatim proxy (/places)
    (function(){
        const input = document.getElementById('input_tujuan');
        const dropdown = document.getElementById('places_dropdown');
        let timer = null;

        input.addEventListener('input', (e) => {
            const q = e.target.value.trim();
            if (timer) clearTimeout(timer);
            if (q.length < 1) { dropdown.classList.add('hidden'); return; }
            timer = setTimeout(() => {
                fetch('/places?q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(data => {
                        if (!Array.isArray(data) || data.length === 0) {
                            dropdown.innerHTML = '<div class="p-2 text-xs text-slate-400">Tidak ada hasil</div>';
                            dropdown.classList.remove('hidden');
                            return;
                        }
                        dropdown.innerHTML = data.map(p => `
                            <div data-lat="${p.lat}" data-lng="${p.lng}" data-name="${p.name}" data-source="${p.source || ''}" class="cursor-pointer p-2 hover:bg-slate-50 border-b last:border-b-0 text-sm">
                                <div class="flex justify-between items-center">
                                    <div class="font-bold truncate">${p.name}</div>
                                    <div class="text-[10px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full ml-2">${p.source ? p.source.toUpperCase() : ''}</div>
                                </div>
                                <div class="text-xs text-slate-400">${p.address || ''}</div>
                            </div>
                        `).join('');
                        dropdown.classList.remove('hidden');

                        Array.from(dropdown.children).forEach(child => {
                            child.addEventListener('click', () => {
                                const lat = child.dataset.lat;
                                const lng = child.dataset.lng;
                                const name = child.dataset.name;
                                const source = child.dataset.source || '';
                                document.getElementById('lat_tujuan').value = lat;
                                document.getElementById('lng_tujuan').value = lng;
                                document.getElementById('input_tujuan').value = name;
                                document.getElementById('place_source').value = source;
                                // show source label
                                if (source) {
                                    document.getElementById('place_source_name').innerText = source.toUpperCase();
                                    document.getElementById('place_source_label').classList.remove('hidden');
                                } else {
                                    document.getElementById('place_source_label').classList.add('hidden');
                                }

                                dropdown.classList.add('hidden');

                                // show marker
                                L.marker([parseFloat(lat), parseFloat(lng)]).addTo(map).bindPopup(name).openPopup();

                                // enable submit
                                document.getElementById('btn-cari').disabled = false;
                                document.getElementById('btn-cari').classList.remove('opacity-50', 'cursor-not-allowed');
                            });
                        });
                    })
                    .catch(() => {
                        dropdown.innerHTML = '<div class="p-2 text-xs text-slate-400">Terjadi kesalahan</div>';
                        dropdown.classList.remove('hidden');
                    });
            }, 250);
        });

        document.addEventListener('click', (ev) => {
            if (!dropdown.contains(ev.target) && ev.target !== input) dropdown.classList.add('hidden');
        });
    })();

    // Klik peta untuk pilih tujuan (opsional)
    map.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(6);
        const lng = e.latlng.lng.toFixed(6);
        document.getElementById('lat_tujuan').value = lat;
        document.getElementById('lng_tujuan').value = lng;
        document.getElementById('input_tujuan').value = 'Lokasi dipilih ('+lat+','+lng+')';

        L.marker([parseFloat(lat), parseFloat(lng)]).addTo(map).bindPopup('Lokasi dipilih').openPopup();
        document.getElementById('btn-cari').disabled = false;
        document.getElementById('btn-cari').classList.remove('opacity-50', 'cursor-not-allowed');
    });
</script>
@endpush
@endsection