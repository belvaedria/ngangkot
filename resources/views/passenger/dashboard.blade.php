@extends('layouts.app_dashboard')
@include('layouts.menus.passenger')

@section('title', 'Dashboard Penumpang')

@section('content')
<div class="flex-1 flex flex-col relative">
    <div class="absolute inset-0" id="map"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-white/90 via-white/60 to-white/10 pointer-events-none"></div>

    <div class="relative z-10 flex-1 overflow-y-auto custom-scroll p-6 md:p-10 space-y-6">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-1 bg-white/90 backdrop-blur-xl border border-white/70 shadow-xl rounded-3xl p-6 relative">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Navigasi</p>
                        <h1 class="text-2xl font-black text-slate-900 leading-tight">Cari rute tercepat</h1>
                        <p class="text-xs text-slate-400 font-semibold" id="gps-status">Menunggu GPS...</p>
                    </div>
                    <span class="p-3 rounded-2xl bg-blue-50 text-blue-600"><i data-lucide="navigation" class="w-5 h-5"></i></span>
                </div>

                <form action="{{ route('navigasi.search') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-500">Lokasi Awal</label>
                            <button type="button" id="btn-edit-asal" class="text-[10px] font-bold text-blue-600 underline">Ubah asal</button>
                        </div>
                        <input id="display_asal" type="text" readonly placeholder="Sedang mencari lokasi..."
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-semibold text-slate-700">
                        <div id="asal-search" class="hidden">
                            <input id="input_asal" type="text" placeholder="Cari alamat atau tempat di Bandung"
                                   class="w-full mt-2 rounded-xl border border-slate-200 px-3 py-3 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-300">
                            <div id="asal_suggestions" class="mt-1 space-y-1"></div>
                        </div>
                        <input type="hidden" id="lat_asal" name="lat_asal">
                        <input type="hidden" id="lng_asal" name="lng_asal">
                        <input type="hidden" id="nama_asal" name="nama_asal">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500">Tujuan</label>
                        <input id="input_tujuan" type="text" name="nama_tujuan" required placeholder="Contoh: Stasiun Hall"
                               class="w-full rounded-xl border border-slate-200 px-3 py-3 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-300">
                        <div id="tujuan_suggestions" class="mt-1 space-y-1"></div>
                        <input type="hidden" name="lat_tujuan" id="lat_tujuan" value="">
                        <input type="hidden" name="lng_tujuan" id="lng_tujuan" value="">
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($favorit ?? [] as $fav)
                            <button type="button"
                                    class="px-3 py-2 rounded-lg border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100"
                                    onclick="isiFavorit('{{ $fav->asal_coords }}','{{ $fav->tujuan_coords }}','{{ $fav->asal_nama }}','{{ $fav->tujuan_nama }}')">
                                <i data-lucide="star" class="w-3 h-3 inline-block mr-1 text-amber-500"></i>{{ $fav->nama_label }}
                            </button>
                        @endforeach
                    </div>
                    <button type="submit" id="btn-cari" disabled
                            class="w-full rounded-xl bg-slate-900 text-white font-bold py-3 text-sm shadow-lg shadow-slate-900/20 disabled:opacity-50 flex items-center justify-center gap-2">
                        Cari Rute <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>

            <div class="w-full md:w-[360px] space-y-4">
                <div class="bg-white/90 backdrop-blur-xl border border-white/70 shadow-xl rounded-3xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-black text-slate-900">Trayek aktif</h3>
                        <span class="text-[10px] font-black text-blue-600 bg-blue-50 rounded-full px-2 py-1">Live</span>
                    </div>
                    <div class="space-y-3 max-h-[260px] overflow-y-auto custom-scroll pr-1">
                        @forelse($trayeks as $trayek)
                            <a href="{{ route('trayek.show', $trayek->kode_trayek) }}"
                               class="block border border-slate-100 rounded-2xl p-4 hover:-translate-y-1 transition-all bg-white group">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black text-xs" style="background: {{ $trayek->warna_angkot }}">
                                        {{ $trayek->kode_trayek }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $trayek->kode_trayek }}</p>
                                        <p class="text-sm font-black text-slate-900">{{ $trayek->nama_trayek }}</p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-xs text-slate-500">Belum ada data trayek.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-xl border border-white/70 shadow-xl rounded-3xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-black text-slate-900">Riwayat cepat</h3>
                        <a href="{{ route('passenger.riwayat.index') }}" class="text-[10px] font-bold text-blue-600">Lihat semua</a>
                    </div>
                    <div class="space-y-3 max-h-[200px] overflow-y-auto custom-scroll pr-1">
                        @forelse($riwayat ?? [] as $item)
                            <form action="{{ route('navigasi.search') }}" method="POST" class="flex items-center gap-3 border border-slate-100 rounded-xl p-3 bg-slate-50/60">
                                @csrf
                                <input type="hidden" name="asal_coords" value="{{ $item->asal_coords }}">
                                <input type="hidden" name="lat_asal" value="{{ explode(',', $item->asal_coords)[0] }}">
                                <input type="hidden" name="lng_asal" value="{{ explode(',', $item->asal_coords)[1] }}">
                                <input type="hidden" name="lat_tujuan" value="{{ explode(',', $item->tujuan_coords)[0] }}">
                                <input type="hidden" name="lng_tujuan" value="{{ explode(',', $item->tujuan_coords)[1] }}">
                                <input type="hidden" name="nama_asal" value="{{ $item->asal_nama }}">
                                <input type="hidden" name="nama_tujuan" value="{{ $item->tujuan_nama }}">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center"><i data-lucide="clock" class="w-4 h-4"></i></div>
                                <div class="flex-1">
                                    <p class="text-xs font-black text-slate-800">{{ $item->asal_nama }} → {{ $item->tujuan_nama }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $item->created_at->diffForHumans() }}</p>
                                </div>
                                <button class="text-[10px] font-bold text-blue-600">Pakai</button>
                            </form>
                        @empty
                            <p class="text-xs text-slate-500">Belum ada riwayat.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
    var map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-6.917464, 107.619122], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(map);

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                map.setView([lat, lng], 15);
                L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Kamu").openPopup();
                L.circle([lat, lng], {radius: 400, color: '#2563eb', fillOpacity: 0.08, weight: 1}).addTo(map);
                document.getElementById('lat_asal').value = lat;
                document.getElementById('lng_asal').value = lng;
                document.getElementById('nama_asal').value = "Lokasi Saya (" + lat.toFixed(4) + ")";
                document.getElementById('display_asal').value = "Lokasi Saya Saat Ini";
                validateForm();
                document.getElementById('gps-status').innerText = "Lokasi ditemukan akurat.";
                document.getElementById('gps-status').className = "text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded";
            },
            () => {
                document.getElementById('gps-status').innerText = "GPS tidak aktif, memakai lokasi default.";
                document.getElementById('lat_asal').value = -6.921;
                document.getElementById('lng_asal').value = 107.610;
                document.getElementById('display_asal').value = "Alun-alun Bandung";
                validateForm();
            }
        );
    }

    const bboxBandung = '107.4,-6.9,107.75,-6.75';
    function setupGeocoder(inputEl, listEl, onPick) {
        let timer = null;
        inputEl.addEventListener('input', () => {
            clearTimeout(timer);
            const q = inputEl.value.trim();
            if (q.length < 3) { listEl.innerHTML = ''; return; }
            timer = setTimeout(async () => {
                const url = `https://nominatim.openstreetmap.org/search?format=json&limit=5&bounded=1&viewbox=${bboxBandung}&q=${encodeURIComponent(q + ' Bandung')}`;
                const res = await fetch(url, { headers: { 'Accept-Language': 'id' }});
                const data = await res.json();
                listEl.innerHTML = data.map(item => `
                    <button type="button" class="w-full text-left px-3 py-2 rounded-lg border border-slate-100 bg-white hover:bg-blue-50 text-xs font-semibold text-slate-700"
                        data-lat="${item.lat}" data-lng="${item.lon}" data-label="${item.display_name}">
                        ${item.display_name}
                    </button>
                `).join('');
                listEl.querySelectorAll('button').forEach(btn => {
                    btn.addEventListener('click', () => {
                        onPick(btn.dataset.lat, btn.dataset.lng, btn.dataset.label);
                        listEl.innerHTML = '';
                    });
                });
            }, 350);
        });
    }

    function validateForm() {
        const ready = document.getElementById('lat_asal').value && document.getElementById('lng_asal').value &&
                      document.getElementById('lat_tujuan').value && document.getElementById('lng_tujuan').value;
        document.getElementById('btn-cari').disabled = !ready;
    }

    document.getElementById('btn-edit-asal').addEventListener('click', () => {
        document.getElementById('asal-search').classList.toggle('hidden');
        document.getElementById('input_asal').focus();
    });

    setupGeocoder(
        document.getElementById('input_asal'),
        document.getElementById('asal_suggestions'),
        (lat, lng, label) => {
            document.getElementById('lat_asal').value = lat;
            document.getElementById('lng_asal').value = lng;
            document.getElementById('nama_asal').value = label;
            document.getElementById('display_asal').value = label;
            validateForm();
        }
    );
    setupGeocoder(
        document.getElementById('input_tujuan'),
        document.getElementById('tujuan_suggestions'),
        (lat, lng, label) => {
            document.getElementById('lat_tujuan').value = lat;
            document.getElementById('lng_tujuan').value = lng;
            document.getElementById('input_tujuan').value = label;
            validateForm();
        }
    );

    function isiFavorit(asal, tujuan, namaAsal, namaTujuan) {
        const [latA, lngA] = asal.split(',');
        const [latT, lngT] = tujuan.split(',');
        document.getElementById('lat_asal').value = latA;
        document.getElementById('lng_asal').value = lngA;
        document.getElementById('nama_asal').value = namaAsal;
        document.querySelector('input[name=\"lat_tujuan\"]').value = latT;
        document.querySelector('input[name=\"lng_tujuan\"]').value = lngT;
        document.querySelector('input[name=\"nama_tujuan\"]').value = namaTujuan;
        document.getElementById('display_asal').value = namaAsal;
        validateForm();
    }
</script>
@endpush
