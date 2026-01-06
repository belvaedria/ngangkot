{{-- Sidebar Items --}}
@section('sidebar-menu')
    @include('layouts.partials.nav-item', ['route' => 'driver.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.nav-item', ['route' => 'driver.riwayat.index', 'icon' => 'signpost', 'label' => 'Catatan Perjalanan'])
    @include('layouts.partials.nav-item', ['route' => 'driver.angkot.index', 'icon' => 'bus', 'label' => 'Profil Armada'])
@endsection

{{-- Mobile Items --}}
@section('mobile-menu')
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.riwayat.index', 'icon' => 'signpost', 'label' => 'Catatan Perjalanan'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.angkot.index', 'icon' => 'bus', 'label' => 'Armada'])
@endsection
