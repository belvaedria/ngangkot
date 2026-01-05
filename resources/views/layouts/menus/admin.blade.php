{{-- Sidebar Items --}}
@section('sidebar-menu')
    @include('layouts.partials.nav-item', ['route' => 'admin.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.nav-item', ['route' => 'admin.trayek.index', 'icon' => 'signpost-big', 'label' => 'Kelola Trayek'])
    @include('layouts.partials.nav-item', ['route' => 'admin.verifikasi.index', 'icon' => 'shield-check', 'label' => 'Verifikasi Driver'])
    @include('layouts.partials.nav-item', ['route' => 'admin.laporan.index', 'icon' => 'file-text', 'label' => 'Update Laporan'])
    @include('layouts.partials.nav-item', ['route' => 'admin.artikel.index', 'icon' => 'book-open', 'label' => 'Mengelola Panduan'])
@endsection

{{-- Mobile Items --}}
@section('mobile-menu')
    @include('layouts.partials.mobile-nav-item', ['route' => 'admin.dashboard', 'icon' => 'home', 'label' => 'Beranda'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'admin.trayek.index', 'icon' => 'signpost-big', 'label' => 'Trayek'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'admin.verifikasi.index', 'icon' => 'shield-check', 'label' => 'Verifikasi'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'admin.laporan.index', 'icon' => 'file-text', 'label' => 'Laporan'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'admin.artikel.index', 'icon' => 'book-open', 'label' => 'Panduan'])
@endsection
