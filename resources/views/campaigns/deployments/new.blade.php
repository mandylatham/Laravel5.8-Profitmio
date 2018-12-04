@extends('layouts.remark')

@section('header')
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/jquery-wizard/jquery-wizard.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/icheck/icheck.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/typeahead-js/typeahead.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-tokenfield/bootstrap-tokenfield.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/jt-timepicker/jquery-timepicker.min.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/formvalidation/formValidation.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('css/sweetalert.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <style type="text/css">
        #editor {
            min-height: 500px;
        }
        /** FIX WIZARD HEIGHT ISSUE */
        .wizard-pane {
            display:none;
        }
        .wizard-pane.active {
            display: inherit;
        }
        .datepicker {
            z-index: 9000;
        }
    </style>
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
                <div class="col-md-8 offset-md-2">
                    <div class="panel" id="add-new-campaign-wizard">
                        <form data-fv-live="enabled" id="campaign-form" class="form form-horizontal" action="{{ secure_url('/campaign/' . $campaign->id . '/drops/create') }}" method="post">
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
                                        <div class="pearl-icon"><i class="icon fa-users" aria-hidden="true"></i></div>
                                        <span class="pearl-title">Recipients</span>
                                    </div>
                                    <div class="pearl col-4">
                                        <div class="pearl-icon"><i class="icon fa-image" aria-hidden="true"></i></div>
                                        <span class="pearl-title">Media</span>
                                    </div>
                                    <div class="pearl col-4">
                                        <div class="pearl-icon"><i class="icon fa-calendar" aria-hidden="true"></i></div>
                                        <span class="pearl-title">Schedule</span>
                                    </div>
                                </div>
                                <div class="wizard-content">
                                    <div id="campaign-details" class="wizard-pane text-center" role="tabpanel" aria-expanded="false">
                                        <div class="row">
                                            <div class="col-md-6 offset-md-3">
                                                <h3>Welcome to the New Drop Wizard!</h3>
                                                <p>This wizard will allow you to take a piece of media, be it email or text, and apply
                                                    it to a group of recipients in any number of drops.  Each drop can have a distinct
                                                    drop schedule.</p>

                                                <button id="show_assign_recipients_form"
                                                        class="btn btn-primary waves-effect"
                                                        data-target="#addRecipientsModal"
                                                        data-toggle="modal"
                                                        type="button">
                                                    <i class="icon fa-users"></i>
                                                    Add Recipients
                                                </button>
                                                <div id="added-groups" class="row"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="campaign-accounts" class="wizard-pane" role="tabpanel" aria-expanded="false">
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <div id="template-inputs" class="form-group">
                                                    @if ($templates->count() > 0)
                                                        <label for="template" class="form-control-label">
                                                            Use a Template
                                                            <span class="text-warning" style="margin-left: 10px;">
                                                                <i class="icon fa-exclamation-triangle"></i>
                                                                Resets the form!
                                                            </span>
                                                        </label>
                                                        <select class="form-control" name="template" data-plugin="select2" data-width="100%">
                                                            <option value="none" selected>Custom (no template)</option>
                                                            @foreach ($templates as $template)
                                                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <div class="alert alert-info">No templates available</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="type" class="floating-label">Drop Type</label>
                                                <select id="drop_type" name="type" class="form-control" data-fv-field="type" required>
                                                    <option value='email' {{ old('type') ? 'selected' : '' }}>Email</option>
                                                    <option value='sms' {{ old('type') ? 'selected' : '' }}>SMS</option>
                                                    <option disabled><s>Voice</s></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="email-fields">
                                            <div class="form-group">
                                                <label for="email_subject" class="floating-label">Email Subject</label>
                                                <input id="drop_email_subject"
                                                       type="text"
                                                       class="form-control {{ ! empty(old('email_subject')) ?: 'empty' }}"
                                                       name="email_subject"
                                                       placeholder="Email Subject"
                                                       autocomplete="off"
                                                       data-fv-field="email_subject"
                                                       value="{{ old('email_subject') }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="email_text" class="floating-label">Email Text</label>
                                                <textarea id="drop_email_text"
                                                          class="form-control empty"
                                                          name="email_text"
                                                          placeholder="Email Plain Text"
                                                          autocomplete="off">{{ old('email_text') }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="email_html" class="floating-label">Email HTML</label>
                                                <div id="editor">{{ old('email_html') }}</div>
                                                <input id="drop_email_html" type="hidden" name="email_html" value="{{ old('email_html') }}">
                                            </div>
                                        </div>
                                        <div id="sms-fields">
                                            <div class="form-group">
                                                <label for="text_message" class="floating-label">Text Message</label>
                                                <textarea id="drop_text_message"
                                                          class="form-control empty"
                                                          name="text_message"
                                                          placeholder="Text Message"
                                                          autocomplete="off">{{ old('text_message') }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <label>
                                                        <input id="drop_send_vehicle_image" type="checkbox" name="send_vehicle_image" {{ empty(old('send_vehicle_image')) ?: 'checked="checked"' }}>
                                                        Send Vehicle Image
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="text_message_image" class="floating-label">Vehicle Image</label>
                                                <input id="drop_text_message_image" type="text" name="text_message_image" class="form-control" placeholder="Vehicle Image Location" value="{{ old('text_message_image') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="scheduling" class="wizard-pane" role="tabpanel" aria-expanded="true"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade show" id="addRecipientsModal" aria-labelledby="addRecipientsModalLabel" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="recipients-search-form" class="form" action="{{ secure_url('/campaign/' . $campaign->id . '/drops/add-groups') }}" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="addRecipientsModalLabel">Add Recipients</h4>
                    </div>
                    <div class="modal-body">
                        <div id="counts" class="row">
                            <div class="col-md-6">
                                <div class="counter counter-md">
                                    <div class="counter-label grey-600">Recipient Count</div>
                                    <div class="counter-number-group">
                                        <span id="recipient_count" class="counter-number">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="counter counter-md">
                                    <div class="counter-label grey-600">Group Count</div>
                                    <div class="counter-number-group">
                                        <span id="group_count" class="counter-number">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact_filter" class="form-control-label">Contact Method</label>
                                    <select id="contact_filter" name="contact_filter" class="form-control">
                                        <option value="all-sms">All SMS-able</option>
                                        <option value="all-email">All Email-able</option>
                                        <option value="sms-only">Only SMS-able which aren't Email-able</option>
                                        <option value="email-only">Only Email-able which aren't SMS-able</option>
                                        <option value="no-resp-email">All Email-able who haven't responded yet</option>
                                        <option value="no-resp-sms">All Sms-able who haven't responded yet</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="data_source" class="form-control-label">Data Source</label>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="conquest-data-source" name="data_source[]" value="conquest" checked="checked" />
                                            Conquest
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="database-data-source" name="data_source[]" value="database" checked="checked" />
                                            Database
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="recipient_lists" class="form-control-label">Recipient Lists</label>
                                <select id="recipient_lists" name="recipient_lists" class="form-control" multiple="multiple">
                                    <option value="">All</option>
                                    @foreach (\App\Models\RecipientList::whereCampaignId($campaign->id)->get() as $list)
                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="max" class="form-control-label">Maximums</label>
                                <input id="max_per_group" type="text" class="form-control" name="max" placeholder="Max per Group">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div id="group-table" class="table-responsive">
                                    <table id="group-list" class="table table-condensed">
                                        <thead>
                                        <tr>
                                            <th>Group</th>
                                            <th>Count</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button id="add-groups" data-dismiss="modal" class="btn btn-success">Assign Recipients</button>
                            </div>
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

    <script src="{{ secure_url('js/Plugin/jt-timepicker.min.js') }}"></script>
    <script src="{{ secure_url('vendor/jt-timepicker/jquery.timepicker.min.js') }}"></script>

    <script src="{{ secure_url('vendor/typeahead-js/typeahead.bundle.min.js') }}"></script>
    <script src="{{ secure_url('vendor/formvalidation/formValidation.js') }}"></script>
    <script src="{{ secure_url('vendor/formvalidation/framework/bootstrap.js') }}"></script>
    <script src="{{ secure_url('js/Plugin/sweetalert.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="{{ secure_url('vendor/ace/ace.js') }}"></script>
    <script src="{{ secure_url('vendor/ace/mode-html.js') }}"></script>
    <script src="{{ secure_url('vendor/ace/worker-html.js') }}"></script>
    <script src="{{ secure_url('vendor/ace/theme-monokai.js') }}"></script>
    <script src="{{ secure_url('vendor/ace/theme-solarized_light.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var groups = "";
            var total_recipients = 0;
            $("[name=data_source]").iCheck();

            var defaults = Plugin.getDefaults("wizard");
            var options = $.extend(true, {}, defaults, {
                onInit: function () {
                },
                validator: function () {
                    return true;
                },
                templates: {
                    buttons: function () {
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
                onFinish: function () {
                    $('#new-deployment-form').submit();
                }
            });

            $('#new-deployment-form').wizard(options);

            $("#add-groups").click(function(ev) {
                ev.preventDefault();

                var sources = new Array();
                $.each($("[name='data_source[]']:checked"), function () {
                    sources.push($(this).val());
                });

                $.post(
                    "{{ secure_url('/campaign/' . $campaign->id . '/drops/add-groups') }}",
                    {
                        "contact_filter": $("#contact_filter option:selected").val(),
                        "group_using": $("#group_using option:selected").val(),
                        "max": $("#max_per_group").val(),
                        "total": total_recipients,
                        "group_count": groups.length,
                        "lists": $("[name=recipient_lists]").val(),
                        "sources": sources
                    },
                    function (data) {
                        if (data.code == 200) {
                            var table = $("#counts").html();
                            $("#added-groups").html(table);
                            $("#show_assign_recipients_form").html('<i class="icon fa-users"></i> Change Recipients');
                            $("#show_assign_recipients_form").removeClass('btn-primary').addClass('btn-info');
                            var groups_html = '<table class="table table-condensed table-bordered table-striped table-hover table-primary"><thead><th>Group</th><th>Recipients</th><th>Date</th><th>Time</th></thead>';
                            for (var i=0; i < groups.length; i++) {
                                groups_html += '<tr><th class="text-middle">'+groups[i].name+'</th>';
                                groups_html += '<td class="text-middle">'+groups[i].count+'</td>';
                                groups_html += '<td class="form-group text-middle" valign="center" align="center">' +
                                    '<label for="'+groups[i].name+'" class="form-control-label">' + groups[i].name + ' Date</label>' +
                                    '<input type="text" name="' + groups[i].name + '_date" class="form-control datepicker" data-plugin="datepicker">' +
                                    '</td>';
                                groups_html += '<td class="form-group text-middle" valign="center" align="center">' +
                                    '<label for="'+groups[i].name+'" class="form-control-label">' + groups[i].name + ' Time</label>' +
                                    '<input type="text" class="form-control ui-timepicker-input timepicker" name="' + groups[i].name + '_time" data-plugin="timepicker" autocomplete="off">' +
                                    '</td></tr>';
                            }
                            groups_html += '</table>';

                            $("#scheduling").html(groups_html);

                            $("#scheduling .datepicker").datepicker({
                                autoclose: true,
                                zIndexOffset: 2000
                            });
                            $("#scheduling .timepicker").timepicker({
                                autoclose: true,
                                'default': 'now',
                                zIndexOffset: 2000
                            });
                            $('#exampleTimeButton').on('click', function() {
                                $(this).closest('.timepicker').timepicker('setTime', new Date());
                            });
                            swal("Success!", "Your groups have been saved, but will expire unless you complete this form", "success");
                        } else {
                            swal("Error", data.message + "  Please try again and if it happens again <a href='#'>contact support</a>", "error");
                        }
                    },
                    'json'
                );
            });

            $("[name='data_source[]']").on('change', function() {
                var sources = new Array();
                $.each($("[name='data_source[]']:checked"), function () {
                    sources.push($(this).val());
                });
                recipient_search(
                    $("#contact_filter").val(),
                    $("#group_using").val(),
                    $("#max_per_group").val(),
                    $("#recipient_lists").val(),
                    sources
                );
            });

            /*
            $("#addRecipientsModal [name=recipient_lists]").on('change', function() {
                var sources = new Array();
                $.each($("[name='data_source[]']:checked"), function () {
                    sources.push($(this).val());
                });
                recipient_search(
                    $("#contact_filter").val(),
                    $("#group_using").val(),
                    $("#max_per_group").val(),
                    $("#recipient_lists").val(),
                    sources
                );
            });
            */

            $("#addRecipientsModal input[type=text]").change(function() {
                var sources = new Array();
                $.each($("[name='data_source[]']:checked"), function () {
                    sources.push($(this).val());
                });
                recipient_search(
                    $("#contact_filter").val(),
                    $("#group_using").val(),
                    $("#max_per_group").val(),
                    $("#recipient_lists").val(),
                    sources
                );
            });

            $("#addRecipientsModal select").change(function() {
                var sources = new Array();
                $.each($("[name='data_source[]']:checked"), function () {
                    sources.push($(this).val());
                });
                recipient_search(
                    $("#contact_filter").val(),
                    $("#group_using").val(),
                    $("#max_per_group").val(),
                    $("#recipient_lists").val(),
                    sources
                );
            });

            $("#show_assign_recipients_form").click(function() {
                recipient_search('all-sms', 'no-group', '', '', '');
                $("#recipients-search-form").each(function() {
                    this.reset();
                });
            });

            var recipient_search = function(contact, group, max, lists, source) {
                console.log(source);
                $.get("{{ secure_url('/campaign/' . $campaign->id . '/recipients/search') }}",
                    {
                        "contact": contact,
                        "group": group,
                        "max": max,
                        "lists": lists,
                        "data_source": source
                    },
                    function(data) {
                        if (data.total === undefined) {
                            alert('error');
                            return;
                        }
                        $("#recipient_count").text(data.total);
                        $("#group_count").text(data.groups.length);

                        console.log(data);
                        if (data.groups.length > 0) {
                            var rows = "";
                            for (var i=0; i<data.groups.length; i++) {
                                rows += "<tr><td>" + data.groups[i]['name'] + "</td><td>" + data.groups[i]['count'] + "</td></tr>";
                            }
                            $("#group-list tbody").html(rows);
                            groups = data.groups;
                            total_recipients = data.total;
                            console.log(data.total);
                        }
                    },
                    'json'
                );
            };

            var defaults = Plugin.getDefaults("wizard");
            var options = $.extend(true, {}, defaults, {
                onInit: function () {
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
                    $('#campaign-form').submit();
                },
                buttonsAppendTo: '.panel-body'
            });

            $('#campaign-form').wizard(options).data("wizard");

            var editor = ace.edit("editor");
            editor.setTheme("ace/theme/solarized_light");
            editor.getSession().setMode("ace/mode/html");

            editor.getSession().on('change', function (e) {
                $("input[name=email_html]").val(editor.getSession().getValue());
            });

            $('pre code').each(function (i, block) {
                hljs.highlightBlock(block);
            });

            $("#editor.ace_editor .ace_scroller .ace_content").on('change', function () {
                //alert('changed');
                $("input[name=email_html]").val(editor.getSession().getValue());
            });

            if ($("select[name=type] option:selected").val() == 'email') {
                $("#sms-fields").hide();
                $("#email-fields").show();
            } else if ($("select[name=type] option:selected").val() == 'sms') {
                $("#sms-fields").show();
                $("#email-fields").hide();
            }

            $("select[name=type]").change(function () {
                if ($("select[name=type] option:selected").val() == 'email') {
                    $("#sms-fields").hide();
                    $("#email-fields").show();
                } else if ($("select[name=type] option:selected").val() == 'sms') {
                    $("#sms-fields").show();
                    $("#email-fields").hide();
                }
            });

            $("#template-inputs select").change(function () {
                if ($("#template-inputs select option:selected").val() != 'none') {
                    replace_form_data_with_template($("#template-inputs select option:selected").val());
                } else {
                    $("#drop_type option[value=email]").attr("selected", "selected");
                    $("#drop_email_subject").val("");
                    $("#drop_email_text").val("");
                    $("#drop_email_html").val("");
                    editor.getSession().setValue("");

                    $("#drop_text_message").val("");
                    $("#drop_send_vehicle_image").val("");
                    $("#drop_text_message_image").val("");

                }
            });

            var replace_form_data_with_template = function (template_id) {
                var template = {};
                $.post(
                    "{{ secure_url('/template/') }}/" + template_id + "/json",
                    function (data) {
                        if (data.type == 'email') {
                            $("#drop_type").val(data.type);
                            $("#drop_type option[value=email]").attr("selected", "selected");
                        }

                        if (data.type == 'sms') {
                            $("#drop_type").val(data.type);
                            $("#drop_type option[value=sms]").attr("selected", "selected");
                        }

                        if (data.type == 'email' || data.type == 'legacy') {
                            $("#drop_email_subject").val(data.email_subject);
                            $("#drop_email_text").val(data.email_text);
                            $("#drop_email_html").val(data.email_html);
                            editor.getSession().setValue(data.email_html);

                        }

                        if (data.type == 'sms' || data.type == 'legacy') {
                            $("#drop_text_message").val(data.text_message);
                            $("#drop_send_vehicle_image").val(data.send_vehicle_image);
                            $("#drop_text_message_image").val(data.text_message_image);
                        }
                        /*
                         swal({
                         title: "Are you sure?",
                         text: "This will overwrite your current drop data!",
                         type: "warning",
                         showCancelButton: true,
                         confirmButtonColor: "#DD6B55",
                         confirmButtonText: "Yes",
                         cancelButtonText: "No",
                         showLoaderOnConfirm: true,
                         closeOnConfirm: false,
                         customClass: "deleteBox"
                         },
                         function () {
                         });
                         */
                    },
                    'json'
                );
            };
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {

        });
    </script>
@endsection

@section('scripts')
@endsection
