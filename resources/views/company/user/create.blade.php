@extends('layouts.remark')

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
@endsection

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-md-6 offset-md-3">
                    <button type="button"
                            role="button"
                            data-url="{{ route('company.user.index', ['company' => $company->id]) }}"
                            class="btn btn-sm float-left btn-default waves-effect campaign-edit-button"
                            data-toggle="tooltip"
                            data-original-title="Go Back"
                            style="margin-right: 15px; background: rgba(255, 255, 255, 0.2); border-size: 0.5px;">
                        <i class="icon fa-angle-left" style="color: #efefef" aria-hidden="true"></i>
                    </button>
                    <h3 class="page-title text-default d-flex align-items-center">
                        New User <small class="ml-auto"># {{ $company->id }}: {{ ucwords($company->name) }}</small>
                    </h3>
                </div>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="row-fluid" data-plugin="matchHeight" data-by-row="true">
                <div class="col-md-6 offset-md-3">
                    <div class="panel" id="add-new-campaign-wizard">
                        <form data-fv-live="enabled" id="user-form" class="form form-horizontal" action="{{ route('company.user.store', ['company' => $company->id]) }}" method="post">
                            {{ csrf_field() }}
                            <div class="panel-body">
                                @if ($errors->count() > 0)
                                    <div class="alert alert-danger">
                                        <h3>There were some errors:</h3>
                                        <ul>
                                            @foreach ($errors->all() as $message)
                                                <li>{{ $message }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="pearls row">
                                    <div class="pearl current col-4">
                                        <div class="pearl-icon"><i class="icon md-format-list-bulleted" aria-hidden="true"></i></div>
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
                                            <label for="role" class="floating-label">Role</label>
                                            <select name="role" value="{{ old('role') }}" class="form-control" required>
                                                <option selected disabled>Choose role...</option>
                                                <option value="admin">Admin</option>
                                                <option value="user">User</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="order" class="floating-label">First Name</label>
                                            <input type="text" class="form-control empty" name="first_name" autocomplete="off" data-fv-field="first_name" value="{{ old('first_name') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="order" class="floating-label">Last Name</label>
                                            <input type="text" class="form-control empty" name="last_name" autocomplete="off" data-fv-field="last_name" value="{{ old('last_name') }}" required>
                                        </div>
                                    </div>
                                    <div id="user-contacts" class="wizard-pane" role="tabpanel" aria-expanded="false">
                                        <h4>Contact</h4>
                                        <div class="form-group">
                                            <input type="email"
                                                   class="form-control {{ ! empty(old('email')) ?: 'empty' }}"
                                                   name="email"
                                                   autocomplete="off"
                                                   data-fv-field="email"
                                                   value="{{ old('email') }}"
                                                   required>
                                            <label for="email" class="floating-label">Email Address</label>
                                        </div>
                                        <div class="form-group">
                                            <input type="text"
                                                   class="form-control empty"
                                                   name="phone_number"
                                                   autocomplete="off"
                                                   value="{{ old('phone_number') }}"
                                                   data-plugin="formatter"
                                                   data-pattern="([[999]]) [[999]]-[[9999]]"
                                            required>
                                            <label for="phone" class="floating-label">Phone Number</label>
                                        </div>
                                        <div class="form-group">
                                            <select name="timezone" class="form-control" data-plugin="select2">
                                                <option disabled {{ old('timezone') == '' ? 'selected' : '' }}>Choose Timezone...</option>
                                                @foreach (App\Models\User::getPossibleTimezonesForUser() as $timezone)
                                                    <option {{ old('timezone') == $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div id="user-auth" class="wizard-pane" role="tabpanel" aria-expanded="true">
                                        <h4>Authentication</h4>
                                        <div class="form-group">
                                            <label for="username" class="form-control-label">Username</label>
                                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="form-control-label">Password</label>
                                            <input type="password" name="password" class="form-control" value="" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="verify_password" class="form-control-label">Verify Password</label>
                                            <input type="password" name="verify_password" class="form-control" value="" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script type="text/javascript">
        var errorFields = [];
        $(document).ready(function() {
            var defaults = Plugin.getDefaults("wizard");
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
                        var html = '<div class="" style="padding-top: 50px">' +
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
                buttonsAppendTo: '.panel-body'
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
@endsection

