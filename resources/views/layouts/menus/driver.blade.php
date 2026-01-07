{{-- Sidebar Items --}}
@section('sidebar-menu')
    @include('layouts.partials.nav-item', ['route' => 'driver.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.nav-item', ['route' => 'driver.edukasi.index', 'icon' => 'book-open', 'label' => 'Edukasi Driver'])
    @include('layouts.partials.nav-item', ['route' => 'driver.angkot.index', 'icon' => 'truck', 'label' => 'Profil Armada'])
@endsection

{{-- Mobile Items --}}
@section('mobile-menu')
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.edukasi.index', 'icon' => 'book-open', 'label' => 'Edukasi'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.angkot.index', 'icon' => 'truck', 'label' => 'Armada'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.profile.index', 'icon' => 'user', 'label' => 'Akun'])
@endsection
