@extends('layouts.app')

@section('title', 'Akses Terbatas')

@section('content')
<div class="max-w-md mx-auto p-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h1 class="text-lg font-black">Akses Terbatas</h1>
        <p class="mt-3 text-sm text-slate-600">{{ $message ?? 'Fitur ini hanya tersedia untuk wargi yang sudah bergabung.' }}</p>

        <div class="mt-6 flex gap-3">
            <a href="{{ $loginUrl }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold">Masuk</a>
            <button onclick="history.back()" class="px-4 py-2 bg-slate-100 rounded-lg">Kembali</button>
        </div>

        <p class="mt-4 text-xs text-slate-400">Atau Anda bisa membuat akun terlebih dahulu jika belum memiliki.</p>
    </div>
</div>
@endsection