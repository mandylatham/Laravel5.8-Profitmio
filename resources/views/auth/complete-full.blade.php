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
<body style="background-image: url('/img/background-{{ rand(1,6)  }}.jpg')">
<div class="card" id="registration" v-cloak>
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
                <select :class="{'is-invalid': userForm.errors.has('timezone')}" @change="clearError('timezone')" name="timezone" class="form-control" v-model="userForm.timezone">
                    <option disabled>Choose
                        Timezone...
                    </option>
                    @foreach (App\Models\User::getPossibleTimezonesForUser() as $timezone)
                        <option value="{{ $timezone }}">{{ $timezone }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" v-if="userForm.errors.has('timezone')">
                    <div v-for="msg in userForm.errors.get('timezone')">@{{ msg }}</div>
                </div>
            </div>
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
