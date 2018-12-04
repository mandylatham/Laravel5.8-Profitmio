<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="author" content="">
    <title>Mailbox | Remark Admin Template</title>
    <link rel="apple-touch-icon" href="{{ secure_url('images/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ secure_url('images/favicon.ico') }}">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ secure_url('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ secure_url('css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ secure_url('css/site.css') }}">
    <!-- Plugins -->
    <link rel="stylesheet" href="{{ secure_url('vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/asscrollable/asScrollable.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/switchery/switchery.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/intro-js/introjs.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/slidepanel/slidePanel.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/flag-icon-css/flag-icon.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/waves/waves.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/bootstrap-markdown/bootstrap-markdown.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ secure_url('css/mailbox.css') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ secure_url('fonts/material-design/material-design.min.css') }}">
    <link rel="stylesheet" href="{{ secure_url('fonts/brand-icons/brand-icons.min.css') }}">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
    <!--[if lt IE 9]>
    <script src="{{ secure_url('vendor/html5shiv/html5shiv.min.js') }}"></script>
    <![endif]-->
    <!--[if lt IE 10]>
    <script src="{{ secure_url('vendor/media-match/media.match.min.js') }}"></script>
    <script src="{{ secure_url('vendor/respond/respond.min.js') }}"></script>
    <![endif]-->
    <!-- Scripts -->
    <script src="{{ secure_url('vendor/breakpoints/breakpoints.js') }}"></script>
    <script>
        Breakpoints();
    </script>
</head>
<body class="animsition site-navbar-small app-mailbox page-aside-left">
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided"
                data-toggle="menubar">
            <span class="sr-only">Toggle navigation</span>
            <span class="hamburger-bar"></span>
        </button>
        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
                data-toggle="collapse">
            <i class="icon md-more" aria-hidden="true"></i>
        </button>
        <a class="navbar-brand navbar-brand-center" href="{{ secure_url('/') }}">
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
                <li class="nav-item">
                    <a class="nav-link icon fa-paper-plane waves-effect waves-light waves-round"
                       href="{{ secure_url('campaign/' . $campaign->id) }}"
                       alt="Back to Campaign"
                       role="button">
                        <span class="sr-only">Back to Campaign</span>
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
                        <span style="margin-left: 8px;">{{ \Auth::user()->name }}</span>
                    </a>
                    <div class="dropdown-menu" role="menu">
                        <a class="dropdown-item" href="javascript:void(0)" role="menuitem"><i class="icon md-account" aria-hidden="true"></i> Profile</a>
                        <a class="dropdown-item disabled" href="javascript:void(0)" role="menuitem"><i class="icon md-card" aria-hidden="true"></i> Billing</a>
                        <a class="dropdown-item disabled" href="javascript:void(0)" role="menuitem"><i class="icon md-settings" aria-hidden="true"></i> Settings</a>
                        <div class="dropdown-divider"></div>
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

<h1>Done</h1>

<!-- End Add Label Form -->
<!-- Footer -->
<script src="{{ secure_url('vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
<script src="{{ secure_url('vendor/jquery/jquery.js') }}"></script>
<script src="{{ secure_url('vendor/tether/tether.js') }}"></script>
<script src="{{ secure_url('vendor/bootstrap/bootstrap.js') }}"></script>
<script src="{{ secure_url('vendor/animsition/animsition.js') }}"></script>
<script src="{{ secure_url('vendor/mousewheel/jquery.mousewheel.js') }}"></script>
<script src="{{ secure_url('vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
<script src="{{ secure_url('vendor/asscrollable/jquery-asScrollable.js') }}"></script>
<script src="{{ secure_url('vendor/waves/waves.js') }}"></script>
<!-- Plugins -->
<script src="{{ secure_url('vendor/switchery/switchery.min.js') }}"></script>
<script src="{{ secure_url('vendor/intro-js/intro.js') }}"></script>
<script src="{{ secure_url('vendor/screenfull/screenfull.js') }}"></script>
<script src="{{ secure_url('vendor/slidepanel/jquery-slidePanel.js') }}"></script>
<script src="{{ secure_url('vendor/select2/select2.min.js') }}"></script>
<script src="{{ secure_url('vendor/slidepanel/jquery-slidePanel.js') }}"></script>
<script src="{{ secure_url('vendor/bootstrap-markdown/bootstrap-markdown.js') }}"></script>
<script src="{{ secure_url('vendor/marked/marked.js') }}"></script>
<script src="{{ secure_url('vendor/to-markdown/to-markdown.js') }}"></script>
<script src="{{ secure_url('vendor/aspaginator/jquery.asPaginator.min.js') }}"></script>
<script src="{{ secure_url('vendor/bootbox/bootbox.js') }}"></script>
<!-- Scripts -->
<script src="{{ secure_url('js/State.js') }}"></script>
<script src="{{ secure_url('js/Component.js') }}"></script>
<script src="{{ secure_url('js/Plugin.js') }}"></script>
<script src="{{ secure_url('js/Base.js') }}"></script>
<script src="{{ secure_url('js/Config.js') }}"></script>
<script src="{{ secure_url('js/Section/Menubar.js') }}"></script>
<script src="{{ secure_url('js/Section/Sidebar.js') }}"></script>
<script src="{{ secure_url('js/Section/PageAside.js') }}"></script>
<script src="{{ secure_url('js/Plugin/menu.js') }}"></script>
<!-- Config -->
<script src="{{ secure_url('js/config/colors.js') }}"></script>
<script src="{{ secure_url('js/config/tour.js') }}"></script>
<script>
    Config.set('assets', '/');
</script>
<!-- Page -->
<script src="{{ secure_url('js/Site.js') }}"></script>
<script src="{{ secure_url('js/Plugin/asscrollable.js') }}"></script>
<script src="{{ secure_url('js/Plugin/slidepanel.js') }}"></script>
<script src="{{ secure_url('js/Plugin/switchery.js') }}"></script>
<script src="{{ secure_url('js/Plugin/action-btn.js') }}"></script>
<script src="{{ secure_url('js/Plugin/asselectable.js') }}"></script>
<script src="{{ secure_url('js/Plugin/editlist.js') }}"></script>
<script src="{{ secure_url('js/Plugin/select2.js') }}"></script>
<script src="{{ secure_url('js/Plugin/aspaginator.js') }}"></script>
<script src="{{ secure_url('js/Plugin/animate-list.js') }}"></script>
<script src="{{ secure_url('js/Plugin/selectable.js') }}"></script>
<script src="{{ secure_url('js/Plugin/material.js') }}"></script>
<script src="{{ secure_url('js/Plugin/bootbox.js') }}"></script>
<script src="{{ secure_url('js/BaseApp.js') }}"></script>
<script src="{{ secure_url('js/App/Mailbox.js') }}"></script>
<script src="{{ secure_url('js/mailbox.js') }}"></script>
</body>
</html>