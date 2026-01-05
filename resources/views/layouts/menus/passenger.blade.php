{{-- Sidebar Items --}}
@section('sidebar-menu')
    @include('layouts.partials.nav-item', ['route' => 'passenger.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.trayek.index', 'icon' => 'navigation', 'label' => 'Lihat Trayek'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.riwayat.index', 'icon' => 'clock', 'label' => 'Riwayat Perjalanan'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.edukasi.index', 'icon' => 'book-open', 'label' => 'Edukasi'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.laporan.index', 'icon' => 'alert-circle', 'label' => 'Laporan'])
@endsection

{{-- Mobile Items --}}
@section('mobile-menu')
    @include('layouts.partials.mobile-nav-item', ['route' => 'passenger.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'passenger.trayek.index', 'icon' => 'navigation', 'label' => 'Trayek'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'passenger.edukasi.index', 'icon' => 'book-open', 'label' => 'Edukasi'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'passenger.laporan.index', 'icon' => 'alert-circle', 'label' => 'Laporan'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'profile.edit', 'icon' => 'user', 'label' => 'Akun'])
@endsection
