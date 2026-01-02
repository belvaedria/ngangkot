@extends('layouts.app')

@section('title', 'Pusat Edukasi Ngangkot')

@section('content')
<div class="bg-slate-50 min-h-screen py-12 px-6">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-slate-900 mb-4">Pusat Edukasi Ngangkot</h1>
            <p class="text-slate-600 text-lg mb-8">Temukan tips berguna dan jawaban seputar angkot untuk perjalanan yang aman dan nyaman.</p>
            
            <div class="max-w-2xl mx-auto relative">
                <input type="text" 
                       placeholder="Cari jawaban seputar angkot..." 
                       class="w-full px-6 py-4 rounded-full border-2 border-blue-100 focus:border-blue-500 focus:outline-none shadow-sm text-lg transition-all"
                >
                <button class="absolute right-3 top-2 bottom-2 bg-blue-600 text-white px-6 rounded-full hover:bg-blue-700 transition">
                    Cari
                </button>
            </div>
        </div>

        <div class="flex justify-center gap-4 mb-12">
            <button class="px-8 py-2 bg-blue-600 text-white rounded-full font-semibold shadow-md">Tips</button>
            <button class="px-8 py-2 bg-white text-slate-600 border border-slate-200 rounded-full font-semibold hover:bg-slate-100 transition">Panduan</button>
            <button class="px-8 py-2 bg-white text-slate-600 border border-slate-200 rounded-full font-semibold hover:bg-slate-100 transition">FAQ</button>
        </div>

        <div class="grid md:grid-cols-3 gap-8">

            <div class="group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100">
                <div class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4 text-left">Tips Aman & Nyaman</h3>
                <ul class="text-slate-600 text-sm space-y-3">
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 font-bold">•</span> Selalu dekap tas di depan tubuh saat duduk.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 font-bold">•</span> Siapkan uang pas sebelum sampai tujuan.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 font-bold">•</span> Hindari bermain HP di dekat pintu angkot.
                    </li>
                </ul>
            </div>

            <div class="group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100">
                <div class="w-14 h-14 bg-emerald-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4">Panduan Menarik Angkot</h3>
                <ul class="text-slate-600 text-sm space-y-3">
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-500 font-bold">•</span> Berdiri di tempat aman (bukan tikungan/turunan).
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-500 font-bold">•</span> Lambaikan tangan kiri ke arah jalan.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-500 font-bold">•</span> Pastikan angkot benar-benar berhenti.
                    </li>
                </ul>
            </div>

            <div class="group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100">
                <div class="w-14 h-14 bg-amber-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-amber-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.444 1.103m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4">FAQ (Tanya Jawab)</h3>
                <ul class="text-slate-600 text-sm space-y-3">
                    <li class="flex items-start gap-2 italic">
                        "Berapa tarif jarak dekat?" <br>— Umumnya Rp 3.000 - Rp 5.000.
                    </li>
                    <li class="flex items-start gap-2 italic">
                        "Sampai jam berapa angkot beroperasi?" <br>— Sebagian besar hingga jam 21.00 WIB.
                    </li>
                </ul>
                <a href="#" class="mt-4 block text-blue-600 font-semibold text-sm hover:underline">Lihat semua pertanyaan →</a>
            </div>

        </div>
    </div>
</div>
@endsection