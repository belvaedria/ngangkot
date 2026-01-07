@extends('layouts.app_dashboard')

@section('title', 'Profil Driver')

@section('content')
<div>
    <div class="max-w-xl p-8">
        <h1 class="text-2xl font-bold mb-4">Lengkapi Profil Driver</h1>

        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('driver.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label>Nomor SIM</label>
                <input name="nomor_sim" class="border w-full p-2" value="{{ old('nomor_sim', $profile->nomor_sim) }}">
            </div>

            <div>
                <label>Alamat Domisili</label>
                <input name="alamat_domisili" class="border w-full p-2" value="{{ old('alamat_domisili', $profile->alamat_domisili) }}">
            </div>

            <div>
                <label>Foto KTP</label>
                <br>
                <input type="file" name="foto_ktp">
            </div>

            <div>
                <label>Foto SIM</label>
                <br>
                <input type="file" name="foto_sim">
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">Kirim</button>
        </form>
    </div>
</div>
