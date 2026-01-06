@extends('layouts.app_dashboard')
@include('layouts.menus.passenger')

@section('title', 'Pusat Edukasi Ngangkot')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
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

            {{-- TIPS - dari database --}}
            @forelse($tips as $tip)
            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="tips">
                <div class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-200">
                    <i data-lucide="lightbulb" class="w-8 h-8"></i>
                </div>
                @if($tip->gambar)
                <img src="{{ asset('storage/' . $tip->gambar) }}" alt="{{ $tip->judul }}" class="w-full h-40 object-cover rounded-xl mb-4">
                @endif
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4 text-left">{{ $tip->judul }}</h3>
                <div class="card-content text-slate-600 text-sm space-y-3">
                    {!! nl2br(e(Str::limit($tip->konten, 200))) !!}
                </div>
                <button onclick="showDetail('{{ addslashes($tip->judul) }}', `{{ addslashes($tip->konten) }}`)" class="mt-4 text-blue-600 hover:text-blue-800 font-medium text-sm">
                    Baca Selengkapnya →
                </button>
            </div>
            @empty
            <div class="card-item col-span-3 text-center py-12" data-category="tips">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="file-text" class="w-10 h-10 text-slate-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-600 mb-2">Belum Ada Tips</h3>
                <p class="text-slate-500">Tips perjalanan akan segera ditambahkan.</p>
            </div>
            @endforelse

            {{-- PANDUAN - dari database --}}
            @forelse($panduan as $pand)
            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="panduan">
                <div class="w-14 h-14 bg-emerald-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-200">
                    <i data-lucide="book-open" class="w-8 h-8"></i>
                </div>
                @if($pand->gambar)
                <img src="{{ asset('storage/' . $pand->gambar) }}" alt="{{ $pand->judul }}" class="w-full h-40 object-cover rounded-xl mb-4">
                @endif
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4">{{ $pand->judul }}</h3>
                <div class="card-content text-slate-600 text-sm space-y-3">
                    {!! nl2br(e(Str::limit($pand->konten, 200))) !!}
                </div>
                <button onclick="showDetail('{{ addslashes($pand->judul) }}', `{{ addslashes($pand->konten) }}`)" class="mt-4 text-emerald-600 hover:text-emerald-800 font-medium text-sm">
                    Baca Selengkapnya →
                </button>
            </div>
            @empty
            <div class="card-item col-span-3 text-center py-12" data-category="panduan">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="book-open" class="w-10 h-10 text-slate-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-600 mb-2">Belum Ada Panduan</h3>
                <p class="text-slate-500">Panduan penggunaan angkot akan segera ditambahkan.</p>
            </div>
            @endforelse

            {{-- FAQ - dari artikel kategori faq --}}
            @foreach($faqArtikels as $faqArt)
            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="faq">
                <div class="w-14 h-14 bg-amber-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-amber-200">
                    <i data-lucide="help-circle" class="w-8 h-8"></i>
                </div>
                @if($faqArt->gambar)
                <img src="{{ asset('storage/' . $faqArt->gambar) }}" alt="{{ $faqArt->judul }}" class="w-full h-40 object-cover rounded-xl mb-4">
                @endif
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4">{{ $faqArt->judul }}</h3>
                <div class="card-content text-slate-600 text-sm space-y-3">
                    {!! nl2br(e(Str::limit($faqArt->konten, 200))) !!}
                </div>
                <button onclick="showDetail('{{ addslashes($faqArt->judul) }}', `{{ addslashes($faqArt->konten) }}`)" class="mt-4 text-amber-600 hover:text-amber-800 font-medium text-sm">
                    Baca Selengkapnya →
                </button>
            </div>
            @endforeach

            {{-- FAQ - dari tabel faqs (legacy) --}}
            @forelse($faqs as $faq)
            <div class="card-item group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100" data-category="faq">
                <div class="w-14 h-14 bg-amber-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-amber-200">
                    <i data-lucide="help-circle" class="w-8 h-8"></i>
                </div>
                <h3 class="card-title text-xl font-bold text-slate-900 mb-4">{{ $faq->pertanyaan }}</h3>
                <div class="card-content text-slate-600 text-sm space-y-3">
                    <p class="italic">{!! nl2br(e($faq->jawaban)) !!}</p>
                </div>
            </div>
            @empty
                @if($faqArtikels->isEmpty())
                <div class="card-item col-span-3 text-center py-12" data-category="faq">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="help-circle" class="w-10 h-10 text-slate-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-600 mb-2">Belum Ada FAQ</h3>
                    <p class="text-slate-500">Pertanyaan yang sering diajukan akan segera ditambahkan.</p>
                </div>
                @endif
            @endforelse

        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div id="detailModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h3 id="modalTitle" class="text-xl font-bold text-slate-900"></h3>
            <button onclick="closeModal()" class="text-slate-500 hover:text-slate-700">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <div id="modalContent" class="text-slate-700 leading-relaxed whitespace-pre-line"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    const tabButtons = document.querySelectorAll('.tab-button');
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const cards = document.querySelectorAll('.card-item');
    let currentTab = 'tips';

    function switchTab(tabName) {
        currentTab = tabName;
        
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
        
        searchInput.value = '';
        
        cards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            if (cardCategory === tabName) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function searchCards(keyword) {
        const searchTerm = keyword.toLowerCase().trim();
        
        cards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            
            if (cardCategory !== currentTab) {
                return;
            }
            
            if (searchTerm === '') {
                card.style.display = 'block';
                return;
            }
            
            const title = card.querySelector('.card-title');
            const content = card.querySelector('.card-content');
            
            if (!title || !content) {
                return;
            }
            
            const titleText = title.textContent.toLowerCase();
            const contentText = content.textContent.toLowerCase();
            
            if (titleText.includes(searchTerm) || contentText.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });

    searchInput.addEventListener('input', function() {
        searchCards(this.value);
    });

    searchButton.addEventListener('click', function() {
        searchCards(searchInput.value);
    });

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchCards(this.value);
        }
    });

    switchTab('tips');
});

function showDetail(title, content) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalContent').textContent = content.replace(/\\n/g, '\n');
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('detailModal').classList.add('flex');
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('detailModal').classList.remove('flex');
}

document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

@endsection