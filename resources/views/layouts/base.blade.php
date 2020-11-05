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
        window.notifications = @json(getNotifications());
    </script>
    @yield('head-script')
</head>
<body>
    <div id="app" class="clearfix {{ $hasSidebar ? 'has-sidebar' : 'no-sidebar' }}">
        @if (isset($hasSidebar) && $hasSidebar)
            @component('layouts.sidebar')
            @endcomponent
        @endif
        <div class="main-content-container">
            @component('layouts.top-navbar')
            @endcomponent
            <div class="main-content">
                @if (isset($hasSidebar) && $hasSidebar)
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="sidebar-toggler js-toggle-side-menu d-xl-none">
                                @hasSection('sidebar-toggle-content')
                                    @yield('sidebar-toggle-content')
                                @else
                                    <i class="fas fa-bars"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @yield('main-content')
            </div>
        </div>
    </div>
    @yield('body-script')
</body>
</html>
