<?php
namespace App\Http\Controllers\Passenger;
use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\Faq;

class EdukasiController extends Controller
{
    public function index() {
        $tips = Artikel::where('kategori', 'tips')->latest()->get();
        $faqs = Faq::whereIn('target', ['penumpang', 'umum'])->get();
        return view('passenger.edukasi.index', compact('tips', 'faqs'));
    }
}
