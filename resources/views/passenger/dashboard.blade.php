@extends('layouts.app_dashboard')
@include('layouts.menus.passenger')

@section('title', 'Dashboard Penumpang')

@section('content')
@php
  $hasilRute = $hasilRute ?? collect();

  $hasResult = $hasilRute instanceof \Illuminate\Support\Collection
      ? $hasilRute->isNotEmpty()
      : !empty($hasilRute);

  $searched = request()->routeIs('navigasi.search');

  $showSidebar = $searched;

  $showFloating = !$showSidebar;
@endphp


<div
  x-data="{
    hasResult: @json($hasResult),
    selectedRoute: null,
    guestOpen: false
  }"
  x-init="@json($showSidebar) ? $dispatch('collapse-dashboard-sidebar') : null"
  class="min-h-screen flex flex-col relative"
>

    {{-- MAP background --}}
    <div class="absolute inset-0 z-0" id="map"></div>

    {{-- Content wrapper --}}
    <div class="relative z-10 flex-1 overflow-y-auto custom-scroll p-6 md:p-10 pointer-events-none"
     style="padding-left: calc(var(--sidebar-width, 260px) + 0px);">
        <div class="flex flex-col lg:flex-row gap-6 items-start">

            @if($showFloating)
  {{-- FORM floating top-center (center berdasarkan sisa area setelah sidebar) --}}
  <div
    class="fixed top-8 z-20 pointer-events-auto"
    style="
      left: calc(var(--sidebar-width, 260px) + (100vw - var(--sidebar-width, 260px)) / 2);
      transform: translateX(-50%);
      width: min(500px, calc(100vw - var(--sidebar-width, 260px) - 2rem));
    "
  >
    <div class="bg-white/90 backdrop-blur-xl border border-white/70 shadow-xl rounded-3xl p-6">
      <div x-show="!hasResult" x-cloak>
        <h1 class="text-2xl font-black text-slate-900 leading-tight">Mau ke mana hari ini?</h1>

        <form action="{{ route('navigasi.search') }}" method="POST" class="space-y-4" id="navForm">
                                    @csrf

                                    {{-- Hidden coords --}}
                                    <input type="hidden" name="lat_asal" id="lat_asal">
                                    <input type="hidden" name="lng_asal" id="lng_asal">
                                    <input type="hidden" name="lat_tujuan" id="lat_tujuan">
                                    <input type="hidden" name="lng_tujuan" id="lng_tujuan">
                                    <input type="hidden" name="nama_asal" id="nama_asal">
                                    <input type="hidden" name="nama_tujuan" id="nama_tujuan">

                                    <div class="relative">
                                        <label class="text-xs font-bold text-slate-600">Lokasi Awal</label>
                                        <input
                                        id="input_asal"
                                        type="text"
                                        value="{{ old('nama_asal', $asal ?? '') }}"
                                        class="w-full mt-1 px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        placeholder="Masukkan lokasi awal..."
                                        autocomplete="off"
                                        />

                                        <div id="suggestions_asal"
                                            class="absolute top-full left-0 z-[9999] mt-2 w-full bg-white border border-slate-200 rounded-2xl shadow-xl hidden max-h-64 overflow-y-auto">
                                        </div>
                                    </div>

                                    {{-- Tujuan + autocomplete --}}
                                    <div class="relative">
                                        <label class="text-xs font-bold text-slate-600">Lokasi tujuan</label>
                                        <input
                                        id="input_tujuan"
                                        type="text"
                                        value="{{ old('nama_tujuan', $tujuan ?? '') }}"
                                        class="w-full mt-1 px-4 py-3 rounded-2xl border border-slate-200 bg-white font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        placeholder="Masukkan tujuan Anda..."
                                        autocomplete="off"
                                        />


                                        {{-- Dropdown autocomplete --}}
                                        <div id="suggestions"
                                            class="absolute z-50 mt-2 w-full bg-white border border-slate-200 rounded-2xl shadow-xl hidden max-h-64 overflow-y-auto">
                                        </div>
                                    </div>

                                    <button
                                        type="submit"
                                        id="btnCari"
                                        class="w-full py-3 rounded-2xl bg-blue-600 text-white font-black tracking-tight shadow-lg hover:bg-blue-700 transition disabled:opacity-40 disabled:cursor-not-allowed"
                                        disabled
                                    >
                                        Cari Rute
                                    </button>

                                    @if ($errors->any())
                                        <div class="mt-2 p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                                            <p class="font-bold mb-1">Oops, ada yang kurang:</p>
                                            <ul class="list-disc ml-5">
                                                @foreach ($errors->all() as $err)
                                                    <li>{{ $err }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </form>
      </div>
    </div>
  </div>

  {{-- GUEST badge fixed pojok kanan bawah --}}
  @if(!Auth::check())
    <div class="fixed bottom-6 right-6 z-50 pointer-events-auto" x-data="{ open:false }">
      <button
        x-on:click="open = !open"
        class="flex items-center gap-2 rounded-full bg-white/90 backdrop-blur border border-slate-200 px-3 py-2 shadow-lg"
      >
        <i data-lucide="lock" class="w-4 h-4"></i>
        <span class="text-sm font-semibold">Anda masuk sebagai Guest</span>
      </button>

      <div x-show="open" x-cloak @click.away="open=false"
        class="mt-2 w-80 rounded-2xl bg-white shadow-2xl border border-slate-200 p-4">
        <p class="text-sm text-slate-600 mt-1">
          Anda tetap bisa cari rute. Tapi riwayat perjalanan tidak disimpan. Jika ingin menyimpan perjalanan, silakan login.
        </p>

        <a href="{{ route('login') }}"
          class="mt-3 inline-flex w-full items-center justify-center rounded-xl bg-slate-900 text-white py-3 font-semibold">
          Login
        </a>
      </div>
    </div>
  @endif
@endif



                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                        @if($showSidebar)
                        <aside
                            class="fixed left-0 top-0 h-screen w-[420px] pointer-events-auto bg-white/95 backdrop-blur-xl border-r border-slate-200 z-40 flex flex-col"
                            style="left: var(--sidebar-width, 0px);"
                        >
                            <div class="p-6">
                                <form action="{{ route('navigasi.search') }}" method="POST" class="space-y-4" id="navForm">
                                    @csrf

                                    {{-- Hidden coords --}}
                                    <input type="hidden" name="lat_asal" id="lat_asal">
                                    <input type="hidden" name="lng_asal" id="lng_asal">
                                    <input type="hidden" name="lat_tujuan" id="lat_tujuan">
                                    <input type="hidden" name="lng_tujuan" id="lng_tujuan">
                                    <input type="hidden" name="nama_asal" id="nama_asal">
                                    <input type="hidden" name="nama_tujuan" id="nama_tujuan">

                                    <div class="relative">
                                        <label class="text-xs font-bold text-slate-600">Lokasi awal</label>
                                        <input
                                        id="input_asal"
                                        type="text"
                                        value="{{ old('nama_asal', $asal ?? '') }}"
                                        class="w-full mt-1 px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        placeholder="Masukkan lokasi awal..."
                                        autocomplete="off"
                                        />

                                        <div id="suggestions_asal"
                                            class="absolute top-full left-0 z-[9999] mt-2 w-full bg-white border border-slate-200 rounded-2xl shadow-xl hidden max-h-64 overflow-y-auto">
                                        </div>
                                    </div>

                                {{-- Tujuan + autocomplete --}}
                                <div class="relative">
                                    <label class="text-xs font-bold text-slate-600">Lokasi tujuan</label>
                                    <input
                                    id="input_tujuan"
                                    type="text"
                                    value="{{ old('nama_tujuan', $tujuan ?? '') }}"
                                    class="w-full mt-1 px-4 py-3 rounded-2xl border border-slate-200 bg-white font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    placeholder="Masukkan tujuan Anda..."
                                    autocomplete="off"
                                    />


                                    {{-- Dropdown autocomplete --}}
                                    <div id="suggestions"
                                        class="absolute z-50 mt-2 w-full bg-white border border-slate-200 rounded-2xl shadow-xl hidden max-h-64 overflow-y-auto">
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    id="btnCari"
                                    class="w-full py-3 rounded-2xl bg-blue-600 text-white font-black tracking-tight shadow-lg hover:bg-blue-700 transition disabled:opacity-40 disabled:cursor-not-allowed"
                                    disabled
                                >
                                    Cari Rute
                                </button>

                                @if ($errors->any())
                                    <div class="mt-2 p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                                        <p class="font-bold mb-1">Oops, ada yang kurang:</p>
                                        <ul class="list-disc ml-5">
                                            @foreach ($errors->all() as $err)
                                                <li>{{ $err }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </form>
                        </div>

                        <div class="flex-1 overflow-y-auto custom-scroll p-4 space-y-3">
                        @if(empty($hasilRute))
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <p class="font-black text-slate-900">Tidak ditemukan rute yang cocok.</p>
                            <p class="text-sm text-slate-600 mt-1">Coba titik tujuan lain yang lebih spesifik.</p>
                            </div>
                        @else
                            @foreach($hasilRute as $idx => $route)
                            <details
                            class="rounded-2xl border border-slate-200 bg-white overflow-hidden"
                            data-rute='@json($trayek->rute_json)'
                            data-naik='@json($trayek->titik_naik ?? null)'
                            data-turun='@json($trayek->titik_turun ?? null)'
                            data-geojson='@json($route["map_geojson"])'
                            >
                                <summary class="p-4 cursor-pointer">
                                    <p class="font-black">
                                        Opsi {{ $idx + 1 }} • {{ $route['variant'] }}
                                    </p>
                                    <p class="text-xs text-slate-600">
                                        {{ $route['total_duration_min'] }} min •
                                        {{ number_format($route['total_distance_m']/1000,1) }} km •
                                        Rp {{ number_format($route['total_fare']) }}
                                    </p>
                                </summary>


                                <div class="p-4 bg-slate-50 border-t border-slate-200 space-y-3">
                                @foreach($route['segments'] as $step)
                                    <div class="text-sm">
                                        @if($step['type'] === 'walk')
                                        <p class="font-bold">🚶 Jalan kaki</p>
                                        <p class="text-xs text-slate-600">
                                            {{ $step['from']['name'] ?? '' }} → {{ $step['to']['name'] ?? '' }}
                                            ({{ $step['duration_min'] }} min)
                                        </p>
                                        @else
                                        <p class="font-bold">
                                            🚌 {{ $step['trayek_name'] }}
                                        </p>
                                        <p class="text-xs text-slate-600">
                                            {{ $step['from']['name'] ?? '' }} → {{ $step['to']['name'] ?? '' }}
                                        </p>
                                        <p class="text-xs font-black">{{ $step['fare_label'] }}</p>
                                        @endif
                                    </div>
                                @endforeach

                                </div>
                            </details>
                            @endforeach
                        @endif
                        </div>
                    </aside>
                    @endif

@endsection

@push('scripts')
<script>
    lucide.createIcons();

    // --- MAP ---
    var map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-6.917464, 107.619122], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(map);

    window.addEventListener('collapse-dashboard-sidebar', () => {
        setTimeout(() => map.invalidateSize(), 250);
    });

    const mapEl = document.getElementById('map');
    const ro = new ResizeObserver(() => {
        map.invalidateSize();
    });
    ro.observe(mapEl);


    function validateForm() {
        const latTujuan = document.getElementById('lat_tujuan').value;
        const lngTujuan = document.getElementById('lng_tujuan').value;
        const btn = document.getElementById('btnCari');
        btn.disabled = !(latTujuan && lngTujuan);
    }

    const inputAsal = document.getElementById('input_asal');
    const suggestionsAsal = document.getElementById('suggestions_asal');

    let debounceAsal;

    inputAsal.addEventListener('focus', () => {
    // kalau user mau planning, ubah style jadi putih
    inputAsal.classList.remove('bg-slate-50');
    inputAsal.classList.add('bg-white');
    });

    inputAsal.addEventListener('input', () => {
    clearTimeout(debounceAsal);
    const q = inputAsal.value.trim();

    resetAsalState();

    if (q.length < 3) {
        suggestionsAsal.classList.add('hidden');
        suggestionsAsal.innerHTML = '';
        return;
    }

    debounceAsal = setTimeout(async () => {
        const viewbox = "107.534,-6.999,107.731,-6.840";
        const url = `https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=8&bounded=1&viewbox=${encodeURIComponent(viewbox)}&q=${encodeURIComponent(q + ' Bandung')}`;

        try {
        const res = await fetch(url, { headers: { 'Accept-Language': 'id' }});
        const data = await res.json();

        suggestionsAsal.innerHTML = '';
        if (!data || data.length === 0) {
            suggestionsAsal.classList.add('hidden');
            return;
        }

        data.forEach(item => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'w-full text-left px-4 py-3 hover:bg-slate-50 transition border-b border-slate-100 last:border-b-0';
            btn.innerHTML = `<div class="text-sm font-black text-slate-900">${item.display_name}</div>
                            <div class="text-xs text-slate-500">${item.type || ''}</div>`;
            btn.onclick = () => {
            const lat = parseFloat(item.lat);
            const lon = parseFloat(item.lon);

            document.getElementById('lat_asal').value = lat;
            document.getElementById('lng_asal').value = lon;
            document.getElementById('nama_asal').value = item.display_name;

            inputAsal.value = item.display_name;

            suggestionsAsal.classList.add('hidden');
            suggestionsAsal.innerHTML = '';

            // update map marker asal
            map.setView([lat, lon], 15);
            setTimeout(() => map.invalidateSize(), 200);
            L.marker([lat, lon]).addTo(map).bindPopup("Lokasi awal").openPopup();

            validateForm();
            };
            suggestionsAsal.appendChild(btn);
        });

        suggestionsAsal.classList.remove('hidden');
        } catch(e) {
        suggestionsAsal.classList.add('hidden');
        }
    }, 250);
    });

    document.addEventListener('click', (e) => {
    if (!suggestionsAsal.contains(e.target) && e.target !== inputAsal) {
        suggestionsAsal.classList.add('hidden');
    }
    });

    // --- AUTOCOMPLETE tujuan (Nominatim) ---
    const input = document.getElementById('input_tujuan');
    const suggestions = document.getElementById('suggestions');

    let debounce;
    input.addEventListener('input', () => {
        resetTujuanState();
        clearTimeout(debounce);
        const q = input.value.trim();
        if (q.length < 3) {
            suggestions.classList.add('hidden');
            suggestions.innerHTML = '';
            document.getElementById('lat_tujuan').value = '';
            document.getElementById('lng_tujuan').value = '';
            document.getElementById('nama_tujuan').value = '';
            validateForm();
            return;
        }

        debounce = setTimeout(async () => {
            // Viewbox Bandung (perkiraan): west,south,east,north
            const viewbox = "107.534,-6.999,107.731,-6.840";
            const url = `https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=8&bounded=1&viewbox=${encodeURIComponent(viewbox)}&q=${encodeURIComponent(q + ' Bandung')}`;

            try {
                const res = await fetch(url, {
                    headers: { 'Accept-Language': 'id' }
                });
                const data = await res.json();

                suggestions.innerHTML = '';
                if (!data || data.length === 0) {
                    suggestions.classList.add('hidden');
                    return;
                }

                data.forEach(item => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-left px-4 py-3 hover:bg-slate-50 transition border-b border-slate-100 last:border-b-0';
                    btn.innerHTML = `<div class="text-sm font-black text-slate-900">${item.display_name}</div>
                                     <div class="text-xs text-slate-500">${item.type || ''}</div>`;
                    btn.onclick = () => {
                        const lat = parseFloat(item.lat);
                        const lon = parseFloat(item.lon);

                        document.getElementById('lat_tujuan').value = lat;
                        document.getElementById('lng_tujuan').value = lon;
                        document.getElementById('nama_tujuan').value = item.display_name;

                        input.value = item.display_name;

                        suggestions.classList.add('hidden');
                        suggestions.innerHTML = '';

                        // Pan to tujuan
                        map.setView([lat, lon], 15);
                        setTimeout(() => map.invalidateSize(), 200);

                        L.marker([lat, lon]).addTo(map).bindPopup("Tujuan").openPopup();

                        validateForm();
                    };
                    suggestions.appendChild(btn);
                });

                suggestions.classList.remove('hidden');
            } catch (e) {
                suggestions.classList.add('hidden');
            }
        }, 250);
    });

    // close suggestions when click outside
    document.addEventListener('click', (e) => {
        if (!suggestions.contains(e.target) && e.target !== input) {
            suggestions.classList.add('hidden');
        }
    });

    function resetTujuanState() {
        document.getElementById('lat_tujuan').value = '';
        document.getElementById('lng_tujuan').value = '';
        document.getElementById('nama_tujuan').value = '';
        validateForm();
    }

    function resetAsalState() {
        document.getElementById('lat_asal').value = '';
        document.getElementById('lng_asal').value = '';
        document.getElementById('nama_asal').value = '';
        validateForm();
    }

    let activePolyline = null;
    let activeMarkers = [];

    function clearActiveRoute() {
        if (activePolyline) { map.removeLayer(activePolyline); activePolyline = null; }
        activeMarkers.forEach(m => map.removeLayer(m));
        activeMarkers = [];
    }

    document.querySelectorAll('details[data-geojson]').forEach(dt => {
    dt.addEventListener('toggle', () => {
        if (!dt.open) return;

        clearActiveRoute();

        const geo = JSON.parse(dt.dataset.geojson);

        activePolyline = L.geoJSON(geo, {
        style: f => ({
            color: f.properties?.mode === 'walk' ? '#64748b' : f.properties?.color,
            weight: f.properties?.mode === 'walk' ? 3 : 6,
            dashArray: f.properties?.mode === 'walk' ? '4,6' : null
        })
        }).addTo(map);

        map.fitBounds(activePolyline.getBounds(), { padding: [40,40] });
    });
    });

    document.querySelectorAll('details[data-rute]').forEach(dt => {
    dt.addEventListener('toggle', () => {
        if (!dt.open) return;

        // tutup details lain biar satu rute aktif
        document.querySelectorAll('details[data-rute]').forEach(other => {
        if (other !== dt) other.open = false;
        });

        clearActiveRoute();

        // rute_json kamu itu string json geojson
        const geoStr = dt.dataset.rute;
        let geo;
        try { geo = JSON.parse(geoStr); } catch(e) { return; }

        const coords = geo?.features?.[0]?.geometry?.coordinates || [];
        // Leaflet butuh [lat,lng]
        const latlngs = coords.map(c => [c[1], c[0]]);

        activePolyline = L.polyline(latlngs, { weight: 5 }).addTo(map);
        map.fitBounds(activePolyline.getBounds(), { padding: [30, 30] });

        const naik = dt.dataset.naik ? JSON.parse(dt.dataset.naik) : null;
        const turun = dt.dataset.turun ? JSON.parse(dt.dataset.turun) : null;

        if (naik?.lat && naik?.lng) {
        activeMarkers.push(L.marker([naik.lat, naik.lng]).addTo(map).bindPopup("Titik naik"));
        }
        if (turun?.lat && turun?.lng) {
        activeMarkers.push(L.marker([turun.lat, turun.lng]).addTo(map).bindPopup("Titik turun"));
        }
    });
    });

</script>
@endpush
