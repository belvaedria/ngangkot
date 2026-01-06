@extends('layouts.app_dashboard')
@include('layouts.menus.driver')

@section('title', 'Profil Armada - Ngangkot')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 md:p-10">
    
    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium flex items-center gap-2">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900">Profil Armada</h1>
            <p class="text-slate-500 mt-1">Kelola informasi kendaraan angkot Anda</p>
        </div>
        <a href="{{ route('driver.angkot.create') }}" 
           class="mt-4 md:mt-0 inline-flex items-center gap-2 px-5 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Tambah Armada
        </a>
    </div>

    @if($angkots->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($angkots as $angkot)
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
            {{-- Header Card --}}
            <div class="bg-gradient-to-r from-slate-800 to-slate-900 p-6 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center">
                        <i data-lucide="bus" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black">{{ $angkot->plat_nomor }}</p>
                        <p class="text-slate-300 text-sm">Angkot Terdaftar</p>
                    </div>
                </div>
            </div>

            {{-- Body Card --}}
            <div class="p-6 space-y-4">
                {{-- Trayek Info --}}
                <div class="flex items-center gap-3 p-4 rounded-xl bg-blue-50 border border-blue-100">
                    <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                        <i data-lucide="route" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-bold">Trayek</p>
                        <p class="text-sm font-bold text-slate-900">
                            {{ $angkot->trayek ? $angkot->trayek->kode_trayek . ' - ' . $angkot->trayek->nama_trayek : 'Belum ditentukan' }}
                        </p>
                    </div>
                </div>

                {{-- Status --}}
                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg {{ $angkot->is_active ? 'bg-emerald-100' : 'bg-slate-200' }} flex items-center justify-center">
                            <i data-lucide="{{ $angkot->is_active ? 'radio' : 'radio-off' }}" class="w-5 h-5 {{ $angkot->is_active ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-bold">Status</p>
                            <p class="text-sm font-bold {{ $angkot->is_active ? 'text-emerald-600' : 'text-slate-500' }}">
                                {{ $angkot->is_active ? 'Sedang Beroperasi' : 'Tidak Aktif' }}
                            </p>
                        </div>
                    </div>
                    @if($angkot->is_active)
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    @endif
                </div>

                {{-- Last Update --}}
                <div class="flex items-center gap-3 text-sm text-slate-500">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span>Terakhir diupdate: {{ $angkot->updated_at->diffForHumans() }}</span>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('driver.angkot.edit', $angkot) }}" 
                       class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                        Edit
                    </a>
                    <form action="{{ route('driver.angkot.destroy', $angkot) }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus armada ini?')" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-50 text-rose-600 font-bold rounded-xl hover:bg-rose-100 transition">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    {{-- Empty State --}}
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-12 text-center">
        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-6">
            <i data-lucide="truck" class="w-10 h-10 text-slate-400"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Armada</h3>
        <p class="text-slate-500 mb-6 max-w-md mx-auto">
            Anda belum mendaftarkan kendaraan angkot. Daftarkan armada Anda untuk mulai beroperasi.
        </p>
        <a href="{{ route('driver.angkot.create') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Daftarkan Armada Sekarang
        </a>
    </div>
    @endif

    {{-- Info Card --}}
    <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center flex-shrink-0">
                <i data-lucide="info" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 mb-1">Tips Mengelola Armada</h4>
                <ul class="text-sm text-slate-600 space-y-1">
                    <li>• Pastikan nomor polisi sesuai dengan STNK kendaraan</li>
                    <li>• Pilih trayek yang sesuai dengan izin operasional</li>
                    <li>• Update status armada saat mulai dan selesai beroperasi</li>
                </ul>
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
