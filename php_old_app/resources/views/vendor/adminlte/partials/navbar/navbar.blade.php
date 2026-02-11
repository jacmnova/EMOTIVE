@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- Notificações (sino) --}}
        @if(Auth::check())
            @php
                $notificacoes = Auth::user()->unreadNotifications()->take(5)->get();
            @endphp

            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    @if($notificacoes->count())
                        <span class="badge badge-warning navbar-badge">{{ $notificacoes->count() }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header">{{ $notificacoes->count() }} Notificações</span>
                    <div class="dropdown-divider"></div>

                    @forelse($notificacoes as $notif)
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user-plus mr-2"></i>
                            {{ $notif->data['titulo'] }}
                            <br>
                            <small>{{ $notif->data['mensagem'] }}</small>
                            <span class="float-right text-muted text-sm">
                                {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                            </span>
                        </a>
                        <div class="dropdown-divider"></div>
                    @empty
                        <span class="dropdown-item text-center">Sem notificações novas</span>
                    @endforelse

                    <a href="{{ route('notificacoes.marcarLidas') }}" class="dropdown-item dropdown-footer">
                        Marcar todas como lidas
                    </a>
                </div>
            </li>
        @endif

        {{-- User menu link --}}
        @if(Auth::user())
            @if(config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>

</nav>
