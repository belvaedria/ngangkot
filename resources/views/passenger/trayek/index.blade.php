@extends('layouts.app_dashboard')
@include('layouts.menus.passenger')

@section('title', content: 'Lihat Trayek')

@section('content')
<div
  x-data="trayekPage()"
  x-init="initPage()"
  class="relative w-full h-screen overflow-hidden"
>

  {{-- MAP --}}
  <div id="map" class="absolute inset-0 z-0"></div>

  {{-- SIDEBAR TRAYEK --}}
  <aside
    class="fixed top-0 h-screen w-[380px] bg-white/95 backdrop-blur-xl
           border-r border-slate-200 shadow-xl z-50
           overflow-hidden flex flex-col"
    style="left: var(--sidebar-width, 88px);"
  >

    {{-- HEADER --}}
    <div class="p-5 border-b border-slate-200">
      <h1 class="text-lg font-semibold text-slate-800">
        Lihat Trayek Angkot
      </h1>
      <p class="text-sm text-slate-500">
        Informasi jalur & arah trayek
      </p>
    </div>

    {{-- SEARCH --}}
    <div class="p-4">
      <input
        type="text"
        placeholder="Cari trayek..."
        x-model="search"
        class="w-full rounded-xl border border-slate-300 px-4 py-2
               text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
      >
    </div>

    <div class="flex-1 overflow-y-auto">
  <div class="px-4 space-y-3 pb-6" x-show="!selectedDetail">
    <template x-for="trayek in filteredTrayeks" :key="trayek.kode_trayek">
      <button
        x-on:click="selectTrayek(trayek)"
        class="w-full text-left p-4 rounded-2xl border transition"
        :class="selectedTrayek?.kode_trayek === trayek.kode_trayek
          ? 'border-slate-400 bg-slate-100'
          : 'border-slate-200 hover:bg-slate-50'"
      >
        <div class="font-medium text-slate-800">
          <span x-text="trayek.nama_trayek"></span>
        </div>
      </button>
    </template>

    <div
      x-show="trayeks.length === 0"
      class="text-sm text-slate-500 text-center pt-10"
    >
      Tidak ada trayek.
    </div>
  </div>

  {{-- DETAIL TRAYEK (muncul setelah klik) --}}
  <div class="px-4 pb-6" x-show="selectedDetail">
    <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-4">

      {{-- Tombol balik ke list --}}
      <button
        class="text-sm text-slate-400 hover:text-slate-900"
        x-on:click="backToList()"
      >
        ← Kembali ke daftar trayek
      </button>

      {{-- GAMBAR VISUALISASI ANGKOT --}}
      <img
        class="w-full h-40 object-contain rounded-xl bg-slate-50 border border-slate-200"
        :src="`/images/${selectedDetail?.gambar_url}.png`"
        x-on:error="$event.target.src = `/images/${selectedDetail?.gambar_url}.jpg`"
        alt="Visualisasi angkot"
      />

      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="font-bold" x-text="selectedDetail?.nama_trayek"></div>

          <div class="text-sm text-slate-600 mt-1">
            <span x-text="selectedDetail?.arah_awal"></span>
            
            <span x-text="selectedDetail?.arah_akhir"></span>
          </div>
        </div>

        <span class="text-xs px-2 py-1 rounded-full border border-slate-200"
          x-text="isBalik ? 'Pulang' : 'Pergi'"></span>
      </div>

      <button
        class="w-full rounded-xl border border-slate-200 bg-blue-200 py-2 text-sm hover:bg-slate-100 disabled:opacity-50"
        x-on:click="toggleArah()"
        :disabled="!selectedDetail?.kode_balik"
      >
        Tukar arah
      </button>

      <div class="text-xs text-slate-500" x-show="loadingDetail">Loading detail…</div>

      {{-- DAFTAR JALAN --}}
      <div class="pt-2">
        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
          Jalan yang dilalui
        </div>

        <template x-if="Array.isArray(selectedDetail?.daftar_jalan) && selectedDetail.daftar_jalan.length">
          <ol class="space-y-2">
            <template x-for="(jalan, idx) in selectedDetail.daftar_jalan" :key="idx">
              <li class="flex items-start gap-2 text-sm text-slate-700">
                <span class="mt-2 inline-block w-2 h-2 rounded-full bg-slate-400"></span>
                <span x-text="jalan"></span>
              </li>
            </template>
          </ol>
        </template>

        <div class="text-sm text-slate-400" x-show="!selectedDetail?.daftar_jalan">
          (Daftar jalan belum tersedia)
        </div>
      </div>

    </div>
  </div>

</div>


    </div>

  </aside>
</div>
@endsection


@push('scripts')
<script>
function trayekPage() {
  return {
    // ===== state =====
    map: null,
    trayeks: [],
    selectedTrayek: null,

    // detail trayek (buat gambar rute)
    selectedDetail: null,
    isBalik: false,
    loadingDetail: false,

    search: '',

    // layer map
    routeLayer: null,
    startMarker: null,
    endMarker: null,


    initPage() {
      // collapse sidebar dashboard (sama kayak navigasi)
      window.dispatchEvent(new CustomEvent('collapse-dashboard-sidebar'));

      this.initMap();
      this.fetchTrayeks();
    },

    initMap() {
      this.map = L.map('map', {
        zoomControl: true,
        scrollWheelZoom: true
      }).setView([-6.9147, 107.6098], 12);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }).addTo(this.map);
    },

    async fetchTrayeks() {
      try {
        const res = await fetch('/api/trayeks');
        if (!res.ok) throw new Error('HTTP ' + res.status);

        const data = await res.json();
        this.trayeks = Array.isArray(data) ? data : [];
      } catch (e) {
        console.error('Gagal load trayek', e);
        this.trayeks = [];
      }
    },

    async selectTrayek(trayek) {
      this.selectedTrayek = trayek;
      this.isBalik = false;
      await this.loadDetailAndDraw(trayek.kode_trayek);
    },

    async toggleArah() {
      if (!this.selectedDetail?.kode_balik) return;

      this.isBalik = !this.isBalik;

      const kode = this.isBalik
        ? this.selectedDetail.kode_balik
        : this.selectedTrayek.kode_trayek;

      await this.loadDetailAndDraw(kode);
    },

    async loadDetailAndDraw(kode) {
      this.loadingDetail = true;
      try {
        const res = await fetch(`/api/trayeks/${kode}`);
        if (!res.ok) throw new Error('HTTP ' + res.status);

        const detail = await res.json();
        this.selectedDetail = detail;

        this.drawRoute(detail);
      } catch (e) {
        console.error('Gagal load detail trayek', e);
      } finally {
        this.loadingDetail = false;
      }
    },

    drawRoute(detail) {
      // bersihin layer lama
      if (this.routeLayer) {
        this.map.removeLayer(this.routeLayer);
        this.routeLayer = null;
      }

      // parse geojson
      let geo;
      try {
        geo = typeof detail.rute_json === 'string'
          ? JSON.parse(detail.rute_json)
          : detail.rute_json;
      } catch (e) {
        console.error('GeoJSON invalid:', e);
        return;
      }

      // gambar jalur
      this.routeLayer = L.geoJSON(geo, {
        style: {
          weight: 6,
          opacity: 0.9,
          color: detail.warna_angkot || '#2563eb'
        }
      }).addTo(this.map);

      // ambil koordinat awal & akhir (asumsi LineString pertama)
    const coords = geo?.features?.[0]?.geometry?.coordinates;
    if (Array.isArray(coords) && coords.length >= 2) {
    const first = coords[0];                  // [lng, lat]
    const last  = coords[coords.length - 1];  // [lng, lat]

    this.startMarker = L.circleMarker([first[1], first[0]], {
        radius: 8,
        weight: 2,
        color: '#16a34a',
        fillColor: '#22c55e',
        fillOpacity: 1
    }).addTo(this.map).bindTooltip('Titik awal');

    this.endMarker = L.circleMarker([last[1], last[0]], {
        radius: 8,
        weight: 2,
        color: '#b91c1c',
        fillColor: '#ef4444',
        fillOpacity: 1
    }).addTo(this.map).bindTooltip('Titik akhir');
    }


      // zoom ke jalur
      const bounds = this.routeLayer.getBounds();
      if (bounds.isValid()) {
        this.map.fitBounds(bounds.pad(0.2));
      }
    },

    get filteredTrayeks() {
      if (!this.search) return this.trayeks;
      const q = this.search.toLowerCase();

      return this.trayeks.filter(t =>
        (t.nama_trayek || '').toLowerCase().includes(q) ||
        (t.kode_trayek || '').toLowerCase().includes(q) ||
        (t.arah_awal || '').toLowerCase().includes(q) ||
        (t.arah_akhir || '').toLowerCase().includes(q)
      );
    },

    backToList() {
        this.selectedDetail = null;
        this.selectedTrayek = null;
        this.isBalik = false;

        // bersihin layer map
        if (this.routeLayer) {
            this.map.removeLayer(this.routeLayer);
            this.routeLayer = null;
        }
        if (this.startMarker) { this.map.removeLayer(this.startMarker); this.startMarker = null; }
        if (this.endMarker) { this.map.removeLayer(this.endMarker); this.endMarker = null; }
    },

  }
}
</script>
@endpush


