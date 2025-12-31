{{-- Sidebar Items --}}
@section('sidebar-menu')
    @include('layouts.partials.nav-item', ['route' => 'admin.dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'])
    @include('layouts.partials.nav-item', ['route' => 'admin.trayek.index', 'icon' => 'signpost-big', 'label' => 'Kelola Trayek'])
    @include('layouts.partials.nav-item', ['route' => 'admin.verifikasi.index', 'icon' => 'badge-check', 'label' => 'Verifikasi Driver'])
    @include('layouts.partials.nav-item', ['route' => 'admin.laporan.index', 'icon' => 'inbox', 'label' => 'Laporan Pengguna'])
    @include('layouts.partials.nav-item', ['route' => 'admin.artikel.index', 'icon' => 'newspaper', 'label' => 'Artikel & Tips'])
    @include('layouts.partials.nav-item', ['route' => 'admin.faq.index', 'icon' => 'help-circle', 'label' => 'FAQ'])
@endsection

{{-- Mobile Items --}}
@section('mobile-menu')
    @include('layouts.partials.mobile-nav-item', ['route' => 'admin.dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'admin.trayek.index', 'icon' => 'signpost-big', 'label' => 'Trayek'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'admin.verifikasi.index', 'icon' => 'badge-check', 'label' => 'Verifikasi'])
    @include('layouts.partials.mobile-nav-item', ['route' => 'admin.laporan.index', 'icon' => 'inbox', 'label' => 'Laporan'])
@endsection
