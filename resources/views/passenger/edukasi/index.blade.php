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
                       id="searchInput"
                       placeholder="Cari jawaban seputar angkot..." 
                       class="w-full px-6 py-4 rounded-full border-2 border-blue-100 focus:border-blue-500 focus:outline-none shadow-sm text-lg transition-all"
                >
                <button id="searchButton" class="absolute right-3 top-2 bottom-2 bg-blue-600 text-white px-6 rounded-full hover:bg-blue-700 transition">
                    Cari
                </button>
            </div>
        </div>

        <div class="flex justify-center gap-4 mb-12">
            <button data-tab="tips" class="tab-button px-8 py-2 bg-blue-600 text-white rounded-full font-semibold shadow-md transition">Tips</button>
            <button data-tab="panduan" class="tab-button px-8 py-2 bg-white text-slate-600 border border-slate-200 rounded-full font-semibold hover:bg-slate-100 transition">Panduan</button>
            <button data-tab="faq" class="tab-button px-8 py-2 bg-white text-slate-600 border border-slate-200 rounded-full font-semibold hover:bg-slate-100 transition">FAQ</button>
        </div>

        <div id="cardsContainer" class="grid md:grid-cols-3 gap-8">

            <!-- TIPS -->
            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="tips">
                <div class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4 text-left">Tips Aman & Nyaman</h3>
                <ul class="card-content text-slate-600 text-sm space-y-3">
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

            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="tips">
                <div class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12a2 2 0 100-4 2 2 0 000 4zm0 0c.9 0 1.734.26 2.44.71M9 12H5m4 0h4m0 0a2 2 0 104 0 2 2 0 00-4 0zm0 0c-.9 0-1.734.26-2.44.71M17 16h-2v-3m0 0a2 2 0 104 0 2 2 0 00-4 0z" />
                    </svg>
                </div>
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4 text-left">Tips Berkendara Angkot</h3>
                <ul class="card-content text-slate-600 text-sm space-y-3">
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 font-bold">•</span> Duduk di tengah untuk perjalanan lebih stabil.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 font-bold">•</span> Pegang pegangan saat angkot bergerak.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 font-bold">•</span> Beritahu sopir jika ingin turun jauh-jauh hari.
                    </li>
                </ul>
            </div>

            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="tips">
                <div class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4 text-left">Tips Hemat Ongkos</h3>
                <ul class="card-content text-slate-600 text-sm space-y-3">
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 font-bold">•</span> Pilih angkot yang rutenya paling dekat dengan tujuan.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 font-bold">•</span> Hindari transit berlebihan untuk menghemat biaya.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 font-bold">•</span> Tanyakan tarif sebelum naik jika ragu.
                    </li>
                </ul>
            </div>

            <!-- PANDUAN -->
            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="panduan">
                <div class="w-14 h-14 bg-emerald-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4">Panduan Menarik Angkot</h3>
                <ul class="card-content text-slate-600 text-sm space-y-3">
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

            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="panduan">
                <div class="w-14 h-14 bg-emerald-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                    </svg>
                </div>
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4">Panduan Turun Angkot</h3>
                <ul class="card-content text-slate-600 text-sm space-y-3">
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-500 font-bold">•</span> Katakan "Kiri, Bang!" untuk memberi tahu sopir.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-500 font-bold">•</span> Tunggu angkot berhenti total sebelum turun.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-500 font-bold">•</span> Pastikan tidak ada kendaraan di belakang saat turun.
                    </li>
                </ul>
            </div>

            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="panduan">
                <div class="w-14 h-14 bg-emerald-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4">Panduan Bayar Ongkos</h3>
                <ul class="card-content text-slate-600 text-sm space-y-3">
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-500 font-bold">•</span> Bayar ongkos setelah naik atau sebelum turun.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-500 font-bold">•</span> Oper uang ke depan jika duduk di belakang.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-500 font-bold">•</span> Ucapkan terima kasih kepada sopir.
                    </li>
                </ul>
            </div>

            <!-- FAQ -->
            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="faq">
                <div class="w-14 h-14 bg-amber-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-amber-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4">Tarif & Pembayaran</h3>
                <ul class="card-content text-slate-600 text-sm space-y-3">
                    <li class="flex items-start gap-2 italic">
                        "Berapa tarif jarak dekat?" <br>— Umumnya Rp 3.000 - Rp 5.000.
                    </li>
                    <li class="flex items-start gap-2 italic">
                        "Apakah bisa bayar dengan uang elektronik?" <br>— Sebagian besar masih tunai.
                    </li>
                </ul>
            </div>

            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="faq">
                <div class="w-14 h-14 bg-amber-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-amber-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4">Rute & Jadwal</h3>
                <ul class="card-content text-slate-600 text-sm space-y-3">
                    <li class="flex items-start gap-2 italic">
                        "Sampai jam berapa angkot beroperasi?" <br>— Sebagian besar hingga jam 21.00 WIB.
                    </li>
                    <li class="flex items-start gap-2 italic">
                        "Bagaimana cara tahu rute angkot?" <br>— Lihat nomor trayek dan tanyakan ke sopir.
                    </li>
                </ul>
            </div>

            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="faq">
                <div class="w-14 h-14 bg-amber-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-amber-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4">Keamanan & Kenyamanan</h3>
                <ul class="card-content text-slate-600 text-sm space-y-3">
                    <li class="flex items-start gap-2 italic">
                        "Bagaimana jika barang hilang di angkot?" <br>— Segera hubungi pool angkot terdekat.
                    </li>
                    <li class="flex items-start gap-2 italic">
                        "Apakah aman naik angkot malam hari?" <br>— Pilih angkot yang ramai penumpang.
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ini mneganbil semua elemen yang dibutuhkan
    const tabButtons = document.querySelectorAll('.tab-button');
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const cards = document.querySelectorAll('.card-item');
    let currentTab = 'tips';

    // fungsinya ini untuk ganti tab
    function switchTab(tabName) {
        currentTab = tabName;
        
        // Update style tombol tab
        tabButtons.forEach(btn => {
            const btnTab = btn.getAttribute('data-tab');
            if (btnTab === tabName) {
                btn.classList.remove('bg-white', 'text-slate-600', 'border', 'border-slate-200', 'hover:bg-slate-100');
                btn.classList.add('bg-blue-600', 'text-white', 'shadow-md');
            } else {
                btn.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
                btn.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-200', 'hover:bg-slate-100');
            }
        });
        
        // untuk mengosongkan kolom pencarian
        searchInput.value = '';
        
        // tampilkan/ sembunyikan card sesuai kategori
        cards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            if (cardCategory === tabName) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // fungsi nya untuk mecari card berdasarakan keyword
    function searchCards(keyword) {
        const searchTerm = keyword.toLowerCase().trim();
        
        cards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            
            // cuma cari di tab yang sedang aktif
            if (cardCategory !== currentTab) {
                return;
            }
            
            if (searchTerm === '') {
                card.style.display = 'block';
                return;
            }
            
             // Ambil teks dari judul dan isi card
            const title = card.querySelector('.card-title');
            const content = card.querySelector('.card-content');
            
            if (!title || !content) {
                return;
            }
            
            const titleText = title.textContent.toLowerCase();
            const contentText = content.textContent.toLowerCase();
            
              // Tampilkan card kalau keyword cocok di judul atau isi
            if (titleText.includes(searchTerm) || contentText.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Event: Klik tombol tab (Tips/Panduan/FAQ)
    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });

    // Event: Ketik di kolom search (real-time)
    searchInput.addEventListener('input', function() {
        searchCards(this.value);
    });

    // Event: Klik tombol "Cari"
    searchButton.addEventListener('click', function() {
        searchCards(searchInput.value);
    });

    // Enter key support // Event: Tekan Enter di kolom search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchCards(this.value);
        }
    });

    // Inisialisasi: Tampilkan tab Tips pertama kali
    switchTab('tips');
});
</script>

@endsection