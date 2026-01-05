<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Selamat Datang</h1>
        <p class="text-slate-500 text-sm mt-1">Pintu gerbang transportasi cerdas Bandung.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ loginAs: 'wargi' }">
        @csrf

        <!-- Email Address -->
        <div>
            <div class="relative">
                <i data-lucide="mail" class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="w-full pl-12 pr-4 py-3 rounded-2xl border-2 border-slate-200 bg-slate-50 font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                    placeholder="Email Address" />
            </div>
            @error('email')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="relative">
                <i data-lucide="lock" class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full pl-12 pr-4 py-3 rounded-2xl border-2 border-slate-200 bg-slate-50 font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                    placeholder="Password" />
            </div>
            @error('password')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Login As Toggle -->
        <div>
            <p class="text-xs font-bold text-slate-400 text-center mb-3 uppercase tracking-widest">Login Sebagai</p>
            <div class="flex items-center justify-center gap-3">
                <button type="button" 
                        @click="loginAs = 'wargi'"
                        :class="loginAs === 'wargi' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-white text-slate-400 border border-slate-100'"
                        class="px-8 py-3 rounded-2xl font-bold text-sm transition-all duration-300 focus:outline-none">
                    WARGI
                </button>
                <button type="button" 
                        @click="loginAs = 'driver'"
                        :class="loginAs === 'driver' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-white text-slate-400 border border-slate-100'"
                        class="px-8 py-3 rounded-2xl font-bold text-sm transition-all duration-300 focus:outline-none">
                    DRIVER
                </button>
                <button type="button" 
                        @click="loginAs = 'admin'"
                        :class="loginAs === 'admin' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-white text-slate-400 border border-slate-100'"
                        class="px-8 py-3 rounded-2xl font-bold text-sm transition-all duration-300 focus:outline-none">
                    ADMIN
                </button>
            </div>
            <input type="hidden" name="login_as" :value="loginAs">
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full py-3.5 rounded-2xl bg-blue-600 text-white font-bold tracking-tight shadow-lg hover:bg-blue-700 transition text-base">
            Masuk Sekarang
        </button>

        <!-- Register Link -->
        <p class="text-center text-sm text-slate-500">
            Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-bold">Daftar Akun Baru</a>
        </p>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</x-guest-layout>
