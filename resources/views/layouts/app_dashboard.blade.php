<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - Ngangkot')</title>
    
    <!-- 1. Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- 2. Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        'ngangkot-blue': '#2563eb', 
                        'ngangkot-dark': '#0f172a',
                    }
                }
            }
        }
    </script>
    
    <!-- 3. Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- 4. Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- 5. Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body
  class="flex h-screen overflow-hidden bg-slate-50 text-slate-900"
  x-data="{ sidebarCollapsed: false }"
  :style="`--sidebar-width: ${sidebarCollapsed ? 88 : 260}px`"
  @collapse-dashboard-sidebar.window="sidebarCollapsed = true"
>


    <!-- SIDEBAR (Desktop Only) -->
    <aside
    class="hidden md:flex flex-col bg-white border-r border-slate-200 transition-all duration-300 ease-in-out z-20 relative"
    :class="sidebarCollapsed ? 'w-[88px]' : 'w-[260px]'"
    >
        <!-- Header Sidebar -->
        <div class="h-20 flex items-center px-6 border-b border-slate-100">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="bg-blue-600 p-2 rounded-xl text-white shrink-0">
                    <i data-lucide="bus" class="w-6 h-6"></i>
                </div>
                <span class="text-xl font-black tracking-tight text-slate-900 transition-opacity duration-300"
                      :class="sidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100'">
                    Ngangkot<span class="text-blue-600">.</span>
                </span>
            </div>
        </div>

        <!-- Menu Navigation (DINAMIS DARI VIEW CHILD) -->
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1 custom-scroll">
            @yield('sidebar-menu')
        </nav>

        <div class="p-4 border-t border-slate-100">
            <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer overflow-hidden group">
                <a href="{{ Auth::check() ? route('profile.edit') : '#' }}" class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center shrink-0 font-bold">
                    {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'W' }}
                </a>
                <div class="transition-opacity duration-300 min-w-[120px]" :class="sidebarCollapsed ? 'opacity-0 w-0' : 'opacity-100'">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ Auth::check() ? Auth::user()->name : 'Wargi Bandung' }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ Auth::check() ? Auth::user()->role : 'Tamu' }}</p>
                </div>
                
                @auth
                <form method="POST" action="{{ route('logout') }}" class="ml-auto" :class="sidebarCollapsed ? 'hidden' : 'block'">
                    @csrf
                    <button class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Keluar">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
                @endauth
            </div>
            
            <!-- Toggle Collapse Button -->
            <button @click="sidebarCollapsed = !sidebarCollapsed" 
                    class="mt-2 w-full flex items-center justify-center p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                <i data-lucide="chevrons-left" class="w-5 h-5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''"></i>
            </button>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen relative overflow-hidden">
        @yield('content')
    </main>

    <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-slate-200 z-50 pb-safe shadow-2xl">
        <div class="flex justify-around p-2">
            @yield('mobile-menu')
        </div>
    </nav>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>