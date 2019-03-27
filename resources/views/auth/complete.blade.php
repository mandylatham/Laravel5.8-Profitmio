<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profit Miner</title>
    <link href="{{ asset('css/registration.css') }}" rel="stylesheet">
    <script>
        window.user = @json($user);
        window.userIsAdmin = @json($user->isAdmin());
        window.signupUrl = '{!! $completeRegistrationSignedUrl !!}';
    </script>
</head>
<body style="background-image: url('/img/background-{{ rand(1,6)  }}.jpg')">
<div class="card" id="registration" v-cloak>
    <div class="card-body">
        <div class="logo">
            <img class="brand-img" src="/img/logo-large.png" alt="...">
        </div>
        <p class="text-danger" v-if="errorMessage">@{{ errorMessage }}</p>
        <p class="text-danger" v-for="error in errors">@{{ error }}</p>
        <form-wizard :title="''" :subtitle="''" :step-size="'sm'" :color="'#572E8D'" @on-complete="signup">
            <tab-content title="Basics" :before-change="validateUserDetails">
                <h4>User Details</h4>
                <div class="form-group">
                    <label for="order">First Name</label>
                    <input type="text" class="form-control empty" name="first_name" autocomplete="off" required
                           :class="{'is-invalid': userForm.errors.has('first_name')}" v-model="userForm.first_name"
                           @change="clearError('first_name')">
                    <div class="invalid-feedback" v-if="userForm.errors.has('first_name')">
                        <div v-for="msg in userForm.errors.get('first_name')">@{{ msg }}</div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="order">Last Name</label>
                    <input type="text" class="form-control empty" name="last_name" autocomplete="off" required
                           :class="{'is-invalid': userForm.errors.has('last_name')}" v-model="userForm.last_name"
                           @change="clearError('last_name')">
                    <div class="invalid-feedback" v-if="userForm.errors.has('last_name')">
                        <div v-for="msg in userForm.errors.get('last_name')">@{{ msg }}</div>
                    </div>
                </div>
            </tab-content>
            <tab-content title="Contact" :before-change="validateContactTab">
                <h4>Contact</h4>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" disabled class="form-control" name="email" value="{{ $user->email }}">
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" class="form-control" name="phone_number"
                           v-model="userForm.phone_number">
                </div>
                <div class="form-group">
                    <label for="timezone">Timezone</label>
                    <select name="timezone" class="form-control" v-model="userForm.timezone" :class="{'is-invalid': userForm.errors.has('timezone')}" @change="clearError('timezone')">
                        <option disabled>Choose Timezone...</option>
                        @foreach (App\Models\User::getPossibleTimezonesForUser() as $timezone)
                            <option value="{{ $timezone }}">{{ $timezone }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" v-if="userForm.errors.has('timezone')">
                        <div v-for="msg in userForm.errors.get('timezone')">@{{ msg }}</div>
                    </div>
                </div>
            </tab-content>
            <tab-content title="Auth" :before-change="validateAuthTab">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input :class="{'is-invalid': userForm.errors.has('password')}" type="password" name="password" class="form-control" required v-model="userForm.password" @change="clearError('password')">
                    <div class="invalid-feedback" v-if="userForm.errors.has('password')">
                        <div v-for="msg in userForm.errors.get('password')">@{{ msg }}</div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Verify Password</label>
                    <input :class="{'is-invalid': userForm.errors.has('password_confirmation')}" type="password" name="password_confirmation" class="form-control" value="" required
                           v-model="userForm.password_confirmation" @change="clearError('password_confirmation')">
                    <div class="invalid-feedback" v-if="userForm.errors.has('password_confirmation')">
                        <div v-for="msg in userForm.errors.get('password_confirmation')">@{{ msg }}</div>
                    </div>
                </div>
            </tab-content>
            <template slot="finish">
                <button type="button" class="wizard-btn" :disabled="loading">
                    <span v-if="!loading">Finish</span>
                    <spinner-icon v-if="loading"></spinner-icon>
                </button>
            </template>
        </form-wizard>
    </div>
</div>
<script src="{{ asset('js/registration.js') }}"></script>
</body>
</html>
