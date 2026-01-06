{{-- Sidebar Items --}}
@section('sidebar-menu')
    @php
        // Hitung laporan yang baru diupdate admin (status berubah dari pending ke diproses/selesai)
        $updatedReports = 0;
        if(Auth::check()) {
            $updatedReports = \App\Models\Laporan::where('user_id', Auth::id())
                ->whereIn('status', ['diproses', 'selesai'])
                ->where('updated_at', '>', now()->subDays(7)) // Update dalam 7 hari terakhir
                ->count();
        }
    @endphp

    @include('layouts.partials.nav-item', ['route' => 'passenger.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.trayek.index', 'icon' => 'navigation', 'label' => 'Lihat Trayek'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.riwayat.index', 'icon' => 'clock', 'label' => 'Riwayat Perjalanan'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.edukasi.index', 'icon' => 'book-open', 'label' => 'Edukasi'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.laporan.index', 'icon' => 'alert-circle', 'label' => 'Laporan', 'badge' => $updatedReports])
@endsection

{{-- Mobile Items --}}
@section('mobile-menu')
    @php
        $updatedReports = 0;
        if(Auth::check()) {
            $updatedReports = \App\Models\Laporan::where('user_id', Auth::id())
                ->whereIn('status', ['diproses', 'selesai'])
                ->where('updated_at', '>', now()->subDays(7))
                ->count();
        }
    @endphp

    @include('layouts.partials.mobile-nav-item', ['route' => 'passenger.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'passenger.trayek.index', 'icon' => 'navigation', 'label' => 'Trayek'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'passenger.edukasi.index', 'icon' => 'book-open', 'label' => 'Edukasi'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'passenger.laporan.index', 'icon' => 'alert-circle', 'label' => 'Laporan', 'badge' => $updatedReports])
    @include('layouts.partials.mobile-nav-item', ['route' => 'profile.edit', 'icon' => 'user', 'label' => 'Akun'])
@endsection
