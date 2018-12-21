<template>
    <div class="top-navigation-bar">
        <a href="javascript:;" class="sidebar-toggle">
            <menu-icon></menu-icon>
        </a>
        <a href="index.html" class="logo">
            <img src="/img/logo.png">
        </a>
        <nav class="navbar-menu">
            <img src="/img/logo-reversed.png" alt="Logo Reversed" class="logo-reversed">
            <ul class="nav">
                <li class="menu-item">
                    <a href="" class="active">
                        <campaigns-icon></campaigns-icon>
                        <span>Campaigns</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="">
                        <templates-icon></templates-icon>
                        <span>Templates</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="">
                        <span class="pm-font-phone-icon"></span>
                        <span>Users</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="">
                        <companies-icon></companies-icon>
                        <span>Companies</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="">
                        <system-icon></system-icon>
                        <span>System</span>
                    </a>
                </li>
            </ul>
        </nav>
        <nav class="navbar-menu-extra">
            <ul class="nav clearfix">
                <li class="menu-item">
                    <b-dropdown variant="link" no-caret>
                        <template slot="button-content">
                            <user-icon></user-icon>
                        </template>
                        <b-dropdown-item href="#">Action</b-dropdown-item>
                        <b-dropdown-item href="#">Another action</b-dropdown-item>
                        <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                    </b-dropdown>
                </li>
                <li class="menu-item">
                    <b-dropdown variant="link" no-caret>
                        <template slot="button-content">
                            <user-icon></user-icon>
                        </template>
                        <b-dropdown-item href="#">Action</b-dropdown-item>
                        <b-dropdown-item href="#">Another action</b-dropdown-item>
                        <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                    </b-dropdown>
                </li>
                <li class="menu-item menu-item-profile">
                    <b-dropdown variant="link" no-caret>
                        <template slot="button-content">
                            <span>Jhon Doe</span>
                            <img src="http://lorempixel.com/60/60/" alt="Avatar">
                        </template>
                        <b-dropdown-item href="#">Profile</b-dropdown-item>
                        <b-dropdown-item href="#">Another action</b-dropdown-item>
                        <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                    </b-dropdown>
                </li>
            </ul>
        </nav>
    </div>
</template>
<script>
    require("./top-navigation-bar.scss");

    export default {
        props: {},
        data() {
            return {};
        }
    }
</script>
