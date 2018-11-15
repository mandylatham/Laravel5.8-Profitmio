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
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/ace/ace.min.css') }}">
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
        s {
            text-decoration: line-through;
        }
        #editor {
            min-height: 500px;
        }
        .btn.dropdown-toggle.btn-default {
            margin-top: 8px;
        }
    </style>
@endsection

@section('manualStyle')
@endsection

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-md-12">
                    <div style="display: flex">
                        <button type="button"
                                role="button"
                                data-url="{{ secure_url('/templates') }}"
                                class="btn btn-sm btn-default waves-effect campaign-edit-button"
                                data-toggle="tooltip"
                                data-original-title="Go Back"
                                style="margin-right: 15px; background: rgba(255, 255, 255, 0.1); border: 0;">
                            <i class="icon fa-angle-left" style="color: #efefef" aria-hidden="true"></i>
                        </button>
                        <h3 class="page-title text-default">
                            {{ $template->name }}
                        </h3>
                    </div>
                    <div class="page-header-actions">
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="row-fluid" data-plugin="matchHeight" data-by-row="true">
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-body" data-fv-live="enabled">
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
                            <form class="form" method="post" action="{{ secure_url('/template/' . $template->id . '/update') }}">
                                {{ csrf_field() }}
                                <h4>Template Details</h4>
                                <div class="form-group">
                                    <label for="name" class="floating-label">Title</label>
                                    <input type="text"
                                           class="form-control empty"
                                           name="name"
                                           placeholder="Template Title"
                                           value="{{ old('name') ?: $template->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="type" class="floating-label">Template Type</label>
                                    <select name="type" class="form-control" data-fv-field="type" required>
                                        <option value='email' {{ old('type') ?: $template->type == 'email' ? 'selected' : '' }}>Email</optioni>
                                        <option value='sms' {{ old('type') ?: $template->type == 'sms' ? 'selected' : '' }}>SMS</option>
                                        <option disabled><s>Voice</s></option>
                                    </select>
                                </div>
                                <div id="email-fields">
                                    <div class="form-group">
                                        <label for="email_subject" class="floating-label">Email Subject</label>
                                        <input type="text"
                                               class="form-control {{ ! empty(old('email_subject') ?: $template->email_subject) ?: 'empty' }}"
                                               name="email_subject"
                                               placeholder="Email Subject"
                                               autocomplete="off"
                                               data-fv-field="email_subject"
                                               value="{{ old('email_subject') ?: $template->email_subject }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="email_text" class="floating-label">Email Text</label>
                                        <textarea
                                                class="form-control empty"
                                                name="email_text"
                                                placeholder="Email Plain Text"
                                                autocomplete="off">{{ old('email_text') ?: $template->email_text }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="email_html" class="floating-label">Email HTML</label>
                                        <div id="editor">{{ old('email_html') ?: $template->email_html }}</div>
                                        <input type="hidden" name="email_html" value="{{ old('email_html') ?: $template->email_html }}">
                                    </div>
                                </div>
                                <div id="sms-fields">
                                    <div class="form-group">
                                        <label for="text_message" class="floating-label">Text Message</label>
                                        <textarea
                                                class="form-control empty"
                                                name="text_message"
                                                placeholder="Text Message"
                                                autocomplete="off">{{ old('text_message') ?: $template->text_message }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="send_vehicle_image" {{ empty(old('send_vehicle_image') ?: $template->send_vehicle_image) ?: 'checked="checked"' }}>
                                                Send Vehicle Image
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="text_message_image" class="floating-label">Vehicle Image</label>
                                        <input type="text" name="text_message_image" class="form-control" placeholder="Vehicle Image Location" value="{{ old('text_message_image') ?: $template->text_message_image }}">
                                    </div>
                                </div>
                                <button id="save-template-button" class="btn btn-success">Save Changes</button>
                            </form>
                        </div>
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

    <script src="{{ secure_url('js/Plugin/formatter.min.js') }}"></script>
    <script src="{{ secure_url('vendor/formatter/jquery.formatter.min.js') }}"></script>
    <script src="{{ secure_url('vendor/formatter/formatter.min.js') }}"></script>

    <script src="{{ secure_url('vendor/ace/ace.js') }}"></script>
    <script src="{{ secure_url('vendor/ace/mode-html.js') }}"></script>
    <script src="{{ secure_url('vendor/ace/worker-html.js') }}"></script>
    <script src="{{ secure_url('vendor/ace/theme-monokai.js') }}"></script>
    <script src="{{ secure_url('vendor/ace/theme-solarized_light.js') }}"></script>

    <script type="text/javascript">


        $(document).ready(function() {
            var editor = ace.edit("editor");
            editor.setTheme("ace/theme/solarized_light");
            editor.getSession().setMode("ace/mode/html");

            editor.getSession().on('change', function(e) {
                $("input[name=email_html]").val(editor.getSession().getValue());
            });

            $('pre code').each(function(i, block) {
                hljs.highlightBlock(block);
            });

            $("#editor.ace_editor .ace_scroller .ace_content").on('change', function () {
                //alert('changed');
                $("input[name=email_html]").val(editor.getSession().getValue());
            });

            if ( $("select[name=type] option:selected").val() == 'email') {
                $("#sms-fields").hide();
                $("#email-fields").show();
            } else if ( $("select[name=type] option:selected").val() == 'sms') {
                $("#sms-fields").show();
                $("#email-fields").hide();
            }

            $("select[name=type]").change(function() {
                if ( $("select[name=type] option:selected").val() == 'email') {
                    $("#sms-fields").hide();
                    $("#email-fields").show();
                } else if ( $("select[name=type] option:selected").val() == 'sms') {
                    $("#sms-fields").show();
                    $("#email-fields").hide();
                }
            });

            $("#save-template-button").click(function() {
                $("#save-template-button").attr('disabled', 'disabled').addClass('disabled');

                $(this).parent().closest('form').submit();
            });
        });
    </script>
@endsection
