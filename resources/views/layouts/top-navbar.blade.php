<div class="top-navbar">
    <b-navbar toggleable="sm" class="top-navigation-bar justify-content-space-between justify-content-sm-start">

        <b-navbar-toggle target="sidebar-menu" class="sidebar-toggle js-toggle-navbar-menu d-inline-flex d-md-none">
            <template>
                <menu-icon></menu-icon>
            </template>
        </b-navbar-toggle>

        <b-navbar-brand href="#" class="ml-sm-4 ml-md-6 ml-lg-8">
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
            <b-nav-item href="#" active>
                <span class="pm-font-campaigns-icon"></span>
                <span>Campaigns</span>
            </b-nav-item>
            <b-nav-item href="#">
                <span class="pm-font-templates-icon"></span>
                <span>Templates</span>
            </b-nav-item>
            <b-nav-item href="#">
                <span class="pm-font-phone-icon"></span>
                <span>Users</span>
            </b-nav-item>
            <b-nav-item href="#">
                <span class="pm-font-companies-icon"></span>
                <span>Companies</span>
            </b-nav-item>
            <b-nav-item href="#">
                <span class="pm-font-system-icon"></span>
                <span>System</span>
            </b-nav-item>
        </b-navbar-nav>

        <b-collapse is-nav id="top-navbar">
            <b-navbar-nav class="ml-auto navbar-menu-extra">
                <b-nav-item-dropdown right variant="link" size="lg" no-caret>
                    <template slot="button-content">
                        <span>
                            <i class="pm-font-notification-icon"></i>
                        </span>
                        <span class="d-sm-none">Notifications</span>
                    </template>
                    <b-dropdown-item href="#">Notification 1</b-dropdown-item>
                    <b-dropdown-item href="#">Notification 2</b-dropdown-item>
                </b-nav-item-dropdown>
                <b-nav-item-dropdown right variant="link" size="lg" no-caret>
                    <template slot="button-content">
                        <span>
                            <i class="pm-font-help-icon"></i>
                        </span>
                        <span class="d-sm-none">Help</span>
                    </template>
                    <b-dropdown-item href="#">Help 1</b-dropdown-item>
                    <b-dropdown-item href="#">Help 2</b-dropdown-item>
                </b-nav-item-dropdown>
                <b-nav-item-dropdown class="profile" right variant="link" size="lg" no-caret>
                    <template slot="button-content">
                        <img src="http://lorempixel.com/60/60/" alt="Avatar">
                        <span>Jhon Doe</span>
                    </template>
                    <b-dropdown-item href="#">Profile</b-dropdown-item>
                    <b-dropdown-item href="#">Signout</b-dropdown-item>
                </b-nav-item-dropdown>
            </b-navbar-nav>
        </b-collapse>
    </b-navbar>
</div>