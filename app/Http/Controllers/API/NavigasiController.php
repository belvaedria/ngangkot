<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\RouteFinder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NavigasiController extends Controller
{
    public function searchRoute(
        Request $request,
        RouteFinder $routeFinder
    ): JsonResponse {
        $validated = $request->validate([
            'lat_asal' => 'required|numeric|between:-90,90',
            'lng_asal' => 'required|numeric|between:-180,180',
            'lat_tujuan' => 'required|numeric|between:-90,90',
            'lng_tujuan' => 'required|numeric|between:-180,180',
            'nama_asal' => 'nullable|string|max:255',
            'nama_tujuan' => 'nullable|string|max:255',
        ]);

        $variants = $routeFinder->findVariants(
            latAsal: (float) $validated['lat_asal'],
            lngAsal: (float) $validated['lng_asal'],
            latTujuan: (float) $validated['lat_tujuan'],
            lngTujuan: (float) $validated['lng_tujuan'],
            walkRadius: 700,
            maxVariants: 6
        );

        return response()->json([
            'success' => true,
            'message' => count($variants) > 0
                ? 'Rute berhasil ditemukan.'
                : 'Tidak ditemukan rute yang sesuai.',
            'data' => [
                'asal' => [
                    'nama' => $validated['nama_asal'] ?? 'Lokasi Saya',
                    'lat' => (float) $validated['lat_asal'],
                    'lng' => (float) $validated['lng_asal'],
                ],
                'tujuan' => [
                    'nama' => $validated['nama_tujuan'] ?? 'Tujuan',
                    'lat' => (float) $validated['lat_tujuan'],
                    'lng' => (float) $validated['lng_tujuan'],
                ],
                'routes' => $variants,
            ],
        ]);
    }
}
