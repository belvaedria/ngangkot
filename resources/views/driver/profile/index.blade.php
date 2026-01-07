@extends('layouts.app_dashboard')

@section('title', 'Profil Driver')

@section('content')
<div class="max-w-xl p-8">

    <h1 class="text-2xl font-extrabold text-slate-900 mb-2">Profil Driver</h1>

    {{-- Status --}}
    @if(isset($profile) && $profile->status === 'pending')
        <p class="text-yellow-600 font-semibold mb-6">Status: Menunggu verifikasi admin</p>
    @elseif(isset($profile) && $profile->status === 'verified')
        <p class="text-green-600 font-semibold mb-6">Status: Terverifikasi</p>
    @elseif(isset($profile) && $profile->status === 'rejected')
        <p class="text-red-600 font-semibold mb-6">Status: Ditolak</p>
    @else
        <p class="text-slate-600 mb-6">Lengkapi data berikut untuk proses verifikasi.</p>
    @endif

    {{-- Error --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-700">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Success --}}
    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-700 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Kartu --}}
    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">

        {{-- Admin melihat data (read-only) --}}
        @if(auth()->user()->role === 'admin')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-500 font-bold">Nama</p>
                    <p class="font-semibold">{{ $profile->user->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-bold">Email</p>
                    <p class="font-semibold">{{ $profile->user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-bold">Nomor SIM</p>
                    <p class="font-semibold">{{ $profile->nomor_sim ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-bold">Alamat Domisili</p>
                    <p class="font-semibold">{{ $profile->alamat_domisili ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-slate-500 font-bold mb-2">Foto KTP</p>
                    @if($profile->foto_ktp)
                        <img class="w-full max-w-xs rounded-xl border"
                             src="{{ asset('storage/'.$profile->foto_ktp) }}">
                    @else
                        <p class="text-slate-400">Belum diunggah</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-bold mb-2">Foto SIM</p>
                    @if($profile->foto_sim)
                        <img class="w-full max-w-xs rounded-xl border"
                             src="{{ asset('storage/'.$profile->foto_sim) }}">
                    @else
                        <p class="text-slate-400">Belum diunggah</p>
                    @endif
                </div>
            </div>

            @if($profile->status === 'pending')
                <div class="mt-8 flex gap-3">
                    <form method="POST" action="{{ route('admin.verifikasi.approve', $profile->id) }}">
                        @csrf
                        <button class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-bold">
                            Terima
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.verifikasi.reject', $profile->id) }}">
                        @csrf
                        <button class="bg-red-600 text-white px-6 py-2.5 rounded-xl font-bold">
                            Tolak
                        </button>
                    </form>
                </div>
            @endif

        @else
            {{-- Driver mengisi form --}}
            <form method="POST" action="{{ route('driver.profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Nomor SIM</label>
                    <input name="nomor_sim"
                           value="{{ old('nomor_sim', $profile->nomor_sim ?? '') }}"
                           class="w-full border border-slate-300 rounded-xl px-4 py-3">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Alamat Domisili</label>
                    <input name="alamat_domisili"
                           value="{{ old('alamat_domisili', $profile->alamat_domisili ?? '') }}"
                           class="w-full border border-slate-300 rounded-xl px-4 py-3">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Upload Foto KTP</label>
                    <input type="file" name="foto_ktp" class="w-full">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Upload Foto SIM</label>
                    <input type="file" name="foto_sim" class="w-full">
                </div>

                <button class="bg-blue-600 text-white px-6 py-3 rounded-xl font-extrabold shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition">
                    Kirim untuk Verifikasi
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
