@extends('layouts.app_dashboard')

@section('title', 'Waiting room')

@section(section: 'content')
<div>
    <div class="max-w-xl mx-auto p-10 text-center">
        <h1 class="text-2xl font-bold mb-2">Akun kamu sedang diproses</h1>
        <p class="text-slate-600">
            Mohon ditunggu ya 😊
        </p>

        @if(session('success'))
            <p class="mt-4 text-emerald-700 font-semibold">{{ session('success') }}</p>
        @endif

        <div class="mt-6">
            <a class="underline" href="{{ route('driver.profile.edit') }}">Periksa / lengkapi profil driver</a>
        </div>
    </div>
</div>
