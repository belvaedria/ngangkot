{{-- Sidebar Items --}}
@section('sidebar-menu')
    @include('layouts.partials.nav-item', ['route' => 'passenger.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.nav-item', ['route' => 'navigasi.index', 'icon' => 'navigation', 'label' => 'Navigasi'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.riwayat.index', 'icon' => 'clock', 'label' => 'Riwayat Perjalanan'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.edukasi.index', 'icon' => 'book-open', 'label' => 'Edukasi'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.laporan.index', 'icon' => 'alert-circle', 'label' => 'Laporan'])
@endsection

{{-- Mobile Items --}}
@section('mobile-menu')
    @include('layouts.partials.mobile-nav-item', ['route' => 'passenger.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'navigasi.index', 'icon' => 'navigation', 'label' => 'Navigasi'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'passenger.edukasi.index', 'icon' => 'book-open', 'label' => 'Edukasi'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'passenger.riwayat.index', 'icon' => 'clock', 'label' => 'Riwayat'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'profile.edit', 'icon' => 'user', 'label' => 'Akun'])
@endsection
