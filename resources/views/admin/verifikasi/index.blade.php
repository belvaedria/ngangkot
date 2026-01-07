@extends('layouts.app_dashboard')
@include('layouts.menus.admin')

@section('title', 'Verifikasi Driver')

@section('content')

    <div class="max-w-6xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Verifikasi Driver</h1>

        @if(session('success'))
            <div class="mb-4 text-green-700">{{ session('success') }}</div>
        @endif

        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Nama</th>
                    <th class="border p-2">Email</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($drivers as $driver)
                    <tr>
                        <td class="border p-2">{{ $driver->user->name }}</td>
                        <td class="border p-2">{{ $driver->user->email }}</td>
                        <td class="border p-2 capitalize">{{ $driver->status }}</td>
                        <td class="border p-2">
                            <a href="{{ route('admin.verifikasi.show', $driver->id) }}"
                               class="text-blue-600 underline">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center p-4 text-gray-500">
                            Tidak ada driver pending
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


@endsection
