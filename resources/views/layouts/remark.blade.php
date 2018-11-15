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
    <link rel="stylesheet" href="{{ secure_asset('css/v1.css') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="{{ secure_url('/fonts/material-design/material-design.min.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/fonts/brand-icons/brand-icons.min.css') }}">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/weather-icons/weather-icons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
    <!--[if lt IE 9]>
    <script src="{{ secure_url('/vendor/html5shiv/html5shiv.min.js') }}"></script>
    <![endif]-->
    <!--[if lt IE 10]>
    <script src="{{ secure_url('/vendor/media-match/media.match.min.js') }}"></script>
    <script src="{{ secure_url('/vendor/respond/respond.min.js') }}"></script>
    <![endif]-->
    <!-- Scripts -->
    <script type="text/javascript" src="{{ secure_url('vendor/breakpoints/breakpoints.js') }}"></script>
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
        }
        body.animsition,
        body.site-navbar-small
        {
            padding-top: 4.3rem;
        }
        @media(min-width: 767px) {
            .site-navbar-small .site-navbar .navbar-brand {
                min-width: 180px;
            }
        }

        .site-navbar {
            border-bottom: 1px solid #878787;
        }
        .nav-link > i {
            padding-right: 5px;
        }
        .site-footer {
            background: #083756;
            border-top: 1px solid #cdcdcd;
        }
        .site-footer a {
            color: rgb(123, 163, 193);
        }
        @media(min-width: 767px) {
            @if (\Auth::user()->access != 'Client')
            .page {
                padding-top: 50px;
            }
            @endif
            .site-navbar {
                border-bottom: 1px solid #969696;
            }
        }

		a#drops-nav-link {
			padding-top: 19px;
			padding-bottom: 19px;
		}
		@media (max-width: 768px) {
			a#drops-nav-link {
				padding-top: 6px;
				padding-bottom: 10px;
			}
		}

        .page {
            /* Background pattern from Subtle Patterns
                (https://www.toptal.com/designers/subtlepatterns/) */
            background: #333 url('/images/triangular.png') center center repeat;
        }
        .page-title {
            font-family: "Archivo Narrow", "Open Sans", sans-serif;
            color: #fff;
            text-shadow: 2px 2px 8px #202020;
        }
        .page-header {
            padding: 15px 30px;
        }
        .site-menubar {
            background: #083756;
            border-bottom: 1px solid #e5e5e5;
        }
        .panel {
            box-shadow: 2px 2px 10px #555;
        }
        .site-menu-item > .dropdown-menu {
            border: 0.5px solid #ddd;
            border-top: 0px;
            border-radius: 0 0 4px 4px;
            background-color: #083756;
            color: #efefef;
            margin-top: -1px;
            box-shadow: 1px 1px 3px #333;
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
        .dropdown-menu {
            z-index: 9000;
        }
        @yield('manualStyle')
    </style>
</head>
<body class="animsition site-navbar-small site-menubar-hide" style="animation-duration: 800ms; opacity: 1;">
<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
                data-toggle="collapse">
            <i class="icon md-more" aria-hidden="true"></i>
        </button>
        <a class="navbar-brand navbar-brand-center" href="{{ secure_url('/dashboard') }}">
            <img class="navbar-brand-logo navbar-brand-logo-normal" src="{{ secure_asset('images/favicon.png') }}"
                 title="Profit Miner">
            <img class="navbar-brand-logo navbar-brand-logo-special" src="{{ secure_asset('images/favicon.png') }}"
                 title="Profit Miner">
            <span class="navbar-brand-text hidden-xs-down"> Profit Miner</span>
        </a>
    </div>
    <div class="navbar-container container-fluid">
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
                        <span style="margin-left: 8px;">{{ \auth()->user()->name }}</span>
                    </a>
                    <div class="dropdown-menu" role="menu">
                        <a class="dropdown-item" href="{{ secure_url('/logout') }}" role="menuitem"><i class="icon md-power" aria-hidden="true"></i> Logout</a>
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

@if (auth()->user()->isAdmin())
<div class="site-menubar">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu">
                    <li class="site-menu-category">General</li>
                    <li class="site-menu-item">
                        <a href="{{ secure_url('/dashboard') }}" class=" waves-effect waves-classic">
                            <i class="site-menu-icon icon oi-dashboard" aria-hidden="true"></i>
                            <span class="site-menu-title">Dashboard</span>
                        </a>
                    </li>
                    @if (\Gate::allows('view-campaigns'))
                    <li class="site-menu-item">
                        <a href="{{ secure_url('/campaigns') }}" class=" waves-effect waves-classic">
                            <i class="site-menu-icon icon oi-megaphone" aria-hidden="true"></i>
                            <span class="site-menu-title">Campaigns</span>
                        </a>
                    </li>
                    @endif
                    @if (\Gate::allows('view-templates'))
                    <li class="site-menu-item">
                        <a href="{{ secure_url('/templates') }}" class=" waves-effect waves-classic">
                            <i class="site-menu-icon icon fa-file-text-o" aria-hidden="true"></i>
                            <span class="site-menu-title">Templates</span>
                        </a>
                    </li>
                    @endif
                    @if (\Gate::allows('view-users'))
                    <li class="site-menu-item">
                        <a href="{{ secure_url('/users') }}" class=" waves-effect waves-classic">
                            <i class="site-menu-icon icon fa-users" aria-hidden="true"></i>
                            <span class="site-menu-title">Users</span>
                        </a>
                    </li>
                    @endif
                    @if (\Gate::allows('admin-only'))
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
                                                <a href="{{ secure_url('/system/drops') }}" class=" waves-effect waves-classic">
                                                    <i class="site-menu-icon icon fa-paper-plane" aria-hidden="true"></i>
                                                    <span class="site-menu-title">Drop Management</span>
                                                </a>
                                            </li>
                                            <li class="site-menu-item">
                                                <a href="{{ secure_url('/system/reports') }}" class=" waves-effect waves-classic">
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
                </ul>
            </div>
        </div>
    </div>
</div>
@endif

@yield('content')


<footer class="site-footer">
    <div class="site-footer-legal">© 2017 Profit Miner</div>
    <div class="site-footer-right">
        <a href="{{ secure_url('/terms') }}" class="">Terms of Service</a>
    </div>
</footer>

<!-- Core  -->
<script src="{{ secure_url('/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
<script src="{{ secure_url('/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ secure_url('/vendor/tether/tether.min.js') }}"></script>
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
<script src="{{ secure_url('/vendor/chartist/chartist.min.js') }}"></script>
<script src="{{ secure_url('/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.min.js') }}"></script>
<script src="{{ secure_url('/vendor/jvectormap/jquery-jvectormap.min.js') }}"></script>
<script src="{{ secure_url('/vendor/jvectormap/maps/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ secure_url('/vendor/matchheight/jquery.matchHeight-min.js') }}"></script>
<script src="{{ secure_url('/vendor/peity/jquery.peity.min.js') }}"></script>
<!-- Scripts -->
<script src="{{ secure_url('/js/State.js') }}"></script>
<script src="{{ secure_url('/js/Component.js') }}"></script>
<script src="{{ secure_url('/js/Plugin.js') }}"></script>
<script src="{{ secure_url('/js/Base.js') }}"></script>
<script src="{{ secure_url('/js/Config.js') }}"></script>
<script src="{{ secure_asset('js/Section/Menubar.js') }}"></script>
<script src="{{ secure_asset('js/Section/Sidebar.js') }}"></script>
<script src="{{ secure_asset('js/Section/PageAside.js') }}"></script>
<script src="{{ secure_asset('js/Plugin/menu.js') }}"></script>
<!-- Config -->
<script src="{{ secure_url('/js/config/colors.js') }}"></script>
<script src="{{ secure_asset('js/config/tour.js') }}"></script>
<script>
    Config.set('assets', '/');
</script>
<!-- Page -->
<script src="{{ secure_asset('js/Site.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/asscrollable.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/slidepanel.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/switchery.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/matchheight.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/jvectormap.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/peity.js') }}"></script>
<script src="{{ secure_url('/js/v1.js') }}"></script>


@yield('scriptTags')

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("button.button-link").click(function(ev) {
        ev.preventDefault();

        if ($(this).data('url')) {
            window.location.href = $(this).data('url');
        }
    });

    $(".campaign-edit-button").click(function() {
        var url = $(this).data("url");
        window.location.href = url;
    });
@yield('scripts')
</script>
</body>
</html>
