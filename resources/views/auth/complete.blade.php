<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="apple-touch-icon" href="{{ secure_asset('images/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ secure_asset('images/favicon.ico') }}">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ secure_url('/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/site.css') }}">
    <!-- Plugins -->
    <link rel="stylesheet" href="{{ secure_url('/vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/vendor/asscrollable/asScrollable.css') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="{{ secure_url('/fonts/material-design/material-design.min.css') }}">
    <link rel="stylesheet" href="{{ secure_url('/fonts/brand-icons/brand-icons.min.css') }}">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
    <!--[if lt IE 9]>
    <script src="{{ secure_url('/vendor/html5shiv/html5shiv.min.js') }}"></script>
    <![endif]-->
    <!--[if lt IE 10]>
    <script src="{{ secure_url('/vendor/media-match/media.match.min.js') }}"></script>
    <script src="{{ secure_url('/vendor/respond/respond.min.js') }}"></script>
    <![endif]-->
    <!-- Scripts -->
    <script src="{{ secure_url('/vendor/breakpoints/breakpoints.js') }}"></script>

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
        Breakpoints();
    </script>
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/jquery-wizard/jquery-wizard.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/icheck/icheck.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/typeahead-js/typeahead.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-tokenfield/bootstrap-tokenfield.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/formvalidation/formValidation.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('css/sweetalert.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <style type="text/css">
        #campaigns td {
            cursor: pointer;
        }
        #campaigns > tbody > tr > td > h5 {
            margin-bottom: -20px;
        }
        .round-button {
            border-radius: 40px;
        }
        .btn-circle {
            width: 30px;
            height: 30px;
            text-align: center;
            padding: 6px 0;
            font-size: 12px;
            line-height: 1.428571429;
            border-radius: 15px;
        }
    </style>
    <style>
        html, body {
            height: 100%;
        }

        .page {
            background: url('/images/background-{{ rand(1,6)  }}.jpg') no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        .login-panel {
            min-width: 300px;
            border-radius: 8px;
            border: 10px solid rgba(200, 200, 200, 0.2);
        }

        .page-copyright-inverse .social .icon {
            color: #941c1c;
        }
    </style>
</head>
<body class="animsition page-login layout-full page-dark site-menubar-hide"
      style="animation-duration: 800ms; opacity: 1;">
<div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">
    <div class="page-content vertical-align-middle">
        <div class="panel login-panel">
            <div class="panel-body text-primary">
                <div class="">
                    <img class="brand-img" src="{{ secure_url('images/favicon.png') }}" alt="...">
                    <h2 class="">PROFITMINER</h2>
                </div>
                <p class="text-primary">Register</p>
                @if ($errors->count() > 0)
                    @foreach ($errors->all() as $message)
                        <p class="alert text-danger">{{ $message }}</p>
                    @endforeach
                @endif
                <form id="user-form" method="post" action="{{ $completeRegistrationSignedUrl }}">
                    {{ csrf_field() }}
                    @if (!$user->isAdmin())
                    <input type="hidden" name="company" value="{{ $company->id }}">
                    @endif
                    <div class="pearls row">
                        <div class="pearl col-4">
                            <div class="pearl-icon"><i class="icon md-format-list-bulleted" aria-hidden="true"></i>
                            </div>
                            <span class="pearl-title">Basics</span>
                        </div>
                        <div class="pearl col-4">
                            <div class="pearl-icon"><i class="icon md-phone" aria-hidden="true"></i></div>
                            <span class="pearl-title">Contact</span>
                        </div>
                        <div class="pearl col-4">
                            <div class="pearl-icon"><i class="icon md-key" aria-hidden="true"></i></div>
                            <span class="pearl-title">Auth</span>
                        </div>
                    </div>
                    <div class="wizard-content">
                        <div id="campaign-details" class="wizard-pane" role="tabpanel" aria-expanded="false">
                            <h4>User Details</h4>
                            <div class="form-group">
                                <label for="order" class="floating-label">First Name</label>
                                <input type="text" class="form-control empty" name="first_name" autocomplete="off"
                                       data-fv-field="first_name" value="{{ old('first_name') ?? $user->first_name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="order" class="floating-label">Last Name</label>
                                <input type="text" class="form-control empty" name="last_name" autocomplete="off"
                                       data-fv-field="last_name" value="{{ old('last_name') ?? $user->last_name }}" required>
                            </div>
                        </div>
                        <div id="user-contacts" class="wizard-pane" role="tabpanel" aria-expanded="false">
                            <h4>Contact</h4>
                            <div class="form-group">
                                <input type="email"
                                       disabled
                                       class="form-control"
                                       name="email"
                                       autocomplete="off"
                                       data-fv-field="email"
                                       value="{{ $user->email }}"
                                       required>
                                <label for="email" class="floating-label">Email Address</label>
                            </div>
                            <div class="form-group">
                                <input type="text"
                                       class="form-control empty"
                                       name="phone_number"
                                       autocomplete="off"
                                       value="{{ old('phone_number') ?? $user->phone_number }}"
                                       data-plugin="formatter"
                                       data-pattern="([[999]]) [[999]]-[[9999]]"
                                       required>
                                <label for="phone" class="floating-label">Phone Number</label>
                            </div>
                            @if(!$user->isAdmin())
                            <div class="form-group">
                                <select name="timezone" class="form-control" data-plugin="select2">
                                    <option disabled {{ (old('timezone') ?? $user->timezone) == '' ? 'selected' : '' }}>Choose Timezone...
                                    </option>
                                    @foreach (App\Models\User::getPossibleTimezonesForUser() as $timezone)
                                        <option {{ (old('timezone') ?? $user->timezone) == $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                        <div id="user-auth" class="wizard-pane" role="tabpanel" aria-expanded="true">
                            <h4>Authentication</h4>
                            <div class="form-group">
                                <label for="username" class="form-control-label">Username</label>
                                <input type="text" name="username" class="form-control" value="{{ old('username') ?? $user->username }}"
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="password" class="form-control-label">Password</label>
                                <input type="password" name="password" class="form-control" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation" class="form-control-label">Verify Password</label>
                                <input type="password" name="password_confirmation" class="form-control" value="" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Core  -->
<script src="{{ secure_url('/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
<script src="{{ secure_url('/vendor/jquery/jquery.js') }}"></script>
<script src="{{ secure_url('/vendor/tether/tether.js') }}"></script>
<script src="{{ secure_url('/vendor/bootstrap/bootstrap.js') }}"></script>
<script src="{{ secure_url('/vendor/animsition/animsition.js') }}"></script>
<script src="{{ secure_url('/vendor/mousewheel/jquery.mousewheel.js') }}"></script>
<script src="{{ secure_url('/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
<script src="{{ secure_url('/vendor/asscrollable/jquery-asScrollable.js') }}"></script>

<!-- Scripts -->
<script src="{{ secure_url('/js/State.js') }}"></script>
<script src="{{ secure_url('/js/Component.js') }}"></script>
<script src="{{ secure_url('/js/Plugin.js') }}"></script>
<script src="{{ secure_url('/js/Plugin/jquery-wizard.js') }}"></script>
<script src="{{ secure_url('/vendor/jquery-wizard/jquery-wizard.js') }}"></script>
<script src="{{ secure_url('/vendor/formvalidation/formValidation.js') }}"></script>
<script src="{{ secure_url('/vendor/formvalidation/framework/bootstrap.js') }}"></script>
<script src="{{ secure_url('js/Plugin/formatter.js') }}"></script>
<script src="{{ secure_url('/vendor/formatter/jquery.formatter.js') }}"></script>
<script type="text/javascript">
    $("input[type=text], input[type=password]").change(function () {
        if ($(this).val().length > 0) {
            $(this).removeClass('empty');
        } else {
            $(this).addClass('empty');
        }
    });
</script>
<script type="text/javascript">
    var errorFields = [];
    $(document).ready(function() {
        var defaults = Plugin.getDefaults("wizard");
        console.log('defaults', defaults);
        var options = $.extend(true, {}, defaults, {
            onInit: function () {
                $("#campaign-details").formValidation({
                    framework: 'bootstrap',
                    icon: {
                        valid: 'icon md-check',
                        invalid: 'icon md-alert-circle',
                        validating: 'icon md-refresh'
                    },
                    // This option will not ignore invisible fields which belong to inactive panels
                    excluded: ':disabled',
                    fields: {
                        organization: {
                            validators: {
                                notEmpty: {
                                    message: 'The user\'s organization name is required'
                                }
                            }
                        },
                        first_name: {
                            validators: {
                                notEmpty: {
                                    message: 'The user\'s first name is required'
                                }
                            }
                        },
                        last_name: {
                            validators: {
                                notEmpty: {
                                    message: 'The user\'s last name is required'
                                }
                            }
                        }
                    },
                    row: {
                        invalid: 'has-danger'
                    }
                })
                    .on('err.field.fv', function(e, data) {
                        if ( errorFields[data.field] !== undefined) {
                            if (errorFields[data.field]) {
                                errorFields[data.field] = false;
                            }
                        }
                        $("#nextButton").addClass("disabled");
                    })
                    .on('success.field.fv', function(e, data) {
                        if ( errorFields[data.field] !== undefined) {
                            if (! errorFields[data.field]) {
                                errorFields[data.field] = true;
                            }
                        }
                        if (errorFields.length == 0) {
                            $("#nextButton").removeClass("disabled");
                        }
                    });

                $("#user-contacts").formValidation({
                    framework: "bootstrap",
                    icon: {
                        valid: 'icon md-check',
                        invalid: 'icon md-alert-circle',
                        validating: 'icon md-refresh'
                    },
                    // This option will not ignore invisible fields which belong to inactive panels
                    excluded: ':disabled',
                    button: {
                        selector: '#nextButton',
                        disabled: 'disabled'
                    },
                    fields: {
                        email: {
                            validators: {
                                notEmpty: {
                                    message: 'The email is required'
                                },
                                emailAddress: {
                                    message: 'The input is not a valid email address'
                                },
                                stringLength: {
                                    max: 512,
                                    message: 'Cannot exceed 512 characters'
                                },

                            }
                        },
                        phone_number: {
                            validators: {
                                digits: {
                                    message: 'Phone numbers may only contain numbers'
                                }
                            }
                        }
                    },
                    row: {
                        invalid: 'has-danger'
                    }
                })
                    .on('err.field.fv', function(e, data) {
                        if ( errorFields[data.field] !== undefined) {
                            if (errorFields[data.field]) {
                                errorFields[data.field] = false;
                            }
                        }
                        $("#nextButton").addClass("disabled");
                    })
                    .on('success.field.fv', function(e, data) {
                        if ( errorFields[data.field] !== undefined) {
                            if (! errorFields[data.field]) {
                                errorFields[data.field] = true;
                            }
                        }
                        if (errorFields.length == 0) {
                            $("#nextButton").removeClass("disabled");
                        }
                    });

                $("#user-auth").formValidation({
                    framework: "bootstrap",
                    icon: {
                        valid: 'icon md-check',
                        invalid: 'icon md-alert-circle',
                        validating: 'icon md-refresh'
                    },
                    // This option will not ignore invisible fields which belong to inactive panels
                    excluded: ':disabled',
                    button: {
                        selector: '#nextButton',
                        disabled: 'disabled'
                    },
                    fields: {
                        password: {
                            validators: {
                                notEmpty: {
                                    message: 'The email is required'
                                },
                                stringLength: {
                                    max: 512,
                                    message: 'Cannot exceed 512 characters'
                                }
                            }
                        },
                        verify_password: {
                            validators: {
                                notEmpty: {
                                    message: 'The email is required'
                                },
                                stringLength: {
                                    max: 512,
                                    message: 'Cannot exceed 512 characters'
                                },
                                identical: {
                                    field: 'password',
                                    message: "The passwords do not match"
                                }
                            }
                        },
                    },
                    row: {
                        invalid: 'has-danger'
                    }
                })
                    .on('err.field.fv', function(e, data) {
                        if ( errorFields[data.field] !== undefined) {
                            if (errorFields[data.field]) {
                                errorFields[data.field] = false;
                            }
                        }
                        $("#nextButton").addClass("disabled");
                        $("#finishButton").addClass("disabled");
                    })
                    .on('success.field.fv', function(e, data) {
                        if ( errorFields[data.field] !== undefined) {
                            if (! errorFields[data.field]) {
                                errorFields[data.field] = true;
                            }
                        }
                        if (errorFields.length == 0) {
                            $("#nextButton").removeClass("disabled");
                            $("#finishButton").removeClass("disabled");
                        }
                    });
            },
            validator: function() {
                return true;
            },
            templates: {
                buttons: function() {
                    var options = this.options;
                    var html = '<div class="pt-20 d-flex justify-content-between">' +
                        '<a href="#' + this.id + '" class="btn btn-default" id="backButton" data-wizard="back" role="button">' + options.buttonLabels.back + '</a>' +
                        '<a href="#' + this.id + '" class="btn btn-success btn-outline float-right" id="finishButton" data-wizard="finish" role="button">' + options.buttonLabels.finish + '</a>' +
                        '<a href="#' + this.id + '" class="btn btn-default btn-outline float-right" id="nextButton" data-wizard="next" role="button">' + options.buttonLabels.next + '</a>' +
                        '</div>';
                    return html;
                }
            },
            row: {
                valid: 'has-success',
                invalid: 'has-danger'
            },
            onFinish: function() {
                $("#finishButton").attr('disabled', 'disabled').addClass('disabled');
                $('#user-form').submit();
            },
            buttonsAppendTo: 'this'
        });

        $('#user-form').wizard(options).data("wizard");

        $("input[type=text], input[type=email]").change(function() {
            if ( $(this).val().length > 0) {
                $(this).removeClass('empty');
            } else {
                $(this).removeClass('empty');
                $(this).addClass('empty');
            }
        });
    });
</script>
</body>
</html>


{{--@extends('layouts.app')--}}

{{--@section('content')--}}
{{--<div class="container">--}}
{{--<div class="row justify-content-center">--}}
{{--<div class="col-md-8">--}}
{{--<div class="card">--}}
{{--<div class="card-header">{{ __('Register') }}</div>--}}

{{--<div class="card-body">--}}
{{--<form method="POST" >--}}
{{--@csrf--}}

{{--<div class="form-group row">--}}
{{--<label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>--}}

{{--<div class="col-md-6">--}}
{{--<input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name', $user->name) }}" required autofocus>--}}

{{--@if ($errors->has('name'))--}}
{{--<span class="invalid-feedback" role="alert">--}}
{{--<strong>{{ $errors->first('name') }}</strong>--}}
{{--</span>--}}
{{--@endif--}}
{{--</div>--}}
{{--</div>--}}

{{--<div class="form-group row">--}}
{{--<label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>--}}

{{--<div class="col-md-6">--}}
{{--<input type="email" class="form-control" name="email" disabled value="{{$user->email}}">--}}
{{--</div>--}}
{{--</div>--}}

{{--<div class="form-group row">--}}
{{--<label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>--}}

{{--<div class="col-md-6">--}}
{{--<input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>--}}

{{--@if ($errors->has('password'))--}}
{{--<span class="invalid-feedback" role="alert">--}}
{{--<strong>{{ $errors->first('password') }}</strong>--}}
{{--</span>--}}
{{--@endif--}}
{{--</div>--}}
{{--</div>--}}

{{--<div class="form-group row">--}}
{{--<label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>--}}

{{--<div class="col-md-6">--}}
{{--<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>--}}
{{--</div>--}}
{{--</div>--}}

{{--@if(!empty($user->companies))--}}
{{--@foreach($user->companies as $company)--}}
{{--<div class="form-group row">--}}
{{--<label for="name" class="col-md-4 col-form-label text-md-right">Timezone for {{$company->name}}</label>--}}

{{--<div class="col-md-6">--}}
{{--<select name="config[timezone][{{$company->id}}]">--}}
{{--@foreach($user->getPossibleTimezones() as $timezone)--}}
{{--<option value="{{$timezone}}">{{$timezone}}</option>--}}
{{--@endforeach--}}
{{--</select>--}}
{{--</div>--}}
{{--</div>--}}
{{--@endforeach--}}
{{--@endif--}}

{{--<div class="form-group row mb-0">--}}
{{--<div class="col-md-6 offset-md-4">--}}
{{--<button type="submit" class="btn btn-primary">--}}
{{--{{ __('Complete registration') }}--}}
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
