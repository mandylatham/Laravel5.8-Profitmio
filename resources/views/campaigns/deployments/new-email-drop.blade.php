@extends('layouts.remark')

@section('header')
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
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
                    <form id="recipients-search-form" class="form" action="{{ secure_url('/campaign/' . $campaign->id . '/drops/add-groups') }}" method="post">
                        <h4 class="" id="addRecipientsModalLabel">Add Recipients</h4>
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
@endsection

@section('scriptTags')
@endsection

@section('scripts')
@endsection
