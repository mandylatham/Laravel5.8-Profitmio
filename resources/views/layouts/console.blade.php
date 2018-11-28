<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="apple-touch-icon" href="{{ secure_asset('images/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ secure_asset('images/favicon.ico') }}">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ secure_url('/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/site.css') }}">
    <!-- Plugins -->
    <link rel="stylesheet" href="{{ secure_url('/vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/vendor/asscrollable/asScrollable.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/vendor/switchery/switchery.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/vendor/intro-js/introjs.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/vendor/slidepanel/slidePanel.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/vendor/flag-icon-css/flag-icon.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/vendor/waves/waves.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/vendor/chartist/chartist.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/vendor/jvectormap/jquery-jvectormap.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/vendor/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/v1.css') }}">
    <link rel="stylesheet" href="{{ secure_url('css/mailbox.css') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="{{ secure_url('/fonts/material-design/material-design.min.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/fonts/brand-icons/brand-icons.min.css') }}">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
    <!--[if lt IE 9]>
    <script src="{{ secure_url('/vendor/html5shiv/html5shiv.min.js') }}"></script>
    <![endif]-->
    <!--[if lt IE 10]>
    <script src="{{ secure_url('/vendor/media-match/media.match.min.js') }}"></script>
    <script src="{{ secure_url('/vendor/respond/respond.min.js') }}"></script>
    <![endif]-->
    <!-- Scripts -->
    <script src="{{ secure_url('/vendor/breakpoints/breakpoints.js') }}"></script>

    @yield('header')

<!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
        Breakpoints();
    </script>
    <style type="text/css">
        html,body {
            height: 100%;
            background: #333 url('/images/triangular.png') center center repeat;
        }
        body.animsition,
        body.site-navbar-small
        {
            padding-top: 4.3rem;
        }
        @media all and (min-width: 767px) {
            .site-navbar-small .site-navbar .navbar-brand {
                min-width: 180px;
            }
            @if (\Auth::user()->access != 'Client')
            .page-main {
                padding-top: 50px;
            }
            @endif
        }

        @if (\Auth::user()->access == 'Client')
        .page-aside {
            padding-top: -50px;
            height: 100%;
        }
        .page-aside-fixed .page-aside {
            top: 0;
            height: 100%;
        }
        @endif

        .site-navbar {
            border-bottom: 1px solid #878787;
            // background: rgb(182, 182, 182);
        }
        .site-navbar .nav-item {
            // background-color: rgba(0, 0, 0, 0.44);
        }
        .navbar-default .navbar-toolbar .nav-link {
            // color: #fff;
        }
        .nav-link > i {
            padding-right: 5px;
        }
        .site-footer {
            // background: #333 url('/images/hexabump.png') center center repeat;
            background: #083756;
            border-top: 1px solid #cdcdcd;
        }
        .site-footer a {
            color: rgb(123, 163, 193);
        }
        @media(min-width: 767px) {
            .page {
                // padding-top: 50px;
            }
            .site-navbar {
                border-bottom: 1px solid #969696;
            }
        }
        /*
        .page {
            background: transparent;
        }
        .page-title {
            font-family: "Archivo Narrow", "Open Sans", sans-serif;
            color: #fff;
            text-shadow: 2px 2px 8px #202020;
        }
        .page-header {
            padding: 15px 30px;
        }
        */
        .site-menubar {
            background: #083756;
            border-bottom: 1px solid #e5e5e5;
        }
        .panel {
            box-shadow: 2px 2px 10px #555;
        }
        .site-menu-item > .dropdown-menu {
            border: 0.5px solid #222;
            box-shadow: 4px 4px 8px #333;
        }
        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_desc:after,
        table.dataTable thead .sorting_asc:after {
            display: none
        }
        .deleteBox .sa-button-container {
            display: flex;
            justify-content: space-around;
        }
        .deleteBox button {
            // padding-top: 2px;
            // padding-bottom: 2px;
        }
        .deleteBox .confirm.btn-primary {
            background-color: #a93f30;
            border-color: #893829;
        }
        .deleteBox .cancel {
            background-color: #418551;
            border-color: #33663a;
            color: #fff;
        }
        .select2-container .select2-selection--single {
            height: inherit;
            padding: 6px 0;
            border-color: #e0e0e0;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            min-height: 40px;
        }
        .page, .page-aside {
            background: transparent url('{{ secure_url('images/debut_light.png') }}') repeat;
        }
        .change-company-selector form {
            display: block;
            padding: 0 15px;
            line-height: 3.572rem;
            white-space: nowrap;
            cursor: pointer;
            top: 0;
            left: 0;
        }
        .change-company-selector form select {
            display: inline-block;
        }

        @yield('manualStyle')
    </style>
</head>
<body class="animsition site-navbar-small page-aside-fixed page-aside-left site-menubar-hide app-mailbox" style="animation-duration: 800ms; opacity: 1;">
<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided"
                data-toggle="menubar">
            <span class="sr-only">Toggle navigation</span>
            <span class="hamburger-bar"></span>
        </button>
        <a class="navbar-brand navbar-brand-center" href="{{ secure_url('/dashboard') }}">
            <img class="navbar-brand-logo navbar-brand-logo-normal" src="{{ secure_asset('images/favicon.png') }}"
                 title="Profit Miner">
            <img class="navbar-brand-logo navbar-brand-logo-special" src="{{ secure_asset('images/favicon.png') }}"
                 title="Profit Miner">
            <span class="navbar-brand-text hidden-xs-down"> Profit Miner</span>
        </a>
    </div>
    <div class="navbar-container container-fluid d-flex align-items-center justify-content-end">
        @impersonating
        <a class="btn-round btn btn-primary d-none d-md-inline btn-sm" href="{{ route('admin.impersonate-leave') }}">Leave impersonation</a>
        @endImpersonating
        <!-- Navbar Collapse -->
        <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
            <!-- Navbar Toolbar -->
            <ul class="nav navbar-toolbar">
                <li class="nav-item hidden-float" id="toggleMenubar">
                    <a class="nav-link" data-toggle="menubar" href="#" role="button">
                        <i class="icon hamburger hamburger-arrow-left">
                            <span class="sr-only">Toggle menubar</span>
                            <span class="hamburger-bar"></span>
                        </i>
                    </a>
                </li>
            </ul>
            <!-- End Navbar Toolbar -->
            <!-- Navbar Toolbar Right -->
            <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
                @if (session('pendingNotificationCount'))
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)" title="Notifications"
                       aria-expanded="false" data-animation="scale-up" role="button">
                        <i class="icon md-notifications" aria-hidden="true"></i>
                        <span class="badge badge-pill badge-danger up">{{ session('pendingNotificationCount') }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-media" role="menu">
                        <div class="dropdown-menu-header">
                            <h5>NOTIFICATIONS</h5>
                            <span class="badge badge-round badge-danger">New 5</span>
                        </div>
                        <div class="list-group">
                            <div data-role="container">
                                <div data-role="content">
                                    @foreach(session('pendingNotifications') as $notification)
                                    <a class="list-group-item dropdown-item" href="{{ secure_url('notifications/' . $notificaiton->id) }}" role="menuitem">
                                        <div class="media">
                                            <div class="pr-10">
                                                <i class="icon md-receipt bg-red-600 white icon-circle" aria-hidden="true"></i>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="media-heading">{{ $notification->message }}</h6>
                                                <time class="media-meta" datetime="{{ $notification->created_at->toDateTimeString() }}">{{ $notification->created_at }}</time>
                                            </div>
                                        </div>
                                    </a>
									@endforeach
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-menu-footer">
                            <a class="dropdown-menu-footer-btn" href="javascript:void(0)" role="button">
                                <i class="icon md-settings" aria-hidden="true"></i>
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)" role="menuitem">
                                All notifications
                            </a>
                        </div>
                    </div>
                </li>
                @endif
                @if (session('unreadMessagesCount'))
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)" title="Messages"
                       aria-expanded="false" data-animation="scale-up" role="button">
                        <i class="icon md-email" aria-hidden="true"></i>
                        <span class="badge badge-pill badge-info up">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-media" role="menu">
                        <div class="dropdown-menu-header" role="presentation">
                            <h5>MESSAGES</h5>
                            <span class="badge badge-round badge-info">New 3</span>
                        </div>
                        <div class="list-group" role="presentation">
                            <div data-role="container">
                                <div data-role="content">
                                    @foreach (session('unreadMessages') as $response)
                                    <a class="list-group-item dropdown-item" href="javascript:void(0)" role="menuitem">
                                        <div class="media">
                                            <div class="pr-10">
                                                <span class="avatar avatar-sm avatar-online"><img src="https://placehold.it/35x35" alt="..." />
                                                    <i></i>
                                                </span>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="media-heading">{{ $response->target->name }}</h6>
                                                <div class="media-meta">
                                                    <time datetime="{{ $response->created_at->toDateTimeString() }}">{{ $response->created_at }}</time>
                                                </div>
                                                <div class="media-detail">{{ $response->text }}</div>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-menu-footer" role="presentation">
                            <a class="dropdown-menu-footer-btn" href="javascript:void(0)" role="button">
                                <i class="icon md-settings" aria-hidden="true"></i>
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)" role="menuitem">
                                See all messages
                            </a>
                        </div>
                    </div>
                </li>
                @endif
                <li class="nav-item dropdown">
                    <a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false"
                       data-animation="scale-up" role="button">
                        <span class="avatar avatar-online">
                            <img src="{{ secure_url('/images/default-user.png') }}">
                            <i></i>
                        </span>
                        <span style="margin-left: 8px;">{{ auth()->user()->first_name }}</span>
                    </a>
                    <div class="dropdown-menu" role="menu">
                        <a class="dropdown-item" href="{{ route('profile.index') }}" role="menuitem"><i class="icon md-account" aria-hidden="true"></i> Setting</a>
                        <a class="dropdown-item" href="{{ route('logout') }}" role="menuitem"><i class="icon md-power" aria-hidden="true"></i> Logout</a>
                    </div>
                </li>
            </ul>
            <!-- End Navbar Toolbar Right -->
        </div>
        <!-- End Navbar Collapse -->
        <!-- Site Navbar Seach -->
        <div class="collapse navbar-search-overlap" id="site-navbar-search">
            <form role="search">
                <div class="form-group">
                    <div class="input-search">
                        <i class="input-search-icon md-search" aria-hidden="true"></i>
                        <input type="text" class="form-control" name="site-search" placeholder="Search...">
                        <button type="button" class="input-search-close icon md-close" data-target="#site-navbar-search"
                                data-toggle="collapse" aria-label="Close"></button>
                    </div>
                </div>
            </form>
        </div>
        <!-- End Site Navbar Seach -->
    </div>
</nav>

<div class="site-menubar">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu">
                    <li class="site-menu-category">General</li>
                    @if (!auth()->user()->isAdmin())
                        <li class="site-menu-item">
                            <a href="{{ secure_url('/dashboard') }}" class=" waves-effect waves-classic">
                                <i class="site-menu-icon icon oi-dashboard" aria-hidden="true"></i>
                                <span class="site-menu-title">Dashboard</span>
                            </a>
                        </li>
                    @endif
                    @can('list', \App\Models\Campaign::class)
                        <li class="site-menu-item">
                            <a href="{{ route('campaign.index') }}" class=" waves-effect waves-classic">
                                <i class="site-menu-icon icon oi-megaphone" aria-hidden="true"></i>
                                <span class="site-menu-title">Campaigns</span>
                            </a>
                        </li>
                    @endcan
                    @can('list', \App\Models\CampaignScheduleTemplate::class)
                        <li class="site-menu-item">
                            <a href="{{ route('template.index') }}" class=" waves-effect waves-classic">
                                <i class="site-menu-icon icon fa-file-text-o" aria-hidden="true"></i>
                                <span class="site-menu-title">Templates</span>
                            </a>
                        </li>
                    @endcan
                    @can('list', \App\Models\User::class)
                        <li class="site-menu-item">
                            <a href="{{ route('user.index') }}" class=" waves-effect waves-classic">
                                <i class="site-menu-icon icon fa-users" aria-hidden="true"></i>
                                <span class="site-menu-title">Users</span>
                            </a>
                        </li>
                    @endcan
                    @if (auth()->user()->isAdmin())
                        <li class="site-menu-item">
                            <a href="{{ route('company.index') }}" class=" waves-effect waves-classic">
                                <i class="site-menu-icon icon fa-users" aria-hidden="true"></i>
                                <span class="site-menu-title">Companies</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->isAdmin())
                        <li class="site-menu-category">System</li>
                        <li class="dropdown site-menu-item has-sub">
                            <a data-toggle="dropdown" href="javascript:void(0)" data-dropdown-toggle="false" class="waves-effect waves-classic">
                                <i class="site-menu-icon icon fa-server" aria-hidden="true"></i>
                                <span class="site-menu-title">System</span>
                                <span class="site-menu-arrow"></span>
                            </a>
                            <div class="dropdown-menu">
                                <div class="site-menu-scroll-wrap is-list scrollable is_enabled scrollable-vertical" style="position: relative">
                                    <div class="scrollable-container" style="max-height: 420px; width: 232px;">
                                        <div class="scrollable-content" style="width: 217px;">
                                            <ul class="site-menu-sub site-menu-normal-list">
                                                <li class="site-menu-item">
                                                    <a href="{{ route('system.drop.index') }}" class=" waves-effect waves-classic">
                                                        <i class="site-menu-icon icon fa-paper-plane" aria-hidden="true"></i>
                                                        <span class="site-menu-title">Drop Management</span>
                                                    </a>
                                                </li>
                                                <li class="site-menu-item">
                                                    <a href="{{ route('system.report.index') }}" class=" waves-effect waves-classic">
                                                        <i class="site-menu-icon icon fa-bar-chart" aria-hidden="true"></i>
                                                        <span class="site-menu-title">Reports</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="scrollable-bar scrollable-bar-vertical scrollable-bar-hide" draggable="false">
                                        <div class="scrollable-bar-handle" style="height: 350.283px;"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endif
                    @if (!auth()->user()->isAdmin())
                        <li class="site-menu-item float-right change-company-selector">
                            <form method="post" action="{{ route('selector.update-active-company') }}">
                                <select class="form-control" name="company" required onchange="this.form.submit()">
                                    <option disabled>Change Company</option>
                                    @foreach (auth()->user()->getActiveCompanies() as $company)
                                        <option value="{{ $company->id }}" {{ $company->id == get_active_company() ? 'disabled enabled' : '' }}>{{ $company->name }} {{ get_active_company() == $company->id ? '(ACTIVE)' : '' }}</option>
                                    @endforeach
                                </select>
                                {{ csrf_field() }}
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

@yield('content')

<footer class="site-footer">
    <div class="site-footer-legal">© 2017 Profit Miner</div>
    <div class="site-footer-right">
        <a href="{{ secure_url('/terms') }}" class="">Terms of Service</a>
    </div>
</footer>

<!-- Core  -->
<script src="{{ secure_url('/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
<script src="{{ secure_url('/vendor/jquery/jquery.js') }}"></script>
<script src="{{ secure_url('/vendor/tether/tether.js') }}"></script>
<script src="{{ secure_url('/vendor/bootstrap/bootstrap.js') }}"></script>
<script src="{{ secure_url('/vendor/animsition/animsition.js') }}"></script>
<script src="{{ secure_url('/vendor/mousewheel/jquery.mousewheel.js') }}"></script>
<script src="{{ secure_url('/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
<script src="{{ secure_url('/vendor/asscrollable/jquery-asScrollable.js') }}"></script>
<script src="{{ secure_url('/vendor/waves/waves.js') }}"></script>
<!-- Plugins -->
<script src="{{ secure_url('/vendor/switchery/switchery.min.js') }}"></script>
<script src="{{ secure_url('/vendor/intro-js/intro.js') }}"></script>
<script src="{{ secure_url('/vendor/screenfull/screenfull.js') }}"></script>
<script src="{{ secure_url('/vendor/slidepanel/jquery-slidePanel.js') }}"></script>
<!-- Scripts -->
<script src="{{ secure_url('/js/State.js') }}"></script>
<script src="{{ secure_url('/js/Component.js') }}"></script>
<script src="{{ secure_url('/js/Plugin.js') }}"></script>
<script src="{{ secure_url('/js/Base.js') }}"></script>
<script src="{{ secure_url('/js/Config.js') }}"></script>
<script src="{{ secure_url('js/Section/Menubar.js') }}"></script>
<script src="{{ secure_url('js/Section/Sidebar.js') }}"></script>
<script src="{{ secure_url('js/Section/PageAside.js') }}"></script>
<script src="{{ secure_url('js/Plugin/menu.js') }}"></script>
<!-- Config -->
<script src="{{ secure_url('/js/config/colors.js') }}"></script>
<script src="{{ secure_url('js/config/tour.js') }}"></script>
<script>
    Config.set('assets', '/');
</script>
<script src="{{ secure_url('/vendor/select2/select2.min.js') }}"></script>
<script src="{{ secure_url('/vendor/slidepanel/jquery-slidePanel.js') }}"></script>
<script src="{{ secure_url('/vendor/bootstrap-markdown/bootstrap-markdown.js') }}"></script>
<script src="{{ secure_url('/vendor/marked/marked.js') }}"></script>
<script src="{{ secure_url('/vendor/to-markdown/to-markdown.js') }}"></script>
<script src="{{ secure_url('/vendor/aspaginator/jquery.asPaginator.min.js') }}"></script>
<script src="{{ secure_url('/vendor/bootbox/bootbox.js') }}"></script>
<!-- Scripts -->
<script src="{{ secure_url('/js/Site.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/asscrollable.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/slidepanel.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/switchery.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/action-btn.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/asselectable.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/editlist.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/select2.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/aspaginator.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/animate-list.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/selectable.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/material.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/bootbox.js') }}"></script>
<script src="{{ secure_url('/js/BaseApp.js') }}"></script>
<script src="{{ secure_url('/js/App/Mailbox.js') }}"></script>

<script src="{{ secure_url('/js/Plugin/toastr.min.js') }}"></script>
<script src="{{ secure_url('/vendor/toastr/toastr.min.js') }}"></script>

@yield('scriptTags')

<script type="text/javascript">
    var response_checksum = "";

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".campaign-edit-button").click(function() {
        var url = $(this).data("url");
        window.location.href = url;
    });

    (function (global, factory) {
        if (typeof define === "function" && define.amd) {
            define('/App/Mailbox', ['exports', 'BaseApp'], factory);
        } else if (typeof exports !== "undefined") {
            factory(exports, require('BaseApp'));
        } else {
            var mod = {
                exports: {}
            };
            factory(mod.exports, global.BaseApp);
            global.AppMailbox = mod.exports;
        }
    })(this, function (exports, _BaseApp2) {
        'use strict';

        Object.defineProperty(exports, "__esModule", {
            value: true
        });
        exports.getInstance = exports.run = exports.AppMailbox = undefined;

        var _BaseApp3 = babelHelpers.interopRequireDefault(_BaseApp2);

        var AppMailbox = function (_BaseApp) {
            babelHelpers.inherits(AppMailbox, _BaseApp);

            function AppMailbox() {
                babelHelpers.classCallCheck(this, AppMailbox);
                return babelHelpers.possibleConstructorReturn(this, (AppMailbox.__proto__ || Object.getPrototypeOf(AppMailbox)).apply(this, arguments));
            }

            babelHelpers.createClass(AppMailbox, [{
                key: 'processed',
                value: function processed() {
                    babelHelpers.get(AppMailbox.prototype.__proto__ || Object.getPrototypeOf(AppMailbox.prototype), 'processed', this).call(this);

                    this.$actionBtn = $('.site-action');
                    this.$actionToggleBtn = this.$actionBtn.find('.site-action-toggle');
                    this.$addMainForm = $('#addMailForm').modal({
                        show: false
                    });
                    this.$content = $('#mailContent');

                    this.setupActionBtn();
                    this.bindListChecked();
                }
            }, {
                key: 'getDefaultActions',
                value: function getDefaultActions() {
                    return Object.assign(babelHelpers.get(AppMailbox.prototype.__proto__ || Object.getPrototypeOf(AppMailbox.prototype), 'getDefaultActions', this).call(this), {
                        listChecked: function listChecked(checked) {
                            var api = this.$actionBtn.data('actionBtn');
                            if (checked) {
                                api.show();
                            } else {
                                api.hide();
                            }
                        }
                    });
                }
            }, {
                key: 'getDefaultState',
                value: function getDefaultState() {
                    return Object.assign(babelHelpers.get(AppMailbox.prototype.__proto__ || Object.getPrototypeOf(AppMailbox.prototype), 'getDefaultState', this).call(this), {
                        listChecked: false
                    });
                }
            }, {
                key: 'setupActionBtn',
                value: function setupActionBtn() {
                    var _this2 = this;

                    this.$actionToggleBtn.on('click', function (e) {
                        if (!_this2.getState('listChecked')) {
                            _this2.$addMainForm.modal('show');
                            e.stopPropagation();
                        }
                    });
                }
            }, {
                key: 'bindListChecked',
                value: function bindListChecked() {
                    var _this3 = this;

                    this.$content.on('asSelectable::change', function (e, api, checked) {
                        _this3.setState('listChecked', checked);
                    });
                }
            }]);
            return AppMailbox;
        }(_BaseApp3.default);

        var instance = null;

        function getInstance() {
            if (!instance) {
                instance = new AppMailbox();
            }
            return instance;
        }

        function run() {
            var app = getInstance();
            app.run();
        }

        exports.default = AppMailbox;
        exports.AppMailbox = AppMailbox;
        exports.run = run;
        exports.getInstance = getInstance;
    });

    $(document).ready(function() {
        AppMailbox.run();
    });

    @yield('scripts')
</script>
<div class="slidePanel slidePanel-right" style="transform: translate3d(0%, 0px, 0px);">
    <div class="slidePanel-scrollable scrollable is-enabled scrollable-vertical" style="position: relative;">
        <div class="scrollable-container" style="height: 211px; width: 700px;">
            <div class="slidePanel-content scrollable-content" style="width: 700px;">
                <header class="slidePanel-header">
                    <div class="slidePanel-actions" aria-label="actions" role="group">
                        <button type="button" class="btn btn-icon btn-pure btn-inverse slidePanel-close actions-top icon md-close" aria-hidden="true"></button>
                        <div class="btn-group actions-bottom btn-group-flat" role="group">
                            <div class="float-left" style="position:relative;">
                                <button type="button" class="btn btn-icon btn-pure btn-inverse icon md-more" data-toggle="dropdown" aria-expanded="false" aria-hidden="true"></button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <a class="dropdown-item" href="javascript:void(0)"><i class="icon md-inbox" aria-hidden="true"></i> Archive</a>
                                    <a class="dropdown-item" href="javascript:void(0)"><i class="icon md-alert-circle" aria-hidden="true"></i> Report Spam</a>
                                    <a class="dropdown-item" href="javascript:void(0)"><i class="icon md-delete" aria-hidden="true"></i> Delete</a>
                                    <a class="dropdown-item" href="javascript:void(0)"><i class="icon md-print" aria-hidden="true"></i> Print</a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-icon btn-pure btn-inverse icon md-chevron-left" aria-hidden="true"></button>
                            <button type="button" class="btn btn-icon btn-pure btn-inverse icon md-chevron-right" aria-hidden="true"></button>
                        </div>
                    </div>
                    <h1>Titudin venenatis ipsum ac feugiat. Vestibulum ullamcorper Neque quam.</h1>
                </header>
                <div class="slidePanel-inner">
                    <section class="slidePanel-inner-section">
                        <div class="mail-header">
                            <div class="mail-header-main">
                                <a class="avatar" href="javascript:void(0)">
                                    <img src="../../../../global/portraits/2.jpg" alt="...">
                                </a>
                                <div>
                                    <span class="name">Seevisual</span>
                                </div>
                                <div><a href="javascript:void(0)">Mazhesee@gmail.com</a> to <a href="javascript:void(0)">me</a>
                                    <span class="identity"><i class="md-circle font-size-10 red-600" aria-hidden="true"></i>Work</span>
                                </div>
                            </div>
                            <div class="mail-header-right">
                                <span class="time">3 minutes ago</span>
                                <div class="btn-group actions" role="group">
                                    <button type="button" class="btn btn-icon btn-pure btn-default"><i class="icon md-star" aria-hidden="true"></i></button>
                                    <button type="button" class="btn btn-icon btn-pure btn-default"><i class="icon md-mail-reply" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="mail-content">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi id neque quam.
                                Aliquam sollicitudin venenatis ipsum ac feugiat. Vestibulum ullamcorper
                                sodales nisi nec condimentum. Mauris convallis mauris at pellentesque volutpat.
                                Phasellus at ultricies neque, quis malesuada augue. Donec eleifend condimentum
                                nisl eu consectetur. Integer eleifend, nisl venenatis consequat iaculis,
                                lectus arcu malesuada sem, dapibus porta quam lacus eu neque.Lorem ipsum
                                dolor sit amet, consectetur adipiscing elit. </p>
                            <p>Morbi id neque quam. Aliquam sollicitudin venenatis ipsum ac feugiat. Vestibulum
                                ullamcorper sodales nisi nec condimentum. Mauris convallis mauris at pellentesque
                                volutpat. Phasellus at ultricies neque, quis malesuada augue. Donec eleifend
                                condimentum nisl eu consectetur. Integer eleifend, nisl venenatis consequat
                                iaculis, lectus arcu malesuada sem, dapibus porta quam lacus eu neque.</p>
                        </div>
                        <div class="mail-attachments">
                            <p><i class="icon md-attachment-alt"></i>Attachments | <a href="javascript:void(0)">Download All</a></p>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <span class="name">Rstuvwxyz.mp4</span>
                                    <span class="size">(2.40M)</span>
                                    <button type="button" class="btn btn-icon btn-pure btn-default"><i class="icon md-download" aria-hidden="true"></i></button>
                                </li>
                                <li class="list-group-item">
                                    <span class="name">Demo.doc</span>
                                    <span class="size">(2.40M)</span>
                                    <button type="button" class="btn btn-icon btn-pure btn-default"><i class="icon md-download" aria-hidden="true"></i></button>
                                </li>
                            </ul>
                        </div>
                    </section>
                    <section class="slidePanel-inner-section">
                        <div class="mail-header">
                            <div class="mail-header-main">
                                <a class="avatar" href="javascript:void(0)">
                                    <img src="../../../../global/portraits/2.jpg" alt="...">
                                </a>
                                <div>
                                    <span class="name">Seevisual</span>
                                </div>
                                <div><a href="javascript:void(0)">Mazhesee@gmail.com</a> to <a href="javascript:void(0)">me</a>
                                    <span class="identity"><i class="md-circle font-size-10 red-600" aria-hidden="true"></i>Work</span>
                                </div>
                            </div>
                            <div class="mail-header-right">
                                <span class="time">2 minutes ago</span>
                                <div class="btn-group actions" role="group">
                                    <button type="button" class="btn btn-icon btn-pure btn-default"><i class="icon md-star" aria-hidden="true"></i></button>
                                    <button type="button" class="btn btn-icon btn-pure btn-default"><i class="icon md-mail-reply" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="mail-content">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi id neque quam.
                                Aliquam sollicitudin venenatis ipsum ac feugiat. Vestibulum ullamcorper
                                sodales nisi nec condimentum. Mauris convallis mauris at pellentesque volutpat.</p>
                        </div>
                    </section>
                    <section class="slidePanel-inner-section">
                        <div class="mail-header">
                            <div class="mail-header-main">
                                <a class="avatar" href="javascript:void(0)">
                                    <img src="../../../../global/portraits/2.jpg" alt="...">
                                </a>
                                <div>
                                    <span class="name">Seevisual</span>
                                </div>
                                <div><a href="javascript:void(0)">Mazhesee@gmail.com</a> to <a href="javascript:void(0)">me</a>
                                    <span class="identity"><i class="md-circle font-size-10 red-600" aria-hidden="true"></i>Work</span>
                                </div>
                            </div>
                            <div class="mail-header-right">
                                <span class="time">1 minutes ago</span>
                                <div class="btn-group actions" role="group">
                                    <button type="button" class="btn btn-icon btn-pure btn-default"><i class="icon md-star" aria-hidden="true"></i></button>
                                    <button type="button" class="btn btn-icon btn-pure btn-default"><i class="icon md-mail-reply" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="mail-content">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi id neque quam.
                                Aliquam sollicitudin venenatis ipsum ac feugiat. </p>
                        </div>
                    </section>
                    <div class="slidePanel-comment">
                        <textarea class="maxlength-textarea form-control mb-sm mb-20" rows="4"></textarea>
                        <button class="btn btn-primary" data-dismiss="modal" type="button">Reply</button>
                    </div>
                </div></div></div>
        <div class="scrollable-bar scrollable-bar-vertical scrollable-bar-hide" draggable="false"><div class="scrollable-bar-handle" style="height: 32.8222px; transform: translate3d(0px, 0px, 0px);"></div></div></div>
    <div class="slidePanel-handler"></div>
    <div class="slidePanel-loading">
        <div class="loader loader-default"></div>
    </div>
</div>
</body>
</html>
