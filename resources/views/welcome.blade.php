@extends('layouts.app') 

@section('title', 'Ngangkot - Solusi Transportasi Bandung')

@section('content')
<div class="relative overflow-hidden">
    
    <!-- HERO SECTION -->
    <section class="relative pt-12 pb-24 lg:pt-28 lg:pb-40 px-6">
        <!-- Background Blob Decoration -->
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-400/10 rounded-full blur-3xl -z-10 translate-x-1/3 -translate-y-1/4"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-400/10 rounded-full blur-3xl -z-10 -translate-x-1/4 translate-y-1/4"></div>

        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
            
            <!-- Teks Hero -->
            <div class="space-y-8 z-10 text-center lg:text-left animate-in">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white border border-slate-200 rounded-full shadow-sm mb-4">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-600">Bandung Smart City</span>
                </div>

                <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-900 leading-[1.1] tracking-tight">
                    Jelajahi Kota <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Lebih Mudah.</span>
                </h1>
                
                <p class="text-lg text-slate-600 font-medium leading-relaxed max-w-lg mx-auto lg:mx-0">
                    Temukan rute angkot terbaik, pantau posisi armada secara <i>real-time</i>, dan dapatkan tips aman berkendara di Bandung.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                    <!-- Tombol Scroll ke Fitur -->
                    <a href="#fitur" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-bold text-sm shadow-xl shadow-slate-900/20 hover:bg-slate-800 hover:-translate-y-1 transition-all flex items-center gap-2 w-full sm:w-auto justify-center">
                        Layanan Kami <i data-lucide="arrow-down" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Visual Hero (Card Mockup) -->
            <div class="relative lg:h-[600px] flex items-center justify-center animate-in" style="animation-delay: 0.2s">
                <div class="relative w-full max-w-md bg-white p-2 rounded-[3rem] shadow-2xl border-4 border-slate-50 rotate-[-3deg] hover:rotate-0 transition-transform duration-700">
                    <div class="bg-slate-100 rounded-[2.5rem] overflow-hidden h-[500px] relative">
                        <!-- Peta Statis -->
                        <div id="hero-map" class="absolute inset-0 w-full h-full opacity-60 mix-blend-multiply"></div>
                        
                        <!-- Elemen UI Mockup -->
                        <div class="absolute top-8 left-6 right-6 space-y-3">
                            <div class="bg-white/90 backdrop-blur-md p-4 rounded-2xl shadow-sm flex items-center gap-3 border border-white/50">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">Dari</p>
                                    <p class="text-sm font-bold text-slate-800">Alun-Alun Bandung</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Angkot Floating -->
                        <div class="absolute bottom-6 left-6 right-6">
                            <div class="bg-slate-900 text-white p-5 rounded-[2rem] shadow-xl">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs font-bold text-slate-400">Rute Terbaik</span>
                                    <span class="bg-green-500 text-xs px-2 py-0.5 rounded text-slate-900 font-bold">Cepat</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center text-white text-lg font-black shadow-lg">
                                        05
                                    </div>
                                    <div>
                                        <p class="font-bold">Cicaheum - Ledeng</p>
                                        <p class="text-xs text-slate-400">Rp 5.000 • 15 min</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="fitur" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="text-blue-600 font-bold text-sm uppercase tracking-widest mb-2 block">Layanan Kami</span>
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-6 tracking-tight">Semua Kebutuhan Perjalanan.</h2>
                <p class="text-slate-500 text-lg leading-relaxed font-medium">
                    Fitur lengkap untuk memudahkan mobilitas harian Anda di Kota Bandung.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Fitur 1: Navigasi (Cari Rute) -->
                <a href="{{ route('passenger.dashboard') }}" class="group bg-slate-50 p-8 rounded-[2.5rem] hover:bg-white hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-slate-100 block">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-[1.5rem] flex items-center justify-center mb-8 group-hover:scale-110 transition-transform shadow-sm">
                        <i data-lucide="map" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Cari Rute</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6 font-medium">Temukan angkot yang tepat dari titik A ke B dengan estimasi harga & waktu.</p>
                    <span class="text-blue-600 font-black text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
                        Coba Sekarang <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </span>
                </a>

                <!-- Fitur 2: Lihat Trayek -->
                <a href="{{ route('passenger.trayek.index') }}" class="group bg-slate-50 p-8 rounded-[2.5rem] hover:bg-white hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-slate-100 block">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-[1.5rem] flex items-center justify-center mb-8 group-hover:scale-110 transition-transform shadow-sm">
                        <i data-lucide="signpost" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Info Trayek</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6 font-medium">Data lengkap seluruh rute angkot Bandung dan posisi armada realtime.</p>
                    <span class="text-emerald-600 font-black text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
                        Lihat Peta <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </span>
                </a>
                
                <!-- Fitur 3: Edukasi -->
                <a href="{{ route('passenger.edukasi.index') }}" class="group bg-slate-50 p-8 rounded-[2.5rem] hover:bg-white hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-slate-100 block">
                    <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-[1.5rem] flex items-center justify-center mb-8 group-hover:scale-110 transition-transform shadow-sm">
                        <i data-lucide="book-open" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Edukasi</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6 font-medium">Tips aman, panduan, dan FAQ untuk pengalaman ngangkot yang lebih baik.</p>
                    <span class="text-amber-600 font-black text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
                        Baca Tips <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </span>
                </a>
            </div>
            
            <!-- Banner Ajakan Login di Bawah -->
            <div class="mt-24 bg-slate-900 rounded-[3rem] p-10 md:p-20 text-center text-white relative overflow-hidden shadow-2xl">
                <div class="relative z-10">
                    <h2 class="text-3xl md:text-5xl font-black mb-6 tracking-tight">Siap Menjelajah Bandung?</h2>
                    <p class="text-slate-400 mb-10 text-lg max-w-xl mx-auto font-medium">Bergabung dengan ribuan wargi lainnya yang sudah beralih ke transportasi cerdas.</p>
                    <a href="{{ route('login') }}" class="inline-block bg-white text-slate-900 px-12 py-4 rounded-2xl font-bold text-sm hover:bg-blue-50 transition transform hover:-translate-y-1 shadow-xl">
                        Masuk ke Aplikasi
                    </a>
                </div>
                <!-- Dekorasi -->
                <div class="absolute top-0 left-0 w-full h-full opacity-20 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-blue-500 via-slate-900 to-slate-900"></div>
            </div>
        </div>
    </section>

</div>

@push('scripts')
<script>
    // Map Mini di Hero Section
    var map = L.map('hero-map', { 
        zoomControl: false, 
        attributionControl: false,
        dragging: false, 
        scrollWheelZoom: false 
    }).setView([-6.917464, 107.619122], 13);
    
    L.tileLayer('https://{s}[.basemaps.cartocdn.com/light_all/](https://.basemaps.cartocdn.com/light_all/){z}/{x}/{y}{r}.png', {
        maxZoom: 19
    }).addTo(map);

    // Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
@endpush
@endsection
