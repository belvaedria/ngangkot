<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trayek;
use App\Models\Angkot;
use App\Models\RiwayatPenumpang;
use App\Services\RouteFinder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NavigasiController extends Controller
{
    // Helper: Hitung Jarak (Haversine Formula) dalam Meter
    private function hitungJarak($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; 
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }

    // Helper: Estimasi Tarif
    private function hitungTarif($jarakKm) {
        if ($jarakKm < 1) return "Rp 3.000";
        if ($jarakKm <= 5) return "Rp 5.000";
        return "Rp 7.000";
    }

    public function index(Request $request)
    {
        // Pastikan panel "Trayek Aktif" hanya menampilkan trayek yang diaktifkan untuk menu.
        $trayeks = \App\Models\Trayek::where('tampil_di_menu', true)->get();
        // Optional prefill when coming from favorites (query params).
        $prefill = $request->only(['lat_asal','lng_asal','nama_asal','lat_tujuan','lng_tujuan','nama_tujuan','asal_coords','tujuan_coords']);
        // If client passed coords as "lat,lng" pairs (asal_coords / tujuan_coords), split them for the form.
        if (!empty($prefill['asal_coords']) && empty($prefill['lat_asal'])) {
            [$plat, $plng] = explode(',', $prefill['asal_coords']) + [null,null];
            $prefill['lat_asal'] = $plat ?? null;
            $prefill['lng_asal'] = $plng ?? null;
        }
        if (!empty($prefill['tujuan_coords']) && empty($prefill['lat_tujuan'])) {
            [$dlat, $dlng] = explode(',', $prefill['tujuan_coords']) + [null,null];
            $prefill['lat_tujuan'] = $dlat ?? null;
            $prefill['lng_tujuan'] = $dlng ?? null;
        }
        return view('passenger.navigasi.index', compact('trayeks','prefill'));
    }

    /**
     * LOGIKA INTI PENCARIAN RUTE
     * Menerima Lat/Long Asal & Tujuan -> Mencocokkan dengan JSON Trayek di Database
     */
    public function searchRoute(Request $request)
    {
        // 1. Validasi: Pastikan frontend mengirim koordinat tujuan; asal bersifat opsional
        $request->validate([
            'lat_tujuan' => 'required|numeric',
            'lng_tujuan' => 'required|numeric',
            'nama_tujuan' => 'required'
        ]);

        // Origin optional: jika tidak diberikan, gunakan pusat kota Bandung sebagai fallback
        $centerLat = -6.917464; $centerLng = 107.619122;
        $latAsal = $request->lat_asal ?? null;
        $lngAsal = $request->lng_asal ?? null;
        $latTujuan = $request->lat_tujuan;
        $lngTujuan = $request->lng_tujuan;

        // If origin missing, fallback to city center (allows search without requiring geolocation)
        if (empty($latAsal) || empty($lngAsal)) {
            $latAsal = $centerLat;
            $lngAsal = $centerLng;
        }

        $request->nama_asal = $request->nama_asal ?? 'Pusat Kota Bandung';



        // 1) First try: flexible RouteFinder (multi-transfer, several variants)
        $variants = [];
        try {
            $finder = new RouteFinder();
            $variants = $finder->findVariants($latAsal, $lngAsal, $latTujuan, $lngTujuan, 700);
        } catch (\Throwable $e) {
            // silently fallback to old logic below
            $variants = [];
        }

        if (!empty($variants)) {
            // Simpan Riwayat jika user login
            if (Auth::check()) {
                RiwayatPenumpang::create([
                    'user_id' => Auth::id(),
                    'asal_nama' => $request->nama_asal ?? 'Lokasi Saya',
                    'tujuan_nama' => $request->nama_tujuan,
                    'asal_coords' => "$latAsal,$lngAsal",
                    'tujuan_coords' => "$latTujuan,$lngTujuan",
                    'rute_hasil_json' => json_encode($variants)
                ]);
            }

            return view('passenger.navigasi.result', ['trayeks' => $variants]);
        }

        // 2) Fallback: original simple matching (single-trayek)
        $radius = 700; // Toleransi jarak jalan kaki (meter) ke jalur angkot
        $hasilPencarian = [];
        
        // Ambil semua trayek dari database
        $allTrayek = Trayek::all();

        foreach ($allTrayek as $trayek) {
            // Decode Rute JSON (Garis jalan angkot)
            $geoJson = json_decode($trayek->rute_json, true);
            
            // Skip jika data rute rusak/kosong
            if (!isset($geoJson['features'][0]['geometry']['coordinates'])) continue;

            $coords = $geoJson['features'][0]['geometry']['coordinates'];
            
            $titikNaik = null;
            $titikTurun = null;
            $jarakKeTitikNaik = 999999;
            $jarakDariTitikTurun = 999999;

            // 2. CEK LOGIKA: Apakah trayek ini lewat dekat Asal DAN dekat Tujuan?
            foreach ($coords as $point) {
                // GeoJSON biasanya [Longitude, Latitude]
                $pointLat = $point[1]; 
                $pointLng = $point[0];

                // Cek jarak titik ini ke Posisi Awal User
                $jarakAsal = $this->hitungJarak($latAsal, $lngAsal, $pointLat, $pointLng);
                if ($jarakAsal <= $radius) {
                    // Simpan titik naik terdekat
                    if ($jarakAsal < $jarakKeTitikNaik) {
                        $jarakKeTitikNaik = $jarakAsal;
                        $titikNaik = [$pointLat, $pointLng];
                    }
                }

                // Cek jarak titik ini ke Posisi Tujuan User
                // Hanya cek jika titik naik sudah ditemukan (Logic arah maju)
                if ($titikNaik) {
                    $jarakTujuan = $this->hitungJarak($latTujuan, $lngTujuan, $pointLat, $pointLng);
                    if ($jarakTujuan <= $radius) {
                        if ($jarakTujuan < $jarakDariTitikTurun) {
                            $jarakDariTitikTurun = $jarakTujuan;
                            $titikTurun = [$pointLat, $pointLng];
                        }
                    }
                }
            }

            // 3. JIKA RUTE COCOK (Ada titik naik & titik turun)
            if ($titikNaik && $titikTurun) {
                
                // Hitung total jarak angkot (garis lurus antar titik - simplifikasi)
                $jarakAngkot = $this->hitungJarak($titikNaik[0], $titikNaik[1], $titikTurun[0], $titikTurun[1]);
                $jarakKm = $jarakAngkot / 1000;
                
                // Hitung estimasi
                $waktuAngkot = ceil(($jarakKm / 20) * 60); // Asumsi 20km/jam
                $waktuJalanKaki = ceil(($jarakKeTitikNaik + $jarakDariTitikTurun) / 50); // 50m/menit
                $totalWaktu = $waktuAngkot + $waktuJalanKaki;

                // Cari Angkot Realtime (Yang is_active = true di DB)
                $angkotAktif = Angkot::where('trayek_id', $trayek->id)
                                    ->where('is_active', true)
                                    ->get();
                
                // Cari yang paling dekat dengan titik naik
                $angkotTerdekat = $angkotAktif->map(function($a) use ($titikNaik) {
                    $a->jarak_ke_pickup = $this->hitungJarak($titikNaik[0], $titikNaik[1], $a->lat_sekarang, $a->lng_sekarang);
                    return $a;
                })->sortBy('jarak_ke_pickup')->first();

                $infoAngkot = $angkotTerdekat 
                    ? "Angkot {$angkotTerdekat->plat_nomor} berjarak " . round($angkotTerdekat->jarak_ke_pickup) . "m dari titik naik."
                    : "Tidak ada armada aktif saat ini.";

                // Siapkan Data untuk View
                $trayek->info_tarif = $this->hitungTarif($jarakKm);
                $trayek->info_waktu = $totalWaktu . " min";
                $trayek->info_jarak = round($jarakKm, 1) . " km";
                $trayek->info_angkot = $infoAngkot;
                
                // Struktur Detail Perjalanan (Steps)
                $trayek->rute_detail = [
                    [
                        'jenis' => 'jalan',
                        'instruksi' => 'Jalan kaki ke titik jemput',
                        'detail' => round($jarakKeTitikNaik) . " meter",
                        'waktu' => ceil($jarakKeTitikNaik / 50) . " min"
                    ],
                    [
                        'jenis' => 'angkot',
                        'instruksi' => "Naik {$trayek->nama_trayek} ({$trayek->kode_trayek})",
                        'detail' => "Turun di dekat tujuan.",
                        'warna' => $trayek->warna_angkot,
                        'waktu' => $waktuAngkot . " min"
                    ],
                    [
                        'jenis' => 'turun',
                        'instruksi' => 'Jalan kaki ke tujuan',
                        'detail' => round($jarakDariTitikTurun) . " meter",
                        'waktu' => ceil($jarakDariTitikTurun / 50) . " min"
                    ]
                ];

                $hasilPencarian[] = $trayek;
            }
        }

        // Simpan Riwayat jika user login
        if (Auth::check()) {
            RiwayatPenumpang::create([
                'user_id' => Auth::id(),
                'asal_nama' => $request->nama_asal ?? 'Lokasi Saya',
                'tujuan_nama' => $request->nama_tujuan,
                'asal_coords' => "$latAsal,$lngAsal",
                'tujuan_coords' => "$latTujuan,$lngTujuan",
                'rute_hasil_json' => json_encode($hasilPencarian)
            ]);
        }

        $message = null;
        if (empty($hasilPencarian)) {
            $message = 'Rute menuju tujuan belum tersedia. Coba titik asal/tujuan lain atau cek trayek terdekat.';
            // Return as an alert popup on the navigasi page (so it's a modal, not a separate page)
            return redirect()->route('navigasi.index')->withInput()->with('alert', $message);
        }

        // Return ke View Hasil
        return view('passenger.navigasi.result', ['trayeks' => $hasilPencarian]);
    }

    /**
     * Search places using Mapbox (if configured) with a fallback to Nominatim.
     * Results are cached briefly to reduce external API calls.
     */
    public function places(Request $request)
    {
        $q = $request->query('q', '');
        $qTrim = trim($q);
        if (strlen($qTrim) < 1) {
            return response()->json([]);
        }

        // Try Mapbox first if user configured an access token
        $mapboxToken = env('MAPBOX_ACCESS_TOKEN');
        if ($mapboxToken) {
            $cacheKey = 'places:mapbox:' . md5($qTrim);
            $results = Cache::remember($cacheKey, 60, function() use ($qTrim, $mapboxToken) {
                $url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode($qTrim) . '.json';
                $resp = Http::get($url, [
                    'access_token' => $mapboxToken,
                    'limit' => 12,
                    'autocomplete' => 'true',
                    'types' => 'place,locality,neighborhood,poi,address'
                ]);
                if (!$resp->successful()) return [];
                return $resp->json();
            });

            $out = [];
            if (isset($results['features']) && is_array($results['features'])) {
                foreach ($results['features'] as $f) {
                    $center = $f['center'] ?? null; // [lng, lat]
                    $out[] = [
                        'name' => $f['place_name'] ?? ($f['text'] ?? ''),
                        'lat' => $center ? $center[1] : null,
                        'lng' => $center ? $center[0] : null,
                        'address' => $f['place_name'] ?? '',
                        'type' => isset($f['place_type']) ? implode(',', $f['place_type']) : null,
                        'source' => 'mapbox'
                    ];
                }

                if (!empty($out)) {
                    return response()->json($out);
                }
                // If Mapbox returned empty results, try LocationIQ if configured
                $locationIqKey = env('LOCATIONIQ_ACCESS_TOKEN');
                if ($locationIqKey) {
                    $cacheKey = 'places:locationiq:' . md5($qTrim);
                    $liResults = Cache::remember($cacheKey, 60, function() use ($qTrim, $locationIqKey) {
                        $resp = Http::get('https://us1.locationiq.com/v1/search.php', [
                            'key' => $locationIqKey,
                            'q' => $qTrim,
                            'format' => 'json',
                            'limit' => 12,
                        ]);
                        if (!$resp->successful()) return [];
                        return $resp->json();
                    });

                    if (is_array($liResults) && !empty($liResults)) {
                        $out = [];
                        foreach ($liResults as $r) {
                            $out[] = [
                                'name' => $r['display_name'] ?? '',
                                'lat' => $r['lat'] ?? null,
                                'lng' => $r['lon'] ?? null,
                                'address' => $r['display_name'] ?? '',
                                'type' => $r['type'] ?? null,
                                'source' => 'locationiq'
                            ];
                        }

                        if (!empty($out)) {
                            return response()->json($out);
                        }
                    }
                }
                // otherwise fallthrough to Nominatim
            }
        }

        // Fallback: Nominatim
        $cacheKey = 'places:nominatim:' . md5($qTrim);
        $results = Cache::remember($cacheKey, 60, function() use ($qTrim) {
            $resp = Http::withHeaders(['User-Agent' => 'pabw-ngangkot/1.0'])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $qTrim,
                'format' => 'jsonv2',
                'addressdetails' => 1,
                'limit' => 12,
            ]);
            if (!$resp->successful()) return [];
            return $resp->json();
        });

        $out = [];
        foreach ($results as $r) {
            $out[] = [
                'name' => $r['display_name'] ?? ($r['name'] ?? ''),
                'lat' => $r['lat'] ?? null,
                'lng' => $r['lon'] ?? ($r['lng'] ?? null),
                'address' => $r['display_name'] ?? '',
                'type' => $r['type'] ?? null,
                'source' => 'nominatim'
            ];
        }

        return response()->json($out);
    }
}