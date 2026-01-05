<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Lupa Password?</h1>
        <p class="text-slate-500 text-sm mt-1">Masukkan email Anda dan kami akan mengirimkan link reset password.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="text-xs font-bold text-slate-600 block mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                placeholder="nama@email.com" />
            @error('email')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full py-3 rounded-2xl bg-blue-600 text-white font-bold tracking-tight shadow-lg hover:bg-blue-700 transition">
            Kirim Link Reset Password
        </button>

        <!-- Back to Login -->
        <p class="text-center text-sm text-slate-500">
            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke halaman login
            </a>
        </p>
    </form>
</x-guest-layout>
