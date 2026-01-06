{{-- Sidebar Items --}}
@section('sidebar-menu')
    @include('layouts.partials.nav-item', ['route' => 'driver.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
<<<<<<< HEAD
    @include('layouts.partials.nav-item', ['route' => 'driver.cekjalur.index', 'icon' => 'signpost', 'label' => 'Cek Jalur'])
    @include('layouts.partials.nav-item', ['route' => 'driver.edukasi.index', 'icon' => 'book-open', 'label' => 'Edukasi Driver'])
    @include('layouts.partials.nav-item', ['route' => 'driver.profil.index', 'icon' => 'user', 'label' => 'Profil Armada'])
=======
    @include('layouts.partials.nav-item', ['route' => 'driver.history.index', 'icon' => 'signpost', 'label' => 'Catatan Perjalanan'])
    @include('layouts.partials.nav-item', ['route' => 'driver.angkot.index', 'icon' => 'bus', 'label' => 'Profil Armada'])
>>>>>>> b4587fe2c04c9ea5834348b0ec09a21d85b2a88f
@endsection

{{-- Mobile Items --}}
@section('mobile-menu')
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
<<<<<<< HEAD
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.cekjalur.index', 'icon' => 'signpost', 'label' => 'Cek Jalur'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.edukasi.index', 'icon' => 'book-open', 'label' => 'Edukasi'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.profil.index', 'icon' => 'user', 'label' => 'Armada'])
=======
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.tracking.index', 'icon' => 'signpost', 'label' => 'Catatan Perjalanan'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'driver.angkot.index', 'icon' => 'bus', 'label' => 'Armada'])
>>>>>>> b4587fe2c04c9ea5834348b0ec09a21d85b2a88f
@endsection
