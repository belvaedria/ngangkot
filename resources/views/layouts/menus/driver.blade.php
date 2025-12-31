{{-- Sidebar Items --}}
@section('sidebar-menu')
    @include('layouts.partials.nav-item', ['route' => 'driver.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.nav-item', ['route' => 'driver.tracking.index', 'icon' => 'satellite-dish', 'label' => 'Tracking'])
    @include('layouts.partials.nav-item', ['route' => 'driver.angkot.index', 'icon' => 'bus', 'label' => 'Armada Saya'])
    @include('layouts.partials.nav-item', ['route' => 'driver.riwayat.index', 'icon' => 'clock', 'label' => 'Riwayat Narik'])
@endsection

{{-- Mobile Items --}}
@section('mobile-menu')
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.tracking.index', 'icon' => 'satellite-dish', 'label' => 'Tracking'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.angkot.index', 'icon' => 'bus', 'label' => 'Armada'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.riwayat.index', 'icon' => 'clock', 'label' => 'Riwayat'])
@endsection
