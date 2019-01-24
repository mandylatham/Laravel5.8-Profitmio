<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profit Miner</title>
    <link href="{{ asset('css/registration-full.css') }}" rel="stylesheet">
    <script>
        window.signupUrl = "{!! $completeRegistrationSignedUrl !!}";
    </script>
</head>
<body style="background-image: url('/images/background-{{ rand(1,6)  }}.jpg')">
<div class="card" id="registration">
    <div class="card-body">
        <div class="logo">
            <img class="brand-img" src="/img/logo-large.png" alt="...">
        </div>
        <p class="text-primary">Join To</p>
        <p class="text-danger" v-for="error in errors">@{{ error }}</p>
        <p class="text-danger" v-if="errorMessage">@{{ errorMessage }}</p>
        <form method="post" @submit.prevent="signup()">
            {{ csrf_field() }}
            <div class="form-group">
                <input class="form-control" disabled value="{{ $company->name }}">
            </div>
            <div class="form-group">
                <label for="timezone">Timezone</label>
                <select name="timezone" class="form-control" v-model="userForm.timezone">
                    <option disabled>Choose
                        Timezone...
                    </option>
                    @foreach (App\Models\User::getPossibleTimezonesForUser() as $timezone)
                        <option value="{{ $timezone }}">{{ $timezone }}</option>
                    @endforeach
                </select></div>
            <button type="submit" class="btn btn-primary btn-block waves-effect" :disabled="loading">
                <span v-if="!loading">Join</span>
                <spinner-icon v-if="loading"></spinner-icon>
            </button>
            <a href="{{ route('login') }}" class="go-back">Already have an account?</a>
        </form>
    </div>
</div>
<script src="{{ asset('js/registration-full.js') }}"></script>
</body>
</html>


{{--<!DOCTYPE html>--}}
{{--<html class="no-js css-menubar" lang="en">--}}
{{--<head>--}}
{{--<meta charset="utf-8">--}}
{{--<meta http-equiv="X-UA-Compatible" content="IE=edge">--}}
{{--<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">--}}
{{--<meta name="description" content="bootstrap admin template">--}}
{{--<meta name="author" content="">--}}
{{--<meta name="csrf-token" content="{{ csrf_token() }}">--}}
{{--<title>{{ config('app.name', 'Laravel') }}</title>--}}

{{--<link rel="apple-touch-icon" href="{{ secure_asset('images/apple-touch-icon.png') }}">--}}
{{--<link rel="shortcut icon" href="{{ secure_asset('images/favicon.ico') }}">--}}

{{--<!-- Stylesheets -->--}}
{{--<link rel="stylesheet" href="{{ secure_url('/css/bootstrap.min.css') }}">--}}
{{--<link rel="stylesheet" href="{{ secure_url('/css/bootstrap-extend.min.css') }}">--}}
{{--<link rel="stylesheet" href="{{ secure_asset('css/site.css') }}">--}}
{{--<!-- Plugins -->--}}
{{--<link rel="stylesheet" href="{{ secure_url('/vendor/animsition/animsition.css') }}">--}}
{{--<link rel="stylesheet" href="{{ secure_url('/vendor/asscrollable/asScrollable.css') }}">--}}
{{--<link rel="stylesheet" href="{{ secure_url('/vendor/switchery/switchery.css') }}">--}}
{{--<link rel="stylesheet" href="{{ secure_url('/vendor/intro-js/introjs.css') }}">--}}
{{--<link rel="stylesheet" href="{{ secure_url('/vendor/slidepanel/slidePanel.css') }}">--}}
{{--<link rel="stylesheet" href="{{ secure_url('/vendor/flag-icon-css/flag-icon.css') }}">--}}
{{--<link rel="stylesheet" href="{{ secure_url('/vendor/waves/waves.css') }}">--}}
{{--<!-- Fonts -->--}}
{{--<link rel="stylesheet" href="{{ secure_url('/fonts/material-design/material-design.min.css') }}">--}}
{{--<link rel="stylesheet" href="{{ secure_url('/fonts/brand-icons/brand-icons.min.css') }}">--}}
{{--<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>--}}
{{--<!--[if lt IE 9]>--}}
{{--<script src="{{ secure_url('/vendor/html5shiv/html5shiv.min.js') }}"></script>--}}
{{--<![endif]-->--}}
{{--<!--[if lt IE 10]>--}}
{{--<script src="{{ secure_url('/vendor/media-match/media.match.min.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/respond/respond.min.js') }}"></script>--}}
{{--<![endif]-->--}}
{{--<!-- Scripts -->--}}
{{--<script src="{{ secure_url('/vendor/breakpoints/breakpoints.js') }}"></script>--}}

{{--<!-- Scripts -->--}}
{{--<script>--}}
{{--window.Laravel = {!! json_encode([--}}
{{--'csrfToken' => csrf_token(),--}}
{{--]) !!};--}}
{{--Breakpoints();--}}
{{--</script>--}}
{{--<style type="text/css">--}}
{{--html,body {--}}
{{--height: 100%;--}}
{{--}--}}
{{--.page {--}}
{{--background: url('/images/background-{{ rand(1,6)  }}.jpg') no-repeat center center fixed;--}}
{{---webkit-background-size: cover;--}}
{{---moz-background-size: cover;--}}
{{---o-background-size: cover;--}}
{{--background-size: cover;--}}
{{--}--}}
{{--.login-panel {--}}
{{--min-width: 300px;--}}
{{--border-radius: 8px;--}}
{{--border: 10px solid rgba(200, 200, 200, 0.2);--}}
{{--}--}}
{{--.page-copyright-inverse .social .icon {--}}
{{--color: #941c1c;--}}
{{--}--}}
{{--</style>--}}
{{--</head>--}}
{{--<body class="animsition page-login layout-full page-dark site-menubar-hide" style="animation-duration: 800ms; opacity: 1;">--}}
{{--<div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">&gt;--}}
{{--<div class="page-content vertical-align-middle">--}}
{{--<div class="panel login-panel">--}}
{{--<div class="panel-body text-primary">--}}
{{--<div class="">--}}
{{--<img class="brand-img" src="{{ secure_url('images/favicon.png') }}" alt="...">--}}
{{--<h2 class="">PROFITMINER</h2>--}}
{{--</div>--}}
{{--<p class="text-primary">Join To</p>--}}
{{--@if ($errors->count() > 0)--}}
{{--@foreach ($errors->all() as $message)--}}
{{--<p class="alert text-danger">{{ $message }}</p>--}}
{{--@endforeach--}}
{{--@endif--}}
{{--<form method="post" action="{{ $completeRegistrationSignedUrl }}">--}}
{{--{{ csrf_field() }}--}}
{{--@if ($user->isAdmin())--}}
{{--<h3>Welcome to Profit Miner!</h3>--}}
{{--@else--}}
{{--<div class="form-group">--}}
{{--<input type="text" class="form-control empty" name="company" disabled value="{{ $company->name }}">--}}
{{--</div>--}}
{{--<div class="form-group">--}}
{{--<select name="timezone" class="form-control" data-plugin="select2">--}}
{{--<option disabled {{ (old('timezone') ?? $company->pivot->timezone) == '' ? 'selected' : '' }}>Choose Timezone...--}}
{{--</option>--}}
{{--@foreach (App\Models\User::getPossibleTimezonesForUser() as $timezone)--}}
{{--<option {{ (old('timezone') ?? $company->pivot->timezone) == $timezone ? 'selected' : '' }}>{{ $timezone }}</option>--}}
{{--@endforeach--}}
{{--</select>--}}
{{--</div>--}}
{{--@endif--}}
{{--<button type="submit" class="btn btn-primary btn-block waves-effect">Join</button>--}}
{{--<div>--}}
{{--<a class="float-right" href="{{ route('login') }}">Already have an account?</a>--}}
{{--</div>--}}
{{--</form>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}

{{--<!-- Core  -->--}}
{{--<script src="{{ secure_url('/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/jquery/jquery.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/tether/tether.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/bootstrap/bootstrap.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/animsition/animsition.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/mousewheel/jquery.mousewheel.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/asscrollable/jquery-asScrollable.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/waves/waves.js') }}"></script>--}}
{{--<!-- Plugins -->--}}
{{--<script src="{{ secure_url('/vendor/switchery/switchery.min.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/intro-js/intro.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/screenfull/screenfull.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/slidepanel/jquery-slidePanel.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/matchheight/jquery.matchHeight-min.js') }}"></script>--}}
{{--<script src="{{ secure_url('/vendor/peity/jquery.peity.min.js') }}"></script>--}}
{{--<!-- Scripts -->--}}
{{--<script src="{{ secure_url('/js/State.js') }}"></script>--}}
{{--<script src="{{ secure_url('/js/Component.js') }}"></script>--}}
{{--<script src="{{ secure_url('/js/Plugin.js') }}"></script>--}}
{{--<script src="{{ secure_url('/js/Base.js') }}"></script>--}}
{{--<script src="{{ secure_url('/js/Config.js') }}"></script>--}}
{{--<script src="{{ secure_asset('js/Section/Menubar.js') }}"></script>--}}
{{--<script src="{{ secure_asset('js/Section/Sidebar.js') }}"></script>--}}
{{--<script src="{{ secure_asset('js/Section/PageAside.js') }}"></script>--}}
{{--<script src="{{ secure_asset('js/Plugin/menu.js') }}"></script>--}}
{{--<!-- Config -->--}}
{{--<script src="{{ secure_url('/js/config/colors.js') }}"></script>--}}
{{--<script>--}}
{{--Config.set('assets', '/');--}}
{{--</script>--}}
{{--<!-- Page -->--}}
{{--<script src="{{ secure_asset('js/Site.js') }}"></script>--}}
{{--<script src="{{ secure_url('/js/Plugin/asscrollable.js') }}"></script>--}}
{{--<script src="{{ secure_url('/js/Plugin/slidepanel.js') }}"></script>--}}
{{--<script src="{{ secure_url('/js/Plugin/switchery.js') }}"></script>--}}
{{--<script src="{{ secure_url('/js/Plugin/matchheight.js') }}"></script>--}}


{{--@yield('scriptTags')--}}

{{--<script type="text/javascript">--}}
{{--$.ajaxSetup({--}}
{{--headers: {--}}
{{--'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
{{--}--}}
{{--});--}}

{{--$(".campaign-edit-button").click(function() {--}}
{{--var url = $(this).data("url");--}}
{{--window.location.href = url;--}}
{{--});--}}

{{--$("input[type=text], input[type=password]").change(function () {--}}
{{--if ($(this).val().length > 0) {--}}
{{--$(this).removeClass('empty');--}}
{{--} else {--}}
{{--$(this).addClass('empty');--}}
{{--}--}}
{{--});--}}
{{--@yield('scripts')--}}
{{--</script>--}}
{{--</body>--}}
{{--</html>--}}
