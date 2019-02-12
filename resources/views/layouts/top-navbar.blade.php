<div class="top-navbar" id="top-navbar-menu" v-cloak>
    <b-navbar toggleable="sm" class="top-navigation-bar justify-content-space-between justify-content-sm-start">

        <b-navbar-toggle target="sidebar-menu" class="sidebar-toggle js-toggle-navbar-menu d-inline-flex d-md-none">
            <template>
                <menu-icon></menu-icon>
            </template>
        </b-navbar-toggle>

        <b-navbar-brand href="{{ url('/') }}" class="ml-sm-4 ml-md-6 ml-lg-8">
            <img src="/img/logo.png" height="40px" class="logo d-sm-none">
            <img src="/img/logo-large.png" height="40px" class="logo-large d-none d-sm-block">
        </b-navbar-brand>

        <b-navbar-toggle target="top-navbar" class="top-navbar-toggle m-0 ml-sm-auto d-inline-flex d-sm-none">
            <template>
                <more-vertical-icon></more-vertical-icon>
            </template>
        </b-navbar-toggle>

        <b-navbar-nav class="navbar-menu m-0 ml-md-3 ml-lg-6 ml-xl-10">
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
                        <div class="avatar-placeholder" v-if="!loggedUser.image_Url">
                            <i class="fas fa-user"></i>
                        </div>
                        <span>@{{ loggedUser.first_name }}</span>
                    </template>
                    <b-dropdown-item href="{{ route('profile.index') }}">Profile</b-dropdown-item>
                    <b-dropdown-item @click="signout('{{ route('logout') }}')">Signout</b-dropdown-item>
                </b-nav-item-dropdown>
            </b-navbar-nav>
        </b-collapse>
    </b-navbar>
</div>
