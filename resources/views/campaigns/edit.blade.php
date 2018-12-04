@extends('layouts.remark_campaign')

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
    <link type="text/css" rel="stylesheet" href="{{ secure_url('css/jsgrid.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('css/jsgrid-theme.css') }}">
    <style type="text/css" media="all">
        form > h4 {
            margin-top: 20px;
        }
        .btn.dropdown-toggle.btn-default {
            margin-top: 8px;
        }
    </style>
@endsection

@section('manualStyle')
    .wizard-buttons {
        padding-top: 50px;
    }
@endsection

@section('campaign_content')
    <div class="container-fluid">
        <form data-fv-live="enabled" id="campaign-form" class="form form-horizontal" action="{{ secure_url('/campaign/' . $campaign->id . '/update') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="phone_number_id" value="{{ $campaign->phone_number_id }}">
            <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
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
                    <h2>Edit Campaign</h2>
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
                                    Accounts
                                </a>
                            </li>
                            <li class="nav-item" role="presentation" style="display: list-item;">
                                <a class="nav-link"
                                   data-toggle="tab"
                                   href="#exampleTabsReverseThree"
                                   aria-controls="exampleTabsReverseThree"
                                   role="tab">
                                    Phone
                                </a>
                            </li>
                            <li class="nav-item" role="presentation" style="display: list-item;">
                                <a class="nav-link"
                                   data-toggle="tab"
                                   href="#exampleTabsReverseFour"
                                   aria-controls="exampleTabsReverseFour"
                                   role="tab">
                                    Leads
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
                                        Phone
                                    </a>
                                    <a class="dropdown-item"
                                       data-toggle="tab"
                                       href="#exampleTabsReverseFour"
                                       aria-controls="exampleTabsReverseFour"
                                       role="tab">
                                        Leads
                                    </a>
                                </div>
                            </li>
                        </ul>
                        <div class="tab-content pt-20">
                            <div class="tab-pane active" id="exampleTabsReverseOne" role="tabpanel">
                                <div class="form-group floating">
                                    <label for="name" class="floating-label">Campaign Name</label>
                                    <input type="text" class="form-control" name="name" data-fv-field="name" value="{{ old('name') ?: $campaign->name }}" required>
                                </div>
                                <div class="form-group floating">
                                    <label for="order" class="floating-label">Order #</label>
                                    <input type="text" class="form-control" name="order" autocomplete="off" data-fv-field="order" value="{{ old('order') ?: $campaign->order_id }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="floating-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option {{ old('status') ?: $campaign->status == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option {{ old('status') ?: $campaign->status == 'Archived' ? 'selected' : '' }}>Archived</option>
                                        <option {{ old('status') ?: $campaign->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                        <option {{ old('status') ?: $campaign->status == 'Expired' ? 'selected' : '' }}>Expired</option>
                                        <option {{ old('status') ?: $campaign->status == 'Upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    </select>
                                </div>
                                <div class="form-group floating">
                                    <div class="input-daterange" data-plugin="datepicker" style="padding-bottom: 40px">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="icon md-calendar" aria-hidden="true"></i>
                                            </span>
                                            <input type="text" class="form-control {{ empty(old('start')) && empty($campaign->starts_at) ? 'empty' : ''  }}" name="start" placeholder="Starts on" value="{{ old('start') ?: ! empty($campaign->starts_at) ? $campaign->starts_at->format("m/d/Y") : '' }}">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon">to</span>
                                            <input type="text" class="form-control {{ empty(old('end')) && empty($campaign->ends_at) ? 'empty' : ''  }}" name="end" placeholder="Ends on" value="{{ old('end') ?: ! empty($campaign->ends_at) ? $campaign->ends_at->format("m/d/Y") : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="exampleTabsReverseTwo" role="tabpanel">
                                <div class="form-group">
                                    <label for="agency" class="floating-label">Agency</label>
                                    <select class="form-control select2" name="agency" data-width="auto" autocomplete="off" required>
                                        <option disabled selected>Agency</option>
                                        @if ($agencies->count() > 0)
                                            @foreach ($agencies as $agency)
                                                <option value="{{ $agency->id }}" {{ $campaign->agency_id == $agency->id ? 'selected' : '' }}>{{ $agency->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="client" class="floating-label">Dealership</label>
                                    <select class="form-control select2" name="client" data-width="auto" autocomplete="off" required>
                                        <option disabled selected>Client</option>
                                        @if ($dealerships->count() > 0)
                                            @foreach ($dealerships as $dealership)
                                                <option value="{{ $dealership->id }}" {{ $campaign->dealership_id == $dealership->id ? 'selected' : '' }}>{{ $dealership->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="tab-pane" id="exampleTabsReverseThree" role="tabpanel">
                                @if ($campaign->phone)
                                <div class="checkbox floating">
                                    <label for="phone_number" class="floating-label">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control" value="{{ $campaign->phone->phone_number }}" disabled>
                                </div>
                                <div class="checkbox floating">
                                    <label for="forward" class="floating-label">Forward Number</label>
                                    <input type="text" name="forward"
                                           id="wtf"
                                           class="form-control {{ ! empty(old('forward') ?: $campaign->phone->forward) ?: "empty"  }}"
                                           value="{{ old('forward') ?: $campaign->phone->forward }}"
                                    >
                                </div>
                                @else
                                    <h3>Phone
                                        <button id="#phone-search-button"
                                                type="button"
                                                role="button"
                                                class="btn btn-round btn-sm btn-success"
                                                data-toggle="modal"
                                                data-target="#addPhoneModal"
                                        >
                                            <i class="icon md-plus" aria-label="Add a new phone number"></i>
                                            Add
                                        </button>
                                    </h3>
                                @endif
                            </div>
                            <div class="tab-pane" id="exampleTabsReverseFour" role="tabpanel">
                                <div class="checkbox floating">
                                    <label>
                                        <input name="adf_crm_export" type="checkbox" class="icheckbox-primary" {{ $campaign->adf_crm_export ? 'checked="checked"' : '' }}> Enable ADF CRM Export
                                    </label>
                                </div>
                                <div id="adf_crm_integration_form" class="col-md-11 col-md-offset-1">
                                    <div class="form-group floating">
                                        <label for="adf_crm_export_email" class="floating-label">ADF CRM Email</label>
                                        <input type="text" class="form-control multi-email" name="adf_crm_export_email" value="{{ old('adf_crm_export_email') ?: $campaign->adf_crm_export_email }}">
                                    </div>
                                </div>
                                <div class="checkbox floating">
                                    <label>
                                        <input name="lead_alerts" type="checkbox" class="icheckbox-primary" {{ $campaign->lead_alerts ? 'checked="checked"' : '' }}>
                                        Enable Lead Alerts
                                    </label>
                                </div>
                                <div id="adf_crm_lead_alert_email_form" class="col-md-11 col-md-offset-1">
                                    <div class="form-group floating">
                                        <label for="lead_alert_email" class="floating-label">Lead Alert Email(s)</label>
                                        <input type="text" class="form-control multi-email" name="lead_alert_email" value="{{ old('lead_alert_email') ?: $campaign->lead_alert_email }}">
                                    </div>
                                </div>
                                <div class="checkbox floating">
                                    <label>
                                        <input name="client_passthrough" type="checkbox" class="icheckbox-primary" {{ $campaign->client_passthrough ? 'checked="checked"' : '' }}> Enable Client Passthrough
                                    </label>
                                </div>
                                <div id="adf_crm_client_passthrough_form" class="col-md-11 col-md-offset-1">
                                    <div class="form-group floating">
                                        <label for="client_passthrough_email" class="floating-label">Client Passthrough Email(s)</label>
                                        <input type="text" class="form-control multi-email" name="client_passthrough_email" value="{{ old('client_passthrough_email') ?: $campaign->client_passthrough_email }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button id="save-campaign-button" class="btn btn-success float-right">Save</button>
                    </div>
                </div>
            </div>
        </form>
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

    <script src="{{ secure_url('js/Plugin/formatter.js') }}"></script>
    <script src="{{ secure_url('vendor/formatter/jquery.formatter.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/jsgrid/jsgrid.min.js') }}"></script>

    <script type="text/javascript">
        $(document).keypress(function(event) {
            if (event.which == '13') {
                event.preventDefault();
            }
        });

        $(document).ready(function() {
            var enable_adf = false;
            var enable_alerts = false;
            var enable_client_passthrough = false;
            var schedules = 1;

            $("#wtf").change(function() {
                if ($(this).val().length > 0) {
                    $("input[name=forward]").removeClass("empty");
                } else {
                    $("input[name=forward]").addClass("empty");
                }
            });

            if (! $("input[name=adf_crm_export]").prop("checked")) {
                $("#adf_crm_integration_form").toggle();
            }
            if (! $("input[name=lead_alerts]").prop("checked")) {
                $("#adf_crm_lead_alert_email_form").toggle();
            }
            if (! $("input[name=client_passthrough]").prop("checked")) {
                $("#adf_crm_client_passthrough_form").toggle();
            }

            $("#add-new-client").click(function() {
                sweetAlert("New Client", "Added a new client", "success");
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

            $(".multi-email").tokenfield({
                "inputType": "email"
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
                                {
                                    phone_number: $("input[name=phone_number]").val(),
                                    campaign_id: $("input[name=campaign_id]").val(),
                                    client_id: $("select[name=client] option:selected").val()
                                },
                                function (data) {
                                    console.log(data, data.id, data.number);
                                    $("input[name=phone_number_id]").val(data.id);
                                    var phone_html = '<div class="checkbox form-material floating">' +
                                    '    <input type="text" name="phone_number" class="form-control" value="' + data.number + '" disabled>' +
                                    '    <label for="phone_number" class="floating-label">Phone Number</label>' +
                                    '</div>' +
                                    '<div class="checkbox form-material floating">' +
                                    '    <input type="text" name="forward" ' +
                                    '           id="forward_number"' +
                                    '           class="form-control"' +
                                    '           value="">' +
                                    '    <label for="forward" class="floating-label">Forward Number</label>' +
                                    '</div>';

                                    $(".tab-pane.active").append(phone_html);

                                    $("#generate-phone").addClass("disabled");
                                    $("#generate-phone").removeAttr("data-toggle");
                                    $("#generate-phone").removeAttr("data-target");
                                    $("#generate-phone").removeData();
                                    $("#generate-phone").hide();
                                },
                                'json'
                            );
                        });
                    },
                    'json'
                );
            });

            $("#save-campaign-button").click(function() {
                $("#save-campaign-button").attr('disabled', 'disabled').addClass('disabled');

                $(this).parent().closest('form').submit();
            });

        });
    </script>
@endsection
