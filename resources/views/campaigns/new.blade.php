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
@endsection

@section('manualStyle')
    .wizard-buttons {
        padding-top: 50px;
    }
@endsection

@section('content')
<div class="page">
    <div class="page-header container-fluid">
        <div class="row-fluid">
            <div class="col-md-12">
            </div>
        </div>
    </div>
    <div class="page-content container-fluid">
        <div class="row-fluid">
            <div class="col-md-6 offset-md-3">
                <div class="panel" id="add-new-campaign-wizard">
                    <form data-fv-live="enabled" id="campaign-form" class="form form-horizontal" action="{{ secure_url('/campaigns/create') }}" method="post">
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
                                    <div class="pearl-icon"><i class="icon md-account" aria-hidden="true"></i></div>
                                    <span class="pearl-title">Accounts</span>
                                </div>
                                <div class="pearl col-4">
                                    <div class="pearl-icon"><i class="icon md-phone" aria-hidden="true"></i></div>
                                    <span class="pearl-title">Contact</span>
                                </div>
                            </div>
                            <div class="wizard-content">
                                <div id="campaign-details" class="wizard-pane" role="tabpanel" aria-expanded="false">
                                    <h4>Campaign Details</h4>
                                    <div class="form-group">
                                        <label for="name" class="floating-label">Campaign Name</label>
                                        <input type="text" class="form-control empty" name="name" data-fv-field="name" value="{{ old('name') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="order" class="floating-label">Order #</label>
                                        <input type="text" class="form-control empty" name="order" autocomplete="off" data-fv-field="order" value="{{ old('order') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="name" class="floating-label">Status</label>
                                        <select name="status" class="form-control">
                                            <option {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option {{ old('status') == 'Archived' ? 'selected' : '' }}>Archived</option>
                                            <option {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option {{ old('status') == 'Expired' ? 'selected' : '' }}>Expired</option>
                                            <option {{ old('status') == 'Upcoming' ? 'selected' : '' }}>Upcoming</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-daterange" data-plugin="datepicker" style="padding-top: 20px;">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="icon md-calendar" aria-hidden="true"></i>
                                                </span>
                                                <input type="text" class="form-control" name="start" placeholder="Starts on">
                                            </div>
                                            <div class="input-group">
                                                <span class="input-group-addon">to</span>
                                                <input type="text" class="form-control" name="end" placeholder="Ends on">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="campaign-accounts" class="wizard-pane" role="tabpanel" aria-expanded="false">
                                    <h4>Accounts</h4>
                                    <div class="form-group form-material floating">
                                        <select class="form-control select2" name="agency" autocomplete="off" required>
                                            <option disabled selected>Agency</option>
                                            @if ($agencies->count() > 0)
                                                @foreach ($agencies as $agency)
                                                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                                @endforeach
                                            @endif

                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group form-material floating">
                                            <select class="form-control select2" name="client" autocomplete="off" required>
                                                <option disabled selected>Dealership</option>
                                                @if ($dealerships->count() > 0)
                                                    @foreach ($dealerships as $dealership)
                                                        <option value="{{ $dealership->id }}">{{ $dealership->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div id="campaign-crm" class="wizard-pane" role="tabpanel" aria-expanded="true">
                                    <h4>Lead Management</h4>
                                    <div class="checkbox">
                                        <label>
                                            <input name="adf_crm_export" type="checkbox" class="icheckbox-primary"> Enable ADF CRM Export
                                        </label>
                                    </div>
                                    <div id="adf_crm_integration_form" class="col-md-11 col-md-offset-1">
                                        <div class="form-group form-material floating multi-email">
                                            <label for="adf_crm_export_email" class="form-label">ADF CRM EXPORT</label>
                                            <input type="text" class="form-control multi-email-tokens" name="adf_crm_export_email" placeholder="Export Email">
                                        </div>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input name="lead_alerts" type="checkbox" class="icheckbox-primary">
                                            Enable Lead Alerts
                                        </label>
                                    </div>
                                    <div id="adf_crm_lead_alert_email_form" class="col-md-11 col-md-offset-1">
                                        <div class="form-group form-material floating multi-email">
                                            <input type="text" class="form-control" name="lead_alert_email" placeholder="add multiple emails seperated by commas">
                                            <label for="lead_alert_email" class="form-label multi-email-tokens">Lead Alert Email Details</label>
                                        </div>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input name="client_passthrough" type="checkbox" class="icheckbox-primary"> Enable Client Passthrough
                                        </label>
                                    </div>
                                    <div id="adf_crm_client_passthrough_form" class="col-md-11 col-md-offset-1">
                                        <div class="form-group form-material floating multi-email">
                                            <input type="text" class="form-control  multi-email-tokens" name="client_passthrough_email">
                                            <label for="client_passthrough_email" class="floating-label">Client Passthrough Email</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button id="generate-phone" class="btn btn-primary waves-effect" data-target="#addPhoneModal" data-toggle="modal" type="button">Generate Phone Number</button>
                                        <input style="display:none;" type="text" class="hide hidden" name="phone_number_id" value="{{ old('phone_number_id') }}">
                                        <ul class="list-group" id="phone-numbers"></ul>
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
<div class="modal fade show" id="addPhoneModal" aria-labelledby="addPhoneModalLabel" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="phone-search-form" class="form" action="{{ route('phone.search') }}" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="addPhoneModalLabel">Add a new Phone Number</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="radio-inline ">
                                <input name="country" class="form-group" type="radio" value="US" checked="checked"> US
                            </label>
                            <label class="radio-inline">
                                <input name="country" class="form-group" type="radio" value="CA"> CA
                            </label>
                        </div>
                        <div class="col-md-3 form-group">
                            <input type="text" class="form-control" name="areaCode" placeholder="Area Code">
                        </div>
                        <div class="col-md-3 form-group">
                            <input type="text" class="form-control" name="inPostalCode" placeholder="Zip">
                        </div>
                        <div class="col-md-3 form-group">
                            <input type="text" class="form-control" name="contains" placeholder="Contains ex. Cars...">
                        </div>
                        <div class="col-md-12 float-right">
                            <button id="phone-search-button" class="btn btn-primary waves-effect" type="button">Search Phones</button>
                        </div>
                        <div class="col-md-12" style="margin-top: 15px;" id="phone-search-results">
                        </div>
                        <ul class="list-group" id="phone_numbers"></ul>
                    </div>
                </div>
            </form>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var enable_adf = false;
            var enable_alerts = false;
            var enable_client_passthrough = false;
            var schedules = 1;

            $("#adf_crm_integration_form").toggle();
            $("#adf_crm_lead_alert_email_form").toggle();
            $("#adf_crm_client_passthrough_form").toggle();

            $("input.multi-email-token").tokenfield()
                .on('tokenfield:createtoken', function (e) {
                    var data = e.attrs.value.split('|');
                    e.attrs.value = data[1] || data[0];
                    e.attrs.label = data[1] ? data[0] + ' (' + data[1] + ')' : data[0];
                })
                .on('tokenfield:createdtoken', function (e) {
                    // Über-simplistic e-mail validation
                    var re = /\S+@\S+\.\S+/;
                    var valid = re.test(e.attrs.value);
                    if (!valid) {
                        $(e.relatedTarget).addClass('invalid');
                    }
                });

            /*
            $("input.multi-email-tokens").blur(function (e) {
                $(this).tokenfield('setTokens', $(this)[0].value);
            });
            */

            var defaults = Plugin.getDefaults("wizard");
            var options = $.extend(true, {}, defaults, {
                onInit: function () {
                    $("#campaign-details").formValidation({
                        framework: 'bootstrap',
                        icon: {
                            valid: 'glyphicon glyphicon-ok',
                            invalid: 'glyphicon glyphicon-remove',
                            validating: 'glyphicon glyphicon-refresh'
                        },
                        // This option will not ignore invisible fields which belong to inactive panels
                        excluded: ':disabled',
                        fields: {
                            name: {
                                validators: {
                                    notEmpty: {
                                        message: 'The campaign name is required'
                                    }
                                }
                            },
                            order: {
                                validators: {
                                    notEmpty: {
                                        message: 'The order number is required'
                                    },
                                    integer: {
                                        message: 'The value is not an number'
                                    }
                                }
                            }
                        },
                        row: {
                            invalid: 'has-danger'
                        }
                    })
                    .on('err.field.fv', function(e, data) {
                        //$("#nextButton").addClass("disabled");
                    })
                    .on('success.field.fv', function(e, data) {
                        //$("#nextButton").removeClass("disabled");
                    });

                    $("#campaign-accounts").formValidation({
                        framework: "bootstrap",
                        icon: null,
                        button: {
                            selector: '#nextButton',
                            disabled: 'disabled'
                        },
                        fields: {
                            agency: {
                                validators: {
                                    notEmpty: {
                                        message: 'The agency is required'
                                    }
                                }
                            },
                            client: {
                                validators: {
                                    notEmpty: {
                                        message: 'The client is required'
                                    }
                                }
                            }
                        }
                    })
                    .on('err.field.fv', function(e, data) {
                        //$("#nextButton").addClass("disabled");
                    })
                    .on('success.field.fv', function(e, data) {
                        //$("#nextButton").removeClass("disabled");
                    });
                },
                validator: function() {
                    /*
                    var fv = $('#campaign-form').data('formValidation');

                    var $this = $("div.wizard-pane.active");

                    // Validate the container
                    fv.validateContainer($("#campaign-details"));

                    var isValidStep = fv.isValidContainer($this);
                    if (isValidStep === false || isValidStep === null) {
                        console.log(isValidStep);
                        return false;
                    }

                    return false;
                    */
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

                    $('#campaign-form').submit();
                },
                buttonsAppendTo: '.panel-body'
            });

            $('#campaign-form').wizard(options).data("wizard");

            $("#add-new-client").click(function() {
                sweetAlert("New Client", "Added a new client", "success");
            });
            $("#add-drop").click(function(e) {
                e.preventDefault();

                $(".schedule-list").append(
                    '<div class="form-group">' +
                    '<label for="type'+schedules+'">Deployment Type</label>' +
                    '<select class="form-control" name="type'+schedules+'">' +
                    '   <option>Email</option><option>SMS</option>' +
                    '</select> <button class="btn btn-primary btn-floating">&rarr;</button></div>'
                );
                schedules++;
            });
            $("input[name=adf_crm_export]").change(function() {
                enable_adf = !enable_adf;
                $("#adf_crm_integration_form").toggle();
            });
            $("input[name=lead_alerts]").change(function() {
                enable_alerts = !enable_alerts;
                $("#adf_crm_lead_alert_email_form").toggle();
            });
            $("input[name=client_passthrough]").change(function() {
                enable_client_passthrough = !enable_client_passthrough;
                $("#adf_crm_client_passthrough_form").toggle();
            });

            $('.select2').select2();

            $("#phone-search-button").click(function() {
                $.post(
                    "{{ route('phone.search') }}",
                    $("#phone-search-form").serialize(),
                    function (data) {
                        var html = '<form id="phone-form"><table class="table table-hover table-striped table-bordered">' +
                                '<input type="hidden" style="display: none;" name="client_id" value="' + $("select[name=client]").val() + '">' +
                            '<thead><tr><th></th><th>Phone</th><th>Region</th></tr></thead>' +
                            '<tbody>';
                        $(data.numbers).each(function (phone) {
                            html += '<tr><td><input type="radio" name="phone_number" value="' + $(this)[0].phoneNumber + '"></td><td>' + $(this)[0].phone + '</td><td>' + $(this)[0].location + '</td></tr>';
                        });
                        html += '</tbody>' +
                            '</table>' +
                        '<div class="col-md-12 float-right">' +
                        '    <button id="add-phone" class="btn btn-success waves-effect" data-dismiss="modal" type="button">$ Purchase Number</button>' +
                        '</div></form>';

                        $("#phone-search-results").html(html);

                        $("#add-phone").click(function() {
                            $.post(
                                "{{ route('phone.provision') }}",
                                { phone_number: $("input[name=phone_number]").val(), client_id: 999999 },
                                function (data) {
                                    console.log(data, data.id, data.number);
                                    $("input[name=phone_number_id]").val(data.id);
                                    $("ul#phone-numbers").append('<li class="list-group-item">' + data.number + '</li>');
                                    $("#generate-phone").addClass("disabled");
                                    $("#generate-phone").removeAttr("data-toggle");
                                    $("#generate-phone").removeAttr("data-target");
                                    $("#generate-phone").removeData();
                                },
                                'json'
                            );
                        });
                    },
                    'json'
                );
            });
        });
    </script>
@endsection

@section('scripts')
@endsection
