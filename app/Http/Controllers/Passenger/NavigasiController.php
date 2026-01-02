<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Trayek;
use App\Models\Angkot;
use App\Models\RiwayatPenumpang;
use App\Models\RuteFavorit;

use App\Services\RouteFinder;

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
    public function searchRoute(Request $request, RouteFinder $routeFinder)
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

        $variants = $routeFinder->findVariants(
            latAsal: $latAsal,
            lngAsal: $lngAsal,
            latTujuan: $latTujuan,
            lngTujuan: $lngTujuan,
            walkRadius: 700,
            maxVariants: 6
        );

        $hasilRute = collect($variants);


        $hasilRute = collect($variants)
            ->sortBy([
                fn ($r) => $r['total_duration_min'] ?? 999999,
                fn ($r) => $r['total_fare'] ?? 999999999,
            ])
            ->values();


        if (Auth::check()) {
            RiwayatPenumpang::create([
                'user_id' => Auth::id(),
                'asal_nama' => $request->nama_asal ?? 'Lokasi Saya',
                'tujuan_nama' => $request->nama_tujuan ?? 'Tujuan',
                'asal_coords' => "$latAsal,$lngAsal",
                'tujuan_coords' => "$latTujuan,$lngTujuan",
                'rute_hasil_json' => json_encode($hasilRute),
            ]);
        }


        // Render dashboard yang sama, tapi dengan hasil di sidebar
        $base = $this->baseDashboardData();

        return view('passenger.dashboard', $this->baseDashboardData() + [
            'hasilRute' => $hasilRute,
            'asal' => $request->nama_asal ?? 'Lokasi Saya',
            'tujuan' => $request->nama_tujuan ?? '',
        ]);

    }
}
