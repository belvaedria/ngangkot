<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Trayek;
use App\Models\Angkot;
use App\Models\RiwayatPenumpang;
use App\Models\RuteFavorit;

class NavigasiController extends Controller
{
    /**
     * Helper: Hitung jarak Haversine dalam meter
     */
    private function hitungJarak($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Helper: Tarif per sekali naik angkot (berdasarkan jarak angkot, km)
     * Rule kamu:
     * - <= 2 km : 3000
     * - >2 s/d <=5 km : 5000
     * - > 5 km : 7000
     */
    private function hitungTarifPerAngkot(float $jarakKm): int
    {
        if ($jarakKm <= 2) return 3000;
        if ($jarakKm <= 5) return 5000;
        return 7000;
    }

    private function formatRupiah(int $angka): string
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }

    /**
     * Data dasar untuk render dashboard passenger (guest juga boleh)
     */
    private function baseDashboardData(): array
    {
        $trayeks = Trayek::where('tampil_di_menu', true)->get();

        $riwayat = Auth::check()
            ? RiwayatPenumpang::where('user_id', Auth::id())->latest()->take(6)->get()
            : collect();

        $favorit = Auth::check()
            ? RuteFavorit::where('user_id', Auth::id())->latest()->take(6)->get()
            : collect();

        return compact('trayeks', 'riwayat', 'favorit');
    }

    /**
     * Kalau kamu masih pakai /navigasi (public) terpisah, ini bisa dipakai.
     * Tapi dalam konsep kamu: navigasi = dashboard, jadi ini optional.
     */
    public function index()
    {
        $base = $this->baseDashboardData();
        return view('passenger.dashboard', $base);
    }

    /**
     * Cari rute (direct route 1 trayek) + tampilkan hasilnya di dashboard sidebar
     */
    public function searchRoute(Request $request)
    {
        // 1) Validasi input koordinat (frontend sudah isi hidden lat/lng)
        $request->validate([
            'lat_asal' => 'required|numeric',
            'lng_asal' => 'required|numeric',
            'lat_tujuan' => 'required|numeric',
            'lng_tujuan' => 'required|numeric',
            'nama_tujuan' => 'nullable|string',
            'nama_asal' => 'nullable|string',
        ]);

        $latAsal = (float) $request->lat_asal;
        $lngAsal = (float) $request->lng_asal;
        $latTujuan = (float) $request->lat_tujuan;
        $lngTujuan = (float) $request->lng_tujuan;

        // Radius "dekat" ke jalur trayek (meter)
        $radius = 350;

        $hasilPencarian = [];

        $allTrayek = Trayek::all();

        foreach ($allTrayek as $trayek) {
            $geoJson = json_decode($trayek->rute_json, true);
            if (!isset($geoJson['features'][0]['geometry']['coordinates'])) continue;

            $coords = $geoJson['features'][0]['geometry']['coordinates'];

            $titikNaik = null;
            $titikTurun = null;

            $jarakKeTitikNaik = INF;
            $jarakDariTitikTurun = INF;

            // Cari kandidat titik naik + titik turun (sederhana: cari titik terdekat dari asal, lalu cari titik terdekat dari tujuan setelah titik naik ditemukan)
            foreach ($coords as $point) {
                // GeoJSON: [lng, lat]
                $pointLng = $point[0];
                $pointLat = $point[1];

                // Jarak dari asal ke titik ini
                $jarakAsal = $this->hitungJarak($latAsal, $lngAsal, $pointLat, $pointLng);

                if ($jarakAsal <= $radius) {
                    if ($jarakAsal < $jarakKeTitikNaik) {
                        $jarakKeTitikNaik = $jarakAsal;
                        $titikNaik = [$pointLat, $pointLng];
                    }
                }

                // Jarak dari tujuan ke titik ini (boleh dicek kalau titik naik sudah ketemu)
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

            if (!$titikNaik || !$titikTurun) {
                continue;
            }

            // 3) Estimasi jarak angkot (simplifikasi: garis lurus naik->turun)
            $jarakAngkotMeter = $this->hitungJarak($titikNaik[0], $titikNaik[1], $titikTurun[0], $titikTurun[1]);
            $jarakAngkotKm = $jarakAngkotMeter / 1000;

            // Estimasi waktu:
            // - angkot: asumsi 20 km/jam
            $waktuAngkotMenit = (int) ceil(($jarakAngkotKm / 20) * 60);
            // - jalan kaki: 50 m/menit (≈ 3 km/jam)
            $waktuJalan1 = (int) ceil($jarakKeTitikNaik / 50);
            $waktuJalan2 = (int) ceil($jarakDariTitikTurun / 50);

            $totalWaktu = $waktuAngkotMenit + $waktuJalan1 + $waktuJalan2;

            // 4) Tarif per sekali naik angkot (direct route = 1x naik)
            $tarifSegmen = $this->hitungTarifPerAngkot($jarakAngkotKm);
            $tarifTotal = $tarifSegmen; // kalau nanti multi-angkot, tinggal dijumlahkan

            // 5) Angkot realtime (aktif) + ambil lokasi untuk map
            $angkotAktif = Angkot::where('trayek_id', $trayek->id)
                ->where('is_active', true)
                ->get();

            $angkotTerdekat = null;
            foreach ($angkotAktif as $a) {
                if ($a->lat_sekarang === null || $a->lng_sekarang === null) continue;

                $a->jarak_ke_pickup = $this->hitungJarak($titikNaik[0], $titikNaik[1], $a->lat_sekarang, $a->lng_sekarang);
                if (!$angkotTerdekat || $a->jarak_ke_pickup < $angkotTerdekat->jarak_ke_pickup) {
                    $angkotTerdekat = $a;
                }
            }

            $trayek->info_angkot = $angkotTerdekat
                ? "Angkot terdekat {$angkotTerdekat->plat_nomor} ± " . round($angkotTerdekat->jarak_ke_pickup) . " m dari titik naik."
                : "Tidak ada armada aktif saat ini.";

            $trayek->angkot_locations = $angkotAktif->map(function ($a) {
                return [
                    'plat_nomor' => $a->plat_nomor,
                    'lat' => $a->lat_sekarang,
                    'lng' => $a->lng_sekarang,
                ];
            })->values();

            // 6) Info ringkas untuk UI
            $trayek->info_waktu = $totalWaktu . " min";
            $trayek->info_jarak = round($jarakAngkotKm, 1) . " km";

            // Penting: simpan angka tarif juga (biar bisa dijumlah di masa depan)
            $trayek->tarif_total = $tarifTotal;
            $trayek->tarif_total_label = $this->formatRupiah($tarifTotal);

            // 7) Detail langkah (siap untuk sidebar accordion)
            $trayek->rute_detail = [
                [
                    'jenis' => 'jalan',
                    'instruksi' => 'Jalan kaki ke titik jemput',
                    'detail' => round($jarakKeTitikNaik) . " meter",
                    'waktu' => $waktuJalan1 . " min",
                ],
                [
                    'jenis' => 'angkot',
                    'instruksi' => "Naik {$trayek->nama_trayek} ({$trayek->kode_trayek})",
                    'detail' => "Turun di dekat tujuan.",
                    'warna' => $trayek->warna_angkot,
                    'waktu' => $waktuAngkotMenit . " min",
                    // tarif per sekali naik
                    'tarif' => $tarifSegmen,
                    'tarif_label' => $this->formatRupiah($tarifSegmen),
                ],
                [
                    'jenis' => 'jalan',
                    'instruksi' => 'Jalan kaki dari titik turun ke tujuan',
                    'detail' => round($jarakDariTitikTurun) . " meter",
                    'waktu' => $waktuJalan2 . " min",
                ],
            ];

            $hasilPencarian[] = $trayek;
        }

        // Sort hasil: waktu tercepat dulu, lalu tarif termurah
        usort($hasilPencarian, function ($a, $b) {
            // info_waktu format "XX min"
            $wa = (int) preg_replace('/\D+/', '', (string)($a->info_waktu ?? '999'));
            $wb = (int) preg_replace('/\D+/', '', (string)($b->info_waktu ?? '999'));
            if ($wa !== $wb) return $wa <=> $wb;

            $ta = (int) ($a->tarif_total ?? PHP_INT_MAX);
            $tb = (int) ($b->tarif_total ?? PHP_INT_MAX);
            return $ta <=> $tb;
        });

        // Simpan riwayat hanya kalau login (guest: tidak disimpan)
        if (Auth::check()) {
            RiwayatPenumpang::create([
                'user_id' => Auth::id(),
                'asal_nama' => $request->nama_asal ?? 'Lokasi Saya',
                'tujuan_nama' => $request->nama_tujuan ?? 'Tujuan',
                'asal_coords' => "$latAsal,$lngAsal",
                'tujuan_coords' => "$latTujuan,$lngTujuan",
                'rute_hasil_json' => json_encode($hasilPencarian),
            ]);
        }

        // Render dashboard yang sama, tapi dengan hasil di sidebar
        $base = $this->baseDashboardData();

        return view('passenger.dashboard', $base + [
            'hasilRute' => $hasilPencarian,
            'asal' => $request->nama_asal ?? 'Lokasi Saya',
            'tujuan' => $request->nama_tujuan ?? '',
        ]);
    }
}
