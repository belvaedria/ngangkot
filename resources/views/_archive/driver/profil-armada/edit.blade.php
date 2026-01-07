@extends('layouts.app_dashboard')
@include('layouts.menus.driver')

@section('title', 'Edit Profil Armada')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('driver.profil.index') }}" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 font-medium mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Profil
            </a>
            <h1 class="text-2xl font-black text-slate-900">Edit Profil Armada</h1>
            <p class="text-sm text-slate-500 mt-1">Perbarui informasi kendaraan dan data pengemudi</p>
        </div>
        
        <!-- Form -->
        <form action="{{ route('driver.profil.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Vehicle Information -->
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="truck" class="w-5 h-5 text-blue-600"></i>
                    Informasi Kendaraan
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nomor Polisi</label>
                        <input type="text" name="plat_nomor" value="{{ old('plat_nomor', $angkot?->plat_nomor) }}"
                               placeholder="D 1234 ABC"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('plat_nomor') border-rose-500 @enderror">
                        @error('plat_nomor')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Trayek</label>
                        <select name="trayek_id" 
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('trayek_id') border-rose-500 @enderror">
                            <option value="">Pilih Trayek</option>
                            @foreach($trayeks as $trayek)
                            <option value="{{ $trayek->id }}" {{ old('trayek_id', $angkot?->trayek_id) == $trayek->id ? 'selected' : '' }}>
                                {{ $trayek->kode_trayek }} - {{ $trayek->nama_trayek }}
                            </option>
                            @endforeach
                        </select>
                        @error('trayek_id')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Driver Information -->
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                    Informasi Pengemudi
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nomor SIM</label>
                        <input type="text" name="nomor_sim" value="{{ old('nomor_sim', $profile?->nomor_sim) }}"
                               placeholder="1234567890123456"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Domisili</label>
                        <textarea name="alamat_domisili" rows="3"
                                  placeholder="Jl. Contoh No. 123, Kota Bandung"
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none">{{ old('alamat_domisili', $profile?->alamat_domisili) }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('driver.profil.index') }}" 
                   class="px-6 py-3 rounded-xl border border-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
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
