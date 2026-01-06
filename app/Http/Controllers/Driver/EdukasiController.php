<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\Faq;

class EdukasiController extends Controller
{
    public function index()
    {
        // Ambil artikel berdasarkan kategori
        $tips = Artikel::where('kategori', 'tips')->latest()->get();
        $panduan = Artikel::where('kategori', 'panduan')->latest()->get();
        
        // FAQ dari artikel (kategori faq)
        $faqArtikels = Artikel::where('kategori', 'faq')->latest()->get();
        
        // FAQ dari tabel faqs (untuk backward compatibility)
        $faqs = Faq::whereIn('target', ['driver', 'umum'])->get();
        
        return view('driver.edukasi.index', compact('tips', 'panduan', 'faqArtikels', 'faqs'));
    }
}
