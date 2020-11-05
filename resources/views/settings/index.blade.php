@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/settings.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.saveFacebookAccessTokenUrl = "{{ route('settings.facebook-access-token.store') }}";
    </script>
    <script src="{{ asset('js/settings.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="settings" v-cloak>
        <div class="row">
            <div class="col-md-8 mt-5">
                <b-alert v-for="notification in notifications.settings" show :variant="notification.level">
                    <h4 class="alert-heading">
                        @{{notification.title}}
                    </h4>
                    <p>
                        @{{notification.description}}
                    </p>
                </b-alert>
                <div class="card" style="width: 100%;">
                    <div class="card-body">

                        <h3>Facebook Integration</h3>
                        <span>
                            <strong>App ID: </strong>{{ $settings->facebook->app_id }}
                        </span>

                        <v-facebook-login
                            app-id="{{$settings->facebook->app_id}}"
                            version="{{$settings->facebook->graph_version}}"
                            v-model="model"
                            v-bind:login-options="loginOptions"
                            @login="handleLogin"
                            class="mt-3"
                        ></v-facebook-login>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
