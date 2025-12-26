<nav class="fixed top-0 w-full z-50 transition-all duration-300" id="main-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20 bg-white/80 backdrop-blur-xl border border-white/50 rounded-b-[2rem] shadow-sm px-6 mt-2 mx-2 md:mx-0 md:mt-0 md:rounded-none md:rounded-b-2xl md:shadow-none md:bg-white/90">
            
            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                <div class="bg-blue-600 p-2 rounded-xl text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform duration-300">
                    <i data-lucide="bus" class="w-5 h-5"></i>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-slate-900">Ngangkot<span class="text-blue-600">.</span></span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex gap-8 items-center font-bold text-sm text-slate-500">
                <a href="{{ url('/#fitur') }}" class="hover:text-blue-600 transition-colors">Fitur Kami</a>
                <a href="{{ url('/#tentang') }}" class="hover:text-blue-600 transition-colors">Tentang Kami</a>
                
                {{-- MENU KHUSUS ADMIN (Desktop) --}}
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <a href="{{ route('admin.trayek.index') }}" class="hover:text-blue-600 transition-colors text-blue-600">Kelola Trayek</a>
                @endif
                
                {{-- MENU KHUSUS DRIVER (Desktop) --}}
                @if(Auth::check() && Auth::user()->role === 'driver')
                    <a href="{{ route('driver.tracking.index') }}" class="hover:text-blue-600 transition-colors text-blue-600">Mulai Narik</a>
                @endif
            </div>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-3">
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-xl hover:bg-slate-800 transition-all flex items-center gap-2">
                            <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i> 
                            {{ Auth::user()->role === 'passenger' ? 'Dashboard' : (Auth::user()->role === 'admin' ? 'Admin Panel' : 'Area Driver') }}
                        </button>
                        <!-- Dropdown Logout -->
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50" x-cloak>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-xl shadow-blue-600/20 hover:bg-blue-700 hover:-translate-y-0.5 transition-all">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
