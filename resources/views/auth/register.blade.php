<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Buat Akun Baru</h1>
        <p class="text-slate-500 text-sm mt-1">Daftar untuk mulai menggunakan Ngangkot</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="{ role: 'wargi' }">
        @csrf

        <!-- Name -->
        <div>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 bg-slate-50 font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                placeholder="Nama Lengkap" />
            @error('name')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 bg-slate-50 font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                placeholder="Email Address" />
            @error('email')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 bg-slate-50 font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                placeholder="Password (minimal 8 karakter)" />
            @error('password')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 bg-slate-50 font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                placeholder="Konfirmasi Password" />
            @error('password_confirmation')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Selection -->
        <div>
            <p class="text-xs font-bold text-slate-400 text-center mb-3 uppercase tracking-widest">Daftar Sebagai</p>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" 
                        @click="role = 'wargi'"
                        :class="role === 'wargi' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-white text-slate-400 border border-slate-100'"
                        class="py-3 rounded-2xl font-bold text-sm transition-all duration-300 focus:outline-none">
                    WARGI
                </button>
                <button type="button" 
                        @click="role = 'driver'"
                        :class="role === 'driver' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-white text-slate-400 border border-slate-100'"
                        class="py-3 rounded-2xl font-bold text-sm transition-all duration-300 focus:outline-none">
                    DRIVER
                </button>
            </div>
            <input type="hidden" name="role" :value="role">
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full py-3.5 rounded-2xl bg-blue-600 text-white font-bold tracking-tight shadow-lg hover:bg-blue-700 transition text-base">
            Daftar Sekarang
        </button>

        <!-- Login Link -->
        <p class="text-center text-sm text-slate-500">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-bold">Masuk di sini</a>
        </p>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</x-guest-layout>
