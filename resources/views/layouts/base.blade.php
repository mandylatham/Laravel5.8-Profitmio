<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profit Miner</title>

    <script src="{{ asset('js/new-app.js') }}" defer></script>
    <link href="{{ asset('css/new-app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <!-- Navigation Bar-->
        <div class="clearfix" id="main-header">
            <a href="index.html" class="logo">
                <img src="/img/logo.png">
            </a>
            <nav class="navbar">
                <a href="javascript:;" class="sidebar-toggle">B</a>
                <div class="navbar-menu-extra">
                    <ul class="nav">
                        <li class="menu-item">
                            <b-dropdown variant="link" no-caret>
                                <template slot="button-content">
                                    <img src="/img/help.png" alt="Help">
                                </template>
                                <b-dropdown-item href="#">Action</b-dropdown-item>
                                <b-dropdown-item href="#">Another action</b-dropdown-item>
                                <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                            </b-dropdown>
                        </li>
                        <li class="menu-item">
                            <b-dropdown variant="link" no-caret>
                                <template slot="button-content">
                                    <img src="/img/notification.png" alt="Notifications">
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
                                    <img src="//lorempixel.com/60/60/" alt="Avatar">
                                </template>
                                <b-dropdown-item href="#">Profile</b-dropdown-item>
                                <b-dropdown-item href="#">Another action</b-dropdown-item>
                                <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                            </b-dropdown>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <!-- End Navigation Bar-->

        @yield('content')
    </div>
</body>
</html>