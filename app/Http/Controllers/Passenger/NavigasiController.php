<?php
namespace App\Http\Controllers\Passenger;
use App\Http\Controllers\Controller;
use App\Models\Trayek;
use App\Models\Angkot; 
use App\Models\RiwayatPenumpang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NavigasiController extends Controller
{
    public function index()
    {
        return view('passenger.navigasi.index'); // Form input A & B
    }

    // --- HELPER FUNCTION: Rumus Haversine (Hitung Jarak GPS) ---
    private function hitungJarak($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // Meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
    
    // --- HELPER FUNCTION: Tarif Dinamis ---
    private function hitungTarif($jarakKm) {
        if ($jarakKm < 1) return "Rp 3.000"; // Jarak dekat
        if ($jarakKm <= 5) return "Rp 5.000"; // Jarak sedang
        return "Rp 7.000"; // Jarak jauh
    }

    // --- LOGIC UTAMA: Cari Rute & Info Lengkap ---
    public function searchRoute(Request $request)
    {
        // 1. VALIDASI INPUT (Wajib Koordinat)
        $request->validate([
            'lat_asal' => 'required|numeric',
            'lng_asal' => 'required|numeric',
            'lat_tujuan' => 'required|numeric',
            'lng_tujuan' => 'required|numeric',
        ]);

        $latAsal = $request->lat_asal;
        $lngAsal = $request->lng_asal;
        $latTujuan = $request->lat_tujuan;
        $lngTujuan = $request->lng_tujuan;
        
        $radius = 500; // Toleransi jarak jalan kaki ke jalur (500 meter)
        $kecepatanAngkot = 25; // km/jam (Asumsi rata-rata dalam kota)
        $hasilPencarian = [];
        
        $allTrayek = Trayek::all(); // Ambil semua trayek (termasuk yg hidden di menu)

        foreach ($allTrayek as $trayek) {
            $geoJson = json_decode($trayek->rute_json, true);
            if (!isset($geoJson['features'][0]['geometry']['coordinates'])) continue;

            $coords = $geoJson['features'][0]['geometry']['coordinates'];
            $passStart = false; 
            $passEnd = false;
            
            // Variabel untuk nyimpen koordinat titik naik (boarding) & turun (dropoff)
            $titikNaik = null;
            $titikTurun = null;

            // 2. Cek apakah trayek ini lewat Asal & Tujuan?
            // (Logika Radius Haversine menjamin pencarian berdasarkan POSISI, bukan NAMA JALAN)
            foreach ($coords as $point) {
                $pointLat = $point[1]; $pointLng = $point[0];
                
                // Cek dekat titik asal
                if (!$passStart && $this->hitungJarak($latAsal, $lngAsal, $pointLat, $pointLng) <= $radius) {
                    $passStart = true;
                    $titikNaik = [$pointLat, $pointLng];
                }
                
                // Cek dekat titik tujuan (Hanya jika titik awal sudah ketemu -> Menjamin Arah Maju)
                if ($passStart && !$passEnd && $this->hitungJarak($latTujuan, $lngTujuan, $pointLat, $pointLng) <= $radius) {
                    $passEnd = true;
                    $titikTurun = [$pointLat, $pointLng];
                    break; // Hemat looping
                }
            }

            // 3. Kalo MATCH, hitung informasi detailnya
            if ($passStart && $passEnd) {
                
                // A. Hitung Jarak Tempuh Angkot
                $jarakPerjalanan = $this->hitungJarak($titikNaik[0], $titikNaik[1], $titikTurun[0], $titikTurun[1]); 
                $jarakKM = $jarakPerjalanan / 1000;

                // B. Hitung Tarif & Waktu
                $tarif = $this->hitungTarif($jarakKM);
                $waktuTempuh = ceil(($jarakKM / $kecepatanAngkot) * 60); 

                // C. Cari Angkot Terdekat (Real-time!)
                $angkotTerdekat = Angkot::where('trayek_id', $trayek->id)
                                    ->where('is_active', true)
                                    ->get()
                                    ->map(function($angkot) use ($latAsal, $lngAsal) {
                                        // Hitung jarak angkot ke User
                                        $jarak = $this->hitungJarak($latAsal, $lngAsal, $angkot->lat_sekarang, $angkot->lng_sekarang);
                                        $angkot->jarak_ke_user = $jarak;
                                        return $angkot;
                                    })
                                    ->sortBy('jarak_ke_user')
                                    ->first();

                // Format info angkot terdekat
                $infoAngkot = "Tidak ada angkot aktif";
                if ($angkotTerdekat) {
                    $waktuTunggu = ceil(($angkotTerdekat->jarak_ke_user / 1000 / $kecepatanAngkot) * 60);
                    $infoAngkot = "Angkot {$angkotTerdekat->plat_nomor} berjarak " . round($angkotTerdekat->jarak_ke_user) . "m ({$waktuTunggu} menit lagi)";
                }

                // D. Susun Data STEPS (Ini yang dibaca View untuk Accordion)
                $jarakJalanKakiAwal = $this->hitungJarak($latAsal, $lngAsal, $titikNaik[0], $titikNaik[1]);
                $jarakJalanKakiAkhir = $this->hitungJarak($titikTurun[0], $titikTurun[1], $latTujuan, $lngTujuan);

                $trayek->rute_detail = [
                    [
                        'jenis' => 'jalan',
                        'instruksi' => 'Jalan kaki ke jalur lintasan',
                        'detail' => round($jarakJalanKakiAwal) . ' meter',
                        'waktu' => ceil($jarakJalanKakiAwal / 80) . ' menit' // Asumsi jalan kaki 80m/menit
                    ],
                    [
                        'jenis' => 'angkot',
                        'kode' => $trayek->kode_trayek,
                        'nama' => $trayek->nama_trayek,
                        'warna' => $trayek->warna_angkot,
                        'instruksi' => "Naik Angkot {$trayek->kode_trayek}",
                        'detail' => "Jarak tempuh: " . round($jarakKM, 1) . " Km",
                        'tarif' => $tarif,
                        'live_info' => $infoAngkot
                    ],
                    [
                        'jenis' => 'turun',
                        'instruksi' => 'Turun di dekat tujuan',
                        'detail' => round($jarakJalanKakiAkhir) . ' meter jalan kaki ke lokasi',
                        'waktu' => ceil($jarakJalanKakiAkhir / 80) . ' menit'
                    ]
                ];

                // Inject data summary buat Card Header
                $trayek->info_tarif = $tarif;
                $trayek->info_waktu = ($waktuTempuh + ceil($jarakJalanKakiAwal/80) + ceil($jarakJalanKakiAkhir/80)) . " Menit";
                $trayek->info_angkot = $infoAngkot;
                $trayek->total_skor = $waktuTempuh; // Buat sorting

                $hasilPencarian[] = $trayek;
            }
        }
        
        // 4. Sorting (Tercepat paling atas)
        usort($hasilPencarian, function($a, $b) {
            return $a->total_skor <=> $b->total_skor;
        });

        // 5. Simpan History
        RiwayatPenumpang::create([
            'user_id' => Auth::id(),
            'asal_nama' => $request->nama_asal,
            'tujuan_nama' => $request->nama_tujuan,
            'asal_coords' => "$latAsal,$lngAsal",
            'tujuan_coords' => "$latTujuan,$lngTujuan",
            'rute_hasil_json' => json_encode($hasilPencarian)
        ]);

        return view('passenger.navigasi.result', ['trayeks' => $hasilPencarian]);
    }
}
