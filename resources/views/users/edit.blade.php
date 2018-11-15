@extends('layouts.users')

@section('header')
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/jquery-wizard/jquery-wizard.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/icheck/icheck.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/typeahead-js/typeahead.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-tokenfield/bootstrap-tokenfield.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/formvalidation/formValidation.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('css/sweetalert.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-select/bootstrap-select.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('css/jsgrid.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('css/jsgrid-theme.css') }}">
    <style type="text/css" media="all">
        form > h4 {
            margin-top: 20px;
        }
        .btn.dropdown-toggle.btn-default,
        select.form-control {
            margin-top: 8px;
        }
        .wizard-buttons {
            padding-top: 50px;
        }
    </style>
@endsection

@section('user_content')
    <div class="container-fluid">
        <form data-fv-live="enabled" id="user-form" class="form form-horizontal" action="{{ secure_url('/user/' . $user->id . '/update') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $user->id }}">
            @if ($errors->count() > 0)
                <div class="row-fluid">
                    <div class="col-md-12">
                        <div class="alert alert-danger">
                            <h3>There were some errors:</h3>
                            <ul>
                                @foreach ($errors->all() as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row-fluid">
                <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2">
                    <h2>Edit User</h2>
                    <div class="nav-tabs-horizontal" data-plugin="tabs">
                        <ul class="nav nav-tabs nav-tabs-reverse" role="tablist">
                            <li class="nav-item" role="presentation" style="display: list-item;">
                                <a class="nav-link active"
                                   data-toggle="tab"
                                   href="#exampleTabsReverseOne"
                                   aria-controls="exampleTabsReverseOne"
                                   role="tab">
                                    Details
                                </a>
                            </li>
                            <li class="nav-item" role="presentation" style="display: list-item;">
                                <a class="nav-link"
                                   data-toggle="tab"
                                   href="#exampleTabsReverseTwo"
                                   aria-controls="exampleTabsReverseTwo"
                                   role="tab">
                                    Contact
                                </a>
                            </li>
                            <li class="nav-item" role="presentation" style="display: list-item;">
                                <a class="nav-link"
                                   data-toggle="tab"
                                   href="#exampleTabsReverseThree"
                                   aria-controls="exampleTabsReverseThree"
                                   role="tab">
                                    Auth
                                </a>
                            </li>
                            <li class="dropdown nav-item" role="presentation" style="display: none;">
                                <a class="dropdown-toggle nav-link" data-toggle="dropdown" href="#" aria-expanded="false">Dropdown </a>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item"
                                       data-toggle="tab"
                                       href="exampleTabsReverseOne"
                                       aria-controls="exampleTabsReverseOne"
                                       role="tab"
                                       style="display: none;">
                                        Details
                                    </a>
                                    <a class="dropdown-item"
                                       data-toggle="tab"
                                       href="#exampleTabsReverseTwo"
                                       aria-controls="exampleTabsReverseTwo"
                                       role="tab"
                                       style="display: none;">
                                        Accounts
                                    </a>
                                    <a class="dropdown-item"
                                       data-toggle="tab"
                                       href="#exampleTabsReverseThree"
                                       aria-controls="exampleTabsReverseThree"
                                       role="tab">
                                        Auth
                                    </a>
                                </div>
                            </li>
                        </ul>
                        <div class="tab-content pt-20">
                            <div class="tab-pane active" id="exampleTabsReverseOne" role="tabpanel">
                                <div class="form-group form-material floating">
                                    <select name="access" class="form-control" required>
                                        <option selected disabled>Choose access level...</option>
                                        <option {{ $user->access == 'Admin' ? 'selected' : '' }}>Admin</option>
                                        <option {{ $user->access == 'Agency' ? 'selected' : '' }}>Agency</option>
                                        <option {{ $user->access == 'Client' ? 'selected' : '' }}>Client</option>
                                    </select>
                                    <label for="access" class="floating-label">User Type</label>
                                </div>
                                <div class="form-group form-material floating">
                                    <input type="text" class="form-control" name="organization" data-fv-field="organization" value="{{ old('organization') ?: $user->organization }}" required>
                                    <label for="organization" class="floating-label">User Organization</label>
                                </div>
                                <div class="form-group form-material floating">
                                    <input type="text" class="form-control" name="first_name" autocomplete="off" data-fv-field="first_name" value="{{ old('first_name') ?: $user->first_name }}" required>
                                    <label for="first_name" class="floating-label">First Name</label>
                                </div>
                                <div class="form-group form-material floating">
                                    <input type="text" class="form-control" name="last_name" autocomplete="off" data-fv-field="last_name" value="{{ old('last_name') ?: $user->last_name }}" required>
                                    <label for="last_name" class="floating-label">Last Name</label>
                                </div>
                            </div>
                            <div class="tab-pane" id="exampleTabsReverseTwo" rsole="tabpanel">
                                <div class="form-group form-material floating">
                                    <select name="timezone" class="form-control selectpicker" style="margin-top: 10px; padding-top: 10px;">
                                        <option disabled {{ old('timezone') ?: $user->timezone == '' ? 'selected' : '' }}>Choose Timezone...</option>
                                        @foreach (DateTimeZone::listIdentifiers(DateTimeZone::AMERICA) as $timezone)
                                            <option {{ old('timezone') ?: $user->timezone == $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                                        @endforeach
                                    </select>
                                    <label for="timezone" class="floating-label" style="margin-bottom: 10px; padding-bottom: 10px">Timezone</label>
                                </div>
                                <div class="form-group form-material floating">
                                    <input type="email"
                                           class="form-control {{ ! empty(old('email') ?: $user->email) ?: 'empty' }}"
                                           name="email"
                                           autocomplete="off"
                                           data-fv-field="email"
                                           value="{{ old('email') ?: $user->email }}"
                                           required>
                                    <label for="email" class="floating-label">Email Address</label>
                                </div>
                                <div class="form-group form-material floating">
                                    <input type="text"
                                           class="form-control {{ ! empty(old('email') ?: $user->phone_number) ?: 'empty' }}"
                                           name="phone_number"
                                           autocomplete="off"
                                           value="{{ old('phone_number') ?: $user->phone_number }}"
                                           data-plugin="formatter"
                                           data-pattern="([[999]]) [[999]]-[[9999]]"
                                           required>
                                    <label for="phone" class="floating-label">Phone Number</label>
                                </div>
                            </div>
                            <div class="tab-pane" id="exampleTabsReverseThree" role="tabpanel">
                                <div class="form-group">
                                    Username: <p class="form-control-static">{{ $user->username }}</p>
                                </div>
                                <button class="btn btn-info update-password">Update Password</button>
                                <div class="password-fields">
                                    <div class="form-group">
                                        <label for="password" class="form-control-label">Password</label>
                                        <input type="password" name="password" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="verify_password" class="form-control-label">Verify Password</label>
                                        <input type="password" name="verify_password" class="form-control" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button id="save-user-button" class="btn btn-success float-right">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scriptTags')
    <script src="{{ secure_url('js/Plugin/material.js') }}"></script>
    <script src="{{ secure_url('js/Plugin/jquery-wizard.js') }}"></script>
    <script src="{{ secure_url('vendor/jquery-wizard/jquery-wizard.js') }}"></script>

    <script src="{{ secure_url('js/Plugin/icheck.js') }}"></script>
    <script src="{{ secure_url('vendor/icheck/icheck.js') }}"></script>

    <script src="{{ secure_url('js/Plugin/bootstrap-tokenfield.js') }}"></script>
    <script src="{{ secure_url('vendor/bootstrap-tokenfield/bootstrap-tokenfield.js') }}"></script>

    <script src="{{ secure_url('js/Plugin/bootstrap-datepicker.js') }}"></script>
    <script src="{{ secure_url('vendor/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>

    <script src="{{ secure_url('vendor/typeahead-js/typeahead.bundle.min.js') }}"></script>
    <script src="{{ secure_url('vendor/formvalidation/formValidation.js') }}"></script>
    <script src="{{ secure_url('vendor/formvalidation/framework/bootstrap.js') }}"></script>
    <script src="{{ secure_url('js/Plugin/sweetalert.min.js') }}"></script>

    <script src="{{ secure_url('js/Plugin/formatter.js') }}"></script>
    <script src="{{ secure_url('vendor/formatter/jquery.formatter.js') }}"></script>

    <script type="text/javascript" src="{{ secure_url('js/Plugin/bootstrap-select.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/bootstrap-select/bootstrap-select.js') }}"></script>

    <script type="text/javascript" src="{{ secure_url('vendor/jsgrid/jsgrid.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $(".password-fields").hide();

            $(".update-password").click(function(ev){
                ev.preventDefault();

                $(".password-fields").toggle();
            });

            $(".selectpicker").selectpicker();

            $("#save-user-button").click(function() {
                $("#save-user-button").attr('disabled', 'disabled').addClass('disabled');

                $(this).parent().closest('form').submit();
            });

        });
    </script>
@endsection
