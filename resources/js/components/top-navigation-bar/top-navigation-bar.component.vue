<template>
    <div class="top-navigation-bar" :class="{'open': openMenu}" v-click-outside="onClickOutside">
        <a href="javascript:;" class="sidebar-toggle" @click="openMenu = true">
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
                        <span class="pm-font-campaigns-icon"></span>
                        <span>Campaigns</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="">
                        <span class="pm-font-templates-icon"></span>
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
                        <span class="pm-font-companies-icon"></span>
                        <span>Companies</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="">
                        <span class="pm-font-system-icon"></span>
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
                            <span class="pm-font-notification-icon"></span>
                        </template>
                        <b-dropdown-item href="#">Action</b-dropdown-item>
                        <b-dropdown-item href="#">Another action</b-dropdown-item>
                        <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                    </b-dropdown>
                </li>
                <li class="menu-item">
                    <b-dropdown variant="link" no-caret>
                        <template slot="button-content">
                            <span class="pm-font-help-icon"></span>
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
            return {
                openMenu: false
            };
        },
        methods: {
            onClickOutside: function() {
                this.openMenu = false
            }
        }
    }
</script>
