<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profit Miner</title>
    <link href="{{ asset('css/reset-password.css') }}" rel="stylesheet">
    <script>
        window.updatePasswordUrl = "{{ route('password.restore') }}";
        window.loginUrl = "{{ route('login') }}";
        window.token = @json($token);
    </script>
</head>
<body style="background-image: url('/img/background-{{ rand(1,6)  }}.jpg')">
<div class="card" id="reset-password" v-cloak>
    <div class="card-body">
        <div class="logo">
            <img class="brand-img" src="/img/logo-large.png" alt="...">
        </div>
        <p class="text-primary">Reset Password</p>
        <p class="text-danger" v-for="error in errors">@{{ error }}</p>
        <p class="text-danger" v-if="errorMessage">@{{ errorMessage }}</p>
        <form method="post" @submit.prevent="reset()">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control empty" name="email" autocomplete="off"
                       :class="{'is-invalid': userForm.errors.has('email')}" v-model="userForm.email"
                       @change="clearError('email')">
                <div class="invalid-feedback" v-if="userForm.errors.has('email')">
                    <div v-for="msg in userForm.errors.get('email')">@{{ msg }}</div>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control empty" name="password" autocomplete="off"
                       :class="{'is-invalid': userForm.errors.has('password')}" v-model="userForm.password"
                       @change="clearError('password')">
                <div class="invalid-feedback" v-if="userForm.errors.has('password')">
                    <div v-for="msg in userForm.errors.get('password')">@{{ msg }}</div>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Confirm Password</label>
                <input type="password" class="form-control empty" name="password_confirmation" autocomplete="off"
                       :class="{'is-invalid': userForm.errors.has('password_confirmation')}" v-model="userForm.password_confirmation"
                       @change="clearError('password_confirmation')">
                <div class="invalid-feedback" v-if="userForm.errors.has('password_confirmation')">
                    <div v-for="msg in userForm.errors.get('password_confirmation')">@{{ msg }}</div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block waves-effect btn-submit" :disabled="loading">
                <span v-if="!loading">Reset Password</span>
                <spinner-icon v-if="loading"></spinner-icon>
            </button>
            <a href="{{ route('login') }}" class="go-back">Go Back</a>
        </form>
    </div>
</div>
<script src="{{ asset('js/reset-password.js') }}"></script>
</body>
</html>
