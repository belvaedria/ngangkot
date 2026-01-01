@extends('layouts.app_dashboard')
@include('layouts.menus.passenger')

@section('title', 'Dashboard Penumpang')

@section('content')
<div
  x-data="{
    hasResult: {{ isset($hasilRute) ? 'true' : 'false' }},
    selectedRoute: null
  }"
  class="min-h-screen flex flex-col relative"
>

    {{-- MAP background --}}
    <div class="absolute inset-0 z-0" id="map"></div>
    @if(!isset($hasilRute))
        <div class="absolute inset-0 z-[1] bg-gradient-to-b from-white/90 via-white/60 to-white/10 pointer-events-none"></div>
    @endif

    {{-- Content wrapper --}}
    <div class="relative z-10 flex-1 overflow-y-auto custom-scroll p-6 md:p-10 lg:pl-[440px]">
        <div class="flex flex-col lg:flex-row gap-6 items-start">

            {{-- LEFT: Search & quick context --}}
            <div class="flex-1 space-y-6">
                    {{-- Search Card --}}
                <div class="bg-white/90 backdrop-blur-xl border border-white/70 shadow-xl rounded-3xl p-6 relative">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h1 class="text-2xl font-black text-slate-900 leading-tight">Mau ke mana hari ini?</h1>
                            <p class="text-xs text-slate-400 font-semibold" id="gps-status">Menunggu GPS...</p>
                        </div>
                        <span class="p-3 rounded-2xl bg-blue-50 text-blue-600">
                            <i data-lucide="navigation" class="w-5 h-5"></i>
                        </span>
                    </div>
                </div>

                {{-- Hint guest/login --}}
                @guest
                    <div class="bg-white/85 backdrop-blur border border-white/70 shadow rounded-3xl p-5">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 p-2 rounded-2xl bg-amber-50 text-amber-700">
                                <i data-lucide="lock" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="font-black text-slate-900">Anda masuk sebagai Guest</p>
                                <p class="text-sm text-slate-600 mt-1">
                                    Anda tetap bisa cari rute. Tapi <span class="font-bold">riwayat perjalanan tidak disimpan</span>.
                                    Jika ingin menyimpan perjalanan, silahkan login.
                                </p>
                                <a href="{{ route('login') }}" class="inline-flex mt-3 px-4 py-2 rounded-2xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">
                                    Login
                                </a>
                            </div>
                        </div>
                    </div>
                @endguest

            </div>
        </div>
    </div>
</div>



@if(isset($hasilRute))
  <aside class="fixed left-[var(--sidebar-width)] top-0 h-screen w-[420px] bg-white/95 backdrop-blur-xl border-r border-slate-200 shadow-2xl z-40 flex flex-col">
    <div>
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
                <input
                id="input_asal"
                type="text"
                value="{{ old('nama_asal', $asal ?? '') }}"
                class="w-full mt-1 px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200"
                placeholder="Lokasi saya (GPS)"
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
                            placeholder="Ketik tujuan Anda..."
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
      @if(count($hasilRute)===0)
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
          <p class="font-black text-slate-900">Tidak ditemukan rute yang cocok.</p>
          <p class="text-sm text-slate-600 mt-1">Coba titik tujuan lain yang lebih spesifik.</p>
        </div>
      @else
        @foreach($hasilRute as $idx => $trayek)
          <details class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
            <summary class="cursor-pointer p-4 hover:bg-slate-50">
              <p class="font-black text-slate-900">{{ $trayek->nama_trayek }}</p>
              <p class="text-xs text-slate-600 mt-1">
                {{ $trayek->tarif_total_label ?? '-' }} • {{ $trayek->info_waktu ?? '-' }} • {{ $trayek->info_jarak ?? '-' }}
              </p>
            </summary>

            <div class="p-4 bg-slate-50 border-t border-slate-200 space-y-3">
              @foreach(($trayek->rute_detail ?? []) as $step)
                <div class="text-sm">
                  <p class="font-bold text-slate-900">{{ $step['instruksi'] ?? '' }}</p>
                  <p class="text-xs text-slate-600">{{ $step['detail'] ?? '' }} • {{ $step['waktu'] ?? '' }}</p>
                  @if(($step['jenis'] ?? '')==='angkot' && !empty($step['tarif_label']))
                    <p class="text-xs font-black text-slate-900 mt-1">{{ $step['tarif_label'] }} (sekali naik)</p>
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

    function validateForm() {
        const latTujuan = document.getElementById('lat_tujuan').value;
        const lngTujuan = document.getElementById('lng_tujuan').value;
        const btn = document.getElementById('btnCari');
        btn.disabled = !(latTujuan && lngTujuan);
    }

    // GPS default asal
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                map.setView([lat, lng], 15);
                setTimeout(() => map.invalidateSize(), 200);

                L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Kamu").openPopup();
                L.circle([lat, lng], {radius: 400, color: '#2563eb', fillOpacity: 0.08, weight: 1}).addTo(map);

                document.getElementById('lat_asal').value = lat;
                document.getElementById('lng_asal').value = lng;
                document.getElementById('nama_asal').value = "Lokasi Saya (" + lat.toFixed(4) + ")";
                document.getElementById('input_asal').value = "Lokasi saat ini";

                validateForm();

                const gps = document.getElementById('gps-status');
                gps.innerText = "Lokasi ditemukan akurat.";
                gps.className = "text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded inline-block mt-1";
            },
            () => {
                const gps = document.getElementById('gps-status');
                gps.innerText = "GPS tidak aktif, memakai lokasi default Bandung.";
                gps.className = "text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded inline-block mt-1";

                // fallback Bandung
                const lat = -6.917464, lng = 107.619122;
                map.setView([lat, lng], 15);
                setTimeout(() => map.invalidateSize(), 200);


                document.getElementById('lat_asal').value = lat;
                document.getElementById('lng_asal').value = lng;
                document.getElementById('nama_asal').value = "Pusat Kota Bandung";
                document.getElementById('display_asal').value = "Pusat Kota Bandung";

                validateForm();
            }
        );
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


</script>
@endpush
