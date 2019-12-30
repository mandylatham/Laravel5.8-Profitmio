<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ (env('APP_ENV') == 'production' ? '' : '('.env('APP_ENV').') ') }}Profit Miner</title>

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    @yield('head-styles')
    <script>
        window.loggedUser = @json(auth()->user());
        window.isAdmin = @json(auth()->user()->isAdmin());
        window.isImpersonated = @json(auth()->user()->isImpersonated());
        window.timezone = @json(auth()->user()->getTimezone(\App\Models\Company::findOrFail(get_active_company())));
        window.emails = @json(config('app.emails'));
        window.sessionLifetime = @json(config('session.lifetime'));
    </script>
    @yield('head-script')
</head>
<body>
    <div id="app" class="clearfix has-sidebar">
        <div id="sidebar" class="sidebar-container">
            <div class="sidebar-wrapper">
                @yield('sidebar-content')
            </div>
        </div>
        <div class="main-content-container">
            <div class="top-navbar" id="top-navbar-menu" v-cloak>
                <b-navbar toggleable="sm" class="top-navigation-bar justify-content-space-between justify-content-sm-start">

                    <b-navbar-toggle target="sidebar-menu" class="sidebar-toggle js-toggle-navbar-menu d-inline-flex d-md-none">
                        <template>
                            <menu-icon></menu-icon>
                        </template>
                    </b-navbar-toggle>

                    <b-navbar-toggle target="top-navbar" class="top-navbar-toggle m-0 ml-sm-auto d-inline-flex d-sm-none">
                        <template>
                            <more-vertical-icon></more-vertical-icon>
                        </template>
                    </b-navbar-toggle>

                    <b-navbar-nav class="navbar-menu m-0 ml-md-3">
                        <div class="pm-logo-reversed d-md-none text-center">
                            <img src="/img/logo-reversed.png" alt="Logo Reversed">
                        </div>
                        @if (!auth()->user()->isAdmin())
                        <b-nav-item href="{{ route('dashboard') }}" active>
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </b-nav-item>
                        @endif
                        @can('list', \App\Models\Campaign::class)
                        <b-nav-item href="{{ route('campaigns.index') }}" active>
                            <span class="pm-font-campaigns-icon"></span>
                            <span>Campaigns</span>
                        </b-nav-item>
                        @endcan
                        @can('list', \App\Models\CampaignScheduleTemplate::class)
                        <b-nav-item href="{{ route('template.index') }}">
                            <span class="pm-font-templates-icon"></span>
                            <span>Templates</span>
                        </b-nav-item>
                        @endcan
                        @can('list', \App\Models\User::class)
                        <b-nav-item href="{{ route('user.index') }}">
                            <span class="pm-font-user-icon"></span>
                            <span>Users</span>
                        </b-nav-item>
                        @endcan
                        @if (auth()->user()->isAdmin())
                        <b-nav-item href="{{ route('company.index') }}">
                            <span class="pm-font-companies-icon"></span>
                            <span>Companies</span>
                        </b-nav-item>
                        @endif
                    </b-navbar-nav>

                    <b-collapse is-nav id="top-navbar">
                        <b-navbar-nav class="ml-auto navbar-menu-extra">
                            @impersonating
                            <b-nav-item right href="{{ route('admin.impersonate-leave') }}">
                                <i class="fas fa-sign-out-alt"></i>
                            </b-nav-item>
                            @endImpersonating
                            <b-nav-item-dropdown class="profile" right variant="link" size="lg" no-caret>
                                <template slot="button-content">
                                    <img :src="loggedUser.image_url" alt="Avatar" v-if="loggedUser.image_url">
                                    <div class="avatar-placeholder" v-if="!loggedUser.image_url">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="user-name">
                                        <div>@{{ loggedUser.name || loggedUser.email }}</div>
                                        @if (!auth()->user()->isAdmin())
                                        <small>{{ get_active_company_model()->name }}</small>
                                        @endif
                                    </div>
                                </template>
                                @if (!auth()->user()->isAdmin() && auth()->user()->companies()->count() > 1)
                                    <b-dropdown-item href="{{ route('selector.select-active-company') }}">Switch Company</b-dropdown-item>
                                @endif
                                <b-dropdown-item href="{{ route('profile.index') }}">Profile</b-dropdown-item>
                                <b-dropdown-item @click="signout('{{ route('logout') }}')">Signout</b-dropdown-item>
                            </b-nav-item-dropdown>
                        </b-navbar-nav>
                    </b-collapse>
                </b-navbar>
            </div>

            <div class="main-content">
                @yield('main-content')
            </div>
        </div>
    </div>
    @yield('body-script')
</body>
</html>
