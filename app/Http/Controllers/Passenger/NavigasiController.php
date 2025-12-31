<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trayek;
use App\Models\Angkot;
use App\Models\RiwayatPenumpang;
use App\Models\RuteFavorit;
use Illuminate\Support\Facades\Auth;

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

    public function index()
    {
        $trayeks = Trayek::where('tampil_di_menu', true)->get();
        $riwayat = Auth::check()
            ? RiwayatPenumpang::where('user_id', Auth::id())->latest()->take(5)->get()
            : collect();
        $favorit = Auth::check()
            ? RuteFavorit::where('user_id', Auth::id())->latest()->take(5)->get()
            : collect();

        return view('passenger.dashboard', compact('trayeks', 'riwayat', 'favorit'));
    }

    /**
     * LOGIKA INTI PENCARIAN RUTE
     * Menerima Lat/Long Asal & Tujuan -> Mencocokkan dengan JSON Trayek di Database
     */
    public function searchRoute(Request $request)
    {
        // 1. Validasi: Pastikan Frontend mengirim Koordinat
        $request->validate([
            'lat_asal' => 'required|numeric',
            'lng_asal' => 'required|numeric',
            'lat_tujuan' => 'required|numeric',
            'lng_tujuan' => 'required|numeric',
            'nama_asal' => 'required',
            'nama_tujuan' => 'required'
        ]);

        $latAsal = $request->lat_asal;
        $lngAsal = $request->lng_asal;
        $latTujuan = $request->lat_tujuan;
        $lngTujuan = $request->lng_tujuan;

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

                // Simpan armada aktif untuk ditampilkan di peta
                $trayek->angkot_locations = $angkotAktif->map(function($a) {
                    return [
                        'plat_nomor' => $a->plat_nomor,
                        'lat' => $a->lat_sekarang,
                        'lng' => $a->lng_sekarang,
                    ];
                })->values();

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

        // Return ke View Hasil
        return view('passenger.result', ['trayeks' => $hasilPencarian]);
    }
}
