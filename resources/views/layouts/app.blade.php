<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ngangkot - Transportasi Cerdas Bandung')</title>
    
    <!-- 1. Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- 2. Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        'ngangkot-blue': '#2563eb', // Blue-600
                        'ngangkot-dark': '#0f172a', // Slate-900
                    }
                }
            }
        }
    </script>
    
    <!-- 3. Leaflet CSS & JS (Peta) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- 4. Alpine.js (Interaksi UI) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- 5. Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f8fafc;
            color: #0f172a;
        }
        
        .custom-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
        
        [x-cloak] { display: none !important; }
        
        /* Animasi */
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fade-in-up 0.6s ease-out forwards; }
    </style>
</head>
<body class="flex flex-col min-h-screen overflow-x-hidden selection:bg-blue-100 selection:text-blue-900">

    <!-- INCLUDE NAVIGATION -->
    @include('layouts.navigation')

    <!-- KONTEN UTAMA -->
    <main class="flex-grow pt-24 w-full h-full relative">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-white py-10 mt-auto relative z-10">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3">
                <div class="bg-white/10 p-2 rounded-lg">
                    <i data-lucide="bus" class="w-5 h-5 text-blue-400"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Ngangkot.</h3>
                    <p class="text-xs text-slate-400">Smart City Bandung</p>
                </div>
            </div>
            <div class="text-sm text-slate-400 font-medium text-center md:text-right">
                &copy; 2025 Tim Ngangkot D4 Sistem Informasi Kota Cerdas Telkom University.
            </div>
        </div>
    </footer>

    <!-- Scripts -->

    <!-- Modal untuk notifikasi login (untuk guest) -->
    <div id="authModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50" aria-hidden="true">
        <div class="bg-white p-6 rounded-lg shadow max-w-md w-full">
            <h3 class="font-black text-lg">Akses Terbatas</h3>
            <p id="authModalMessage" class="text-sm text-slate-600 mt-2">Mohon maaf, fitur ini hanya tersedia untuk wargi yang sudah bergabung. Silakan masuk terlebih dahulu.</p>
            <div class="mt-4 flex gap-3 justify-end">
                <button id="authModalBack" class="px-4 py-2 bg-slate-100 rounded-lg">Kembali</button>
                <a id="authModalLogin" href="/login" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold">Masuk</a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Header Scroll Effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('#main-header > div > div');
            if (window.scrollY > 10) {
                header.classList.add('shadow-md', 'bg-white/95');
                header.classList.remove('md:bg-white/90');
            } else {
                header.classList.remove('shadow-md', 'bg-white/95');
                header.classList.add('md:bg-white/90');
            }
        });

        // Handle clicks on links that require auth
        document.addEventListener('click', function(e) {
            const el = e.target.closest && e.target.closest('.requires-auth');
            if (!el) return;
            e.preventDefault();
            const intended = el.dataset.intended || '/';
            // show modal
            const modal = document.getElementById('authModal');
            const loginBtn = document.getElementById('authModalLogin');
            const backBtn = document.getElementById('authModalBack');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // set login link to include redirect param
            loginBtn.href = '/login?redirect=' + encodeURIComponent(intended);

            const closeModal = () => { modal.classList.remove('flex'); modal.classList.add('hidden'); };
            backBtn.onclick = () => closeModal();

            // clicking outside modal closes it
            modal.addEventListener('click', function(ev) {
                if (ev.target === modal) closeModal();
            }, { once: true });
        });
    </script>

    @stack('scripts')
</body>
</html>
