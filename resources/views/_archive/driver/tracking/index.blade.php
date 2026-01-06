@extends('layouts.app_dashboard')
@include('layouts.menus.driver')

@section('title', 'Tracking Perjalanan')

@section('content')
<div class="flex-1 grid grid-cols-1 xl:grid-cols-3 gap-6 p-6 md:p-10 overflow-y-auto custom-scroll">
    <div class="xl:col-span-2 bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Live lokasi</p>
                <h2 class="text-2xl font-black text-slate-900">Bagikan posisi ke penumpang</h2>
            </div>
            @if(session('success'))
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">{{ session('success') }}</span>
            @endif
            @if(session('error'))
                <span class="text-xs font-bold text-rose-600 bg-rose-50 px-3 py-1 rounded-full">{{ session('error') }}</span>
            @endif
        </div>
        <div id="map" class="w-full h-[420px] rounded-2xl border border-slate-100"></div>
        <div class="mt-4 flex flex-wrap gap-3 items-center">
            @if($angkot)
                <form action="{{ route('driver.tracking.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="{{ $angkot->is_active ? 'nonaktif' : 'aktif' }}">
                    <button class="px-4 py-2 rounded-xl font-bold text-sm flex items-center gap-2 {{ $angkot->is_active ? 'bg-rose-600 text-white' : 'bg-blue-600 text-white' }}">
                        <i data-lucide="{{ $angkot->is_active ? 'stop-circle' : 'radio' }}" class="w-4 h-4"></i>
                        {{ $angkot->is_active ? 'Stop Berbagi Lokasi' : 'Mulai Berbagi Lokasi' }}
                    </button>
                </form>
                <span class="text-xs font-semibold text-slate-500">Status: {{ $angkot->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            @else
                <p class="text-sm text-slate-500">Pilih armada dulu untuk memulai tracking.</p>
            @endif
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-5">
            <h3 class="text-sm font-black text-slate-900 mb-3">Armada terpilih</h3>
            @if($angkot)
                <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/70 space-y-1">
                    <p class="text-xs font-bold text-slate-500 uppercase">{{ $angkot->plat_nomor }}</p>
                    <p class="text-sm font-black text-slate-900">Trayek: {{ $angkot->trayek?->nama_trayek }}</p>
                    <p class="text-[11px] text-slate-500">Terakhir update: {{ $angkot->updated_at->diffForHumans() }}</p>
                </div>
            @else
                <p class="text-sm text-slate-500">Belum ada armada dipilih.</p>
            @endif
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-black text-slate-900">Pilih Angkot</h3>
            </div>
            <form action="{{ route('driver.tracking.pilih') }}" method="POST" class="space-y-3">
                @csrf
                <select name="angkot_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                    @foreach($availableAngkots as $item)
                        <option value="{{ $item->id }}">{{ $item->plat_nomor }} - {{ $item->trayek?->nama_trayek }}</option>
                    @endforeach
                </select>
                <button class="w-full bg-slate-900 text-white rounded-xl py-2.5 text-sm font-bold">Gunakan Armada</button>
            </form>
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-5">
            <h3 class="text-sm font-black text-slate-900 mb-3">Sesi aktif</h3>
            @if($sesiAktif)
                <div class="p-4 rounded-2xl border border-blue-100 bg-blue-50/60">
                    <p class="text-xs font-bold text-blue-700">Sedang narik</p>
                    <p class="text-sm font-black text-slate-900 mt-1">{{ $sesiAktif->waktu_mulai->format('d M Y H:i') }}</p>
                    <p class="text-[11px] text-slate-500">Jarak ditempuh: {{ number_format($sesiAktif->jarak_tempuh_km, 2) }} km</p>
                </div>
            @else
                <p class="text-sm text-slate-500">Belum ada sesi berjalan.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
    var map = L.map('map', { zoomControl: true }).setView([-6.917, 107.619], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(map);
    let marker = null;

    function updateMarker(lat, lng) {
        if (!marker) {
            marker = L.marker([lat, lng]).addTo(map);
        } else {
            marker.setLatLng([lat, lng]);
        }
    }

    @if($angkot && $angkot->lat_sekarang && $angkot->lng_sekarang)
        updateMarker({{ $angkot->lat_sekarang }}, {{ $angkot->lng_sekarang }});
        map.setView([{{ $angkot->lat_sekarang }}, {{ $angkot->lng_sekarang }}], 15);
    @endif

    const isActive = {{ $angkot && $angkot->is_active ? 'true' : 'false' }};
    if (isActive && navigator.geolocation) {
        navigator.geolocation.watchPosition((pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            updateMarker(lat, lng);
            map.setView([lat, lng], 15);
            fetch("{{ route('driver.tracking.lokasi') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ lat, lng })
            });
        });
    }
</script>
@endpush
