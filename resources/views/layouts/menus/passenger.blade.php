{{-- Sidebar Items --}}
@section('sidebar-menu')
    @include('layouts.partials.nav-item', ['route' => 'passenger.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.nav-item', ['route' => 'trayek.index', 'icon' => 'signpost', 'label' => 'Lihat Trayek'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.riwayat.index', 'icon' => 'clock', 'label' => 'Riwayat Perjalanan', 'requireAuth' => true])
    @include('layouts.partials.nav-item', ['route' => 'edukasi.index', 'icon' => 'book-open', 'label' => 'Edukasi'])
    @include('layouts.partials.nav-item', ['route' => 'passenger.laporan.index', 'icon' => 'book-open', 'label' => 'Laporan', 'requireAuth' => true])
@endsection

{{-- Mobile Items --}}
@section('mobile-menu')
    @include('layouts.partials.mobile-nav-item', ['route' => 'passenger.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'trayek.index', 'icon' => 'signpost', 'label' => 'Lihat Trayek'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'edukasi.index', 'icon' => 'book-open', 'label' => 'Edukasi'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'profile.edit', 'icon' => 'user', 'label' => 'Akun', 'requireAuth' => true])
@endsection
