<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profit Miner</title>
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
    <script>
        window.authUrl = "{{ route('auth.authenticate') }}";
    </script>
</head>
<body style="background-image: url('img/background-{{ rand(1,6)  }}.jpg')">
    <div class="card" id="login" v-cloak>
        <div class="card-body">
            <div class="logo">
                <img class="brand-img" src="/img/logo-large.png" alt="...">
            </div>
            <h1>Test CD</h1>
            <p class="text-primary">Sign into your account</p>
            <p dusk="error-message-1" class="text-danger" v-for="error in errors">@{{ error }}</p>
            <p dusk="error-message-2" class="text-danger" v-if="errorMessage">@{{ errorMessage }}</p>
            <form method="post" @submit.prevent="login()">
                {{ csrf_field() }}
                <div class="form-group">
                    <input type="text" class="form-control" id="email" name="email" placeholder="Email" v-model="userForm.email">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" placeholder="Password" name="password" v-model="userForm.password">
                </div>
                <div class="form-group clearfix">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="checkbox-custom remember-me-fg checkbox-inline checkbox-primary float-left">
                                <input type="checkbox" id="inputCheckbox" name="remember">
                                <label for="inputCheckbox">Remember me</label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group clearfix forget-password-fg">
                                <a href="{{ route('password.request') }}">Forgot password?</a>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" dusk="login-button" class="btn btn-primary btn-block waves-effect" :disabled="loading">
                    <span v-if="!loading">Sign in</span>
                    <spinner-icon v-if="loading"></spinner-icon>
                </button>
            </form>
        </div>
    </div>
<script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
