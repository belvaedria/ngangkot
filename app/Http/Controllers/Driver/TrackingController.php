<?php
namespace App\Http\Controllers\Driver;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Angkot;
use App\Models\RiwayatPengemudi;
use Carbon\Carbon;
use App\Models\Angkot;


class TrackingController extends Controller
{
    public function index() {
        $angkot = Angkot::where('user_id', Auth::id())->first();
        $sesiAktif = RiwayatPengemudi::where('user_id', Auth::id())->whereNull('waktu_selesai')->latest()->first();
        $availableAngkots = Angkot::whereNull('user_id')->get();
        
        return view('driver.tracking.index', compact('angkot', 'availableAngkots', 'sesiAktif'));
    }

    public function pilihAngkot(Request $request) {
        $request->validate(['angkot_id' => 'required|exists:angkots,id']);
        $angkot = Angkot::find($request->angkot_id);
        if($angkot->user_id) return back()->with('error', 'Angkot sudah dipakai');

        Angkot::where('user_id', Auth::id())->update(['user_id' => null, 'is_active' => false]);
        $angkot->update(['user_id' => Auth::id()]);
        return back()->with('success', 'Angkot dipilih.');
    }

    public function updateStatus(Request $request) {
        $angkot = Angkot::where('user_id', Auth::id())->first();
        if(!$angkot) return back()->with('error', 'Pilih angkot dulu.');

        if ($request->status == 'aktif') {
            // MULAI NARIK
            $angkot->update(['is_active' => true]);
            
            // Catat Riwayat (Start)
            RiwayatPengemudi::create([
                'user_id' => Auth::id(),
                'angkot_id' => $angkot->id,
                'waktu_mulai' => Carbon::now(),
                'jarak_tempuh_km' => 0,
                'path_history_json' => [] // Init array kosong
            ]);
            
            return back()->with('success', 'Mulai Narik! Lokasi direkam.');
        } else {
            // BERHENTI NARIK
            $angkot->update(['is_active' => false]);
            
            $sesi = RiwayatPengemudi::where('user_id', Auth::id())->whereNull('waktu_selesai')->latest()->first();
            if($sesi) {
                $sesi->update(['waktu_selesai' => Carbon::now()]);
            }
            
            return back()->with('success', 'Selesai Narik.');
        }
    }

    // --- LOGIC REKAM JEJAK GPS (POLYLINE) ---
    public function updateLokasi(Request $request) {
        $request->validate(['lat' => 'required', 'lng' => 'required']);

        // 1. Update Posisi Angkot (Buat Penumpang)
        $angkot = Angkot::where('user_id', Auth::id())->where('is_active', true)->first();
        
        if ($angkot) {
            $angkot->update([
                'lat_sekarang' => $request->lat,
                'lng_sekarang' => $request->lng
            ]);

            // 2. Simpan ke Riwayat (Buat Laporan Driver)
            $sesi = RiwayatPengemudi::where('user_id', Auth::id())->whereNull('waktu_selesai')->latest()->first();
            
            if ($sesi) {
                $logs = $sesi->path_history_json ?? []; // Ambil log lama
                
                // Hitung jarak dari titik sebelumnya (kalau ada)
                if (count($logs) > 0) {
                    $lastPoint = end($logs);
                    $jarakBaru = $this->hitungJarak($lastPoint[0], $lastPoint[1], $request->lat, $request->lng); // Meter
                    $sesi->increment('jarak_tempuh_km', $jarakBaru / 1000);
                }

                // Tambahkan titik baru
                $logs[] = [(float)$request->lat, (float)$request->lng];
                
                $sesi->update(['path_history_json' => $logs]);
            }

            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error'], 400);
    }
    
    // Copy Helper Haversine ke sini juga biar bisa dipake updateLokasi
    private function hitungJarak($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; 
        $dLat = deg2rad($lat2 - $lat1); $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
}
