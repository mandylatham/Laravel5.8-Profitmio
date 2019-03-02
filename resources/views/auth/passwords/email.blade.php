<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profit Miner</title>
    <link href="{{ asset('css/forget-password.css') }}" rel="stylesheet">
    <script>
        window.resetPasswordUrl = "{{ route('password.email') }}";
        window.loginUrl = "{{ route('login') }}";
    </script>
</head>
<body style="background-image: url('/img/background-{{ rand(1,6)  }}.jpg')">
<div class="card" id="forget-password" v-cloak>
    <div class="card-body">
        <div class="logo">
            <img class="brand-img" src="/img/logo-large.png" alt="...">
        </div>
        <p class="text-primary">Reset Password</p>
        <p class="text-danger" v-for="error in errors">@{{ error }}</p>
        <p class="text-danger" v-if="errorMessage">@{{ errorMessage }}</p>
        <form method="post" @submit.prevent="reset()">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control empty" name="email" autocomplete="off"
                       :class="{'is-invalid': userForm.errors.has('email')}" v-model="userForm.email"
                       @change="clearError('email')">
                <div class="invalid-feedback" v-if="userForm.errors.has('email')">
                    <div v-for="msg in userForm.errors.get('email')">@{{ msg }}</div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block waves-effect btn-submit" :disabled="loading">
                <span v-if="!loading">Send Password Reset Link</span>
                <spinner-icon v-if="loading"></spinner-icon>
            </button>
            <a href="{{ route('login') }}" class="go-back">Go Back</a>
        </form>
    </div>
</div>
<script src="{{ asset('js/forget-password.js') }}"></script>
</body>
</html>

{{--@extends('layouts.app')--}}

{{--@section('content')--}}
{{--<div class="container">--}}
    {{--<div class="row">--}}
        {{--<div class="col-md-8 col-md-offset-2">--}}
            {{--<div class="panel panel-default">--}}
                {{--<div class="panel-heading">Reset Password</div>--}}
                {{--<div class="panel-body">--}}
                    {{--@if (session('status'))--}}
                        {{--<div class="alert alert-success">--}}
                            {{--{{ session('status') }}--}}
                        {{--</div>--}}
                    {{--@endif--}}

                    {{--<form class="form-horizontal" role="form" method="POST" action="{{ route('password.email') }}">--}}
                        {{--{{ csrf_field() }}--}}

                        {{--<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">--}}
                            {{--<label for="email" class="col-md-4 control-label">E-Mail Address</label>--}}

                            {{--<div class="col-md-6">--}}
                                {{--<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>--}}

                                {{--@if ($errors->has('email'))--}}
                                    {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('email') }}</strong>--}}
                                    {{--</span>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-6 col-md-offset-4">--}}
                                {{--<button type="submit" class="btn btn-primary">--}}
                                    {{--Send Password Reset Link--}}
                                {{--</button>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
{{--@endsection--}}
