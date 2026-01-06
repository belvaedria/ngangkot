@extends('layouts.app_dashboard')
@include('layouts.menus.driver')

@section('title', 'Profil Armada')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
    @if(session('success'))
    <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif
    
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left Column: Driver Profile Card -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Profile Header -->
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <!-- Avatar -->
                    <div class="w-28 h-28 rounded-2xl bg-slate-800 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="user" class="w-14 h-14 text-slate-400"></i>
                    </div>
                    
                    <!-- Info -->
                    <div class="text-center md:text-left flex-1">
                        <h1 class="text-2xl font-black text-slate-900">{{ $user->name }}</h1>
                        <p class="text-sm text-slate-500 mt-1">
                            PENGEMUDI UTAMA • {{ $angkot ? $angkot->plat_nomor : 'Belum ada armada' }}
                        </p>
                        
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mt-4">
                            <!-- Rating -->
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-slate-100 text-sm font-semibold text-slate-700">
                                <i data-lucide="star" class="w-4 h-4 text-yellow-500 fill-yellow-500"></i>
                                4.9/5.0
                            </span>
                            
                            <!-- Status Verifikasi -->
                            @php
                                $status = $profile ? $profile->status : 'pending';
                            @endphp
                            
                            @if($status === 'verified')
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-emerald-50 border border-emerald-200 text-sm font-semibold text-emerald-700">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Terverifikasi Dishub
                            </span>
                            @elseif($status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-amber-50 border border-amber-200 text-sm font-semibold text-amber-700">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                                Menunggu Verifikasi
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-rose-50 border border-rose-200 text-sm font-semibold text-rose-700">
                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                Ditolak
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Edit Button -->
                    <a href="{{ route('driver.profil.edit') }}" 
                       class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-colors">
                        <i data-lucide="edit-3" class="w-4 h-4 inline mr-1"></i>
                        Edit Profil
                    </a>
                </div>
            </div>
            
            <!-- Vehicle Info Card -->
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
                <h2 class="text-lg font-black text-slate-900 mb-6">Informasi Kendaraan</h2>
                
                @if($angkot)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-slate-100">
                            <span class="text-sm text-slate-500">NOMOR POLISI</span>
                            <span class="text-sm font-bold text-slate-900">{{ $angkot->plat_nomor }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-slate-100">
                            <span class="text-sm text-slate-500">MERK KENDARAAN</span>
                            <span class="text-sm font-bold text-slate-900">Suzuki Carry 2021</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-slate-100">
                            <span class="text-sm text-slate-500">STATUS IZIN</span>
                            <span class="text-sm font-bold text-emerald-600">AKTIF</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-slate-100">
                            <span class="text-sm text-slate-500">JATUH TEMPO KIR</span>
                            <span class="text-sm font-bold text-slate-900">12 Des 2024</span>
                        </div>
                    </div>
                </div>
                
                <!-- Trayek Info -->
                <div class="mt-6 p-4 rounded-2xl bg-blue-50/50 border border-blue-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center">
                            <i data-lucide="route" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">TRAYEK TERDAFTAR</p>
                            <p class="text-sm font-bold text-slate-900">
                                {{ $angkot->trayek ? $angkot->trayek->kode_trayek . ' - ' . $angkot->trayek->nama_trayek : 'Tidak ada trayek' }}
                            </p>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="truck" class="w-8 h-8 text-slate-400"></i>
                    </div>
                    <p class="text-slate-500 mb-4">Belum ada armada terdaftar</p>
                    <a href="{{ route('driver.profil.edit') }}" 
                       class="inline-block px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-bold">
                        Daftarkan Armada
                    </a>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Right Column: Wallet -->
        <div class="space-y-6">
            <!-- Wallet Card -->
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl shadow-xl p-6 text-white relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute top-0 right-0 w-40 h-40 bg-blue-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-blue-500/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                
                <div class="relative">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                            <i data-lucide="wallet" class="w-5 h-5"></i>
                        </div>
                        <span class="text-lg font-bold">Dompet Driver</span>
                    </div>
                    
                    <p class="text-xs text-slate-400 uppercase tracking-wider">SALDO SAAT INI</p>
                    <p class="text-4xl font-black mt-1">Rp {{ number_format($saldo, 0, ',', '.') }}</p>
                    
                    <button class="w-full mt-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-colors">
                        Cairkan Saldo
                    </button>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
                <h3 class="text-sm font-black text-slate-900 mb-4">Statistik Bulan Ini</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                <i data-lucide="map" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <span class="text-sm text-slate-600">Total Perjalanan</span>
                        </div>
                        <span class="text-sm font-bold text-slate-900">128</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                <i data-lucide="trending-up" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                            <span class="text-sm text-slate-600">Pendapatan</span>
                        </div>
                        <span class="text-sm font-bold text-emerald-600">Rp 1.2jt</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                                <i data-lucide="star" class="w-4 h-4 text-amber-600"></i>
                            </div>
                            <span class="text-sm text-slate-600">Rating</span>
                        </div>
                        <span class="text-sm font-bold text-slate-900">4.9 ⭐</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
