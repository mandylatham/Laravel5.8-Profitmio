@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/deployments-edit.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.drop = @json($drop);
        window.dropsIndexUrl = @json(route('campaigns.drops.index', ['campaign' => $campaign->id]));
        window.updateDropUrl = @json(route('campaigns.drops.update', ['campaign' => $campaign->id, 'drop' => $drop->id]));
    </script>
    <script src="{{ asset('js/deployments-edit.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="deployments-edit" v-cloak>
        <div class="edit-container">
            <h4 class="mb-4">Drop Details</h4>
            <form @submit.prevent="save">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label for="type">Drop Type</label>
                            <select id="type" name="type" class="form-control" required v-model="drop.type">
                                <option value="email">Email</option>
                                <option value="sms">SMS</option>
                                <option disabled><s>Voice</s></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="form-group">
                            <label for="type">Send Date</label>
                            <date-picker v-model="drop.send_at_date" lang="en" type="date" format="MM/DD/YYYY" placeholder=""></date-picker>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="form-group">
                            <label for="type">Send Time</label>
                            <date-picker v-model="drop.send_at_time" lang="en" type="time" format="HH:mm" placeholder=""></date-picker>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" v-if="drop.type === 'email'">
                        <div class="form-group">
                            <label for="email_subject">Email Subject</label>
                            <input id="email_subject" type="text" class="form-control" name="email_subject"
                                   placeholder="Email Subject" v-model="drop.email_subject"
                                   autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="email_text">Email Text</label>
                            <textarea id="email_text" class="form-control" name="email_text" v-model="drop.email_text"
                                      placeholder="Email Plain Text" autocomplete="off"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="email_html">Email HTML</label>
                            <editor v-model="drop.email_html" lang="html" height="800" @init="initEditor"></editor>
                        </div>
                    </div>
                    <div class="col-12" v-if="drop.type === 'sms'">
                        <div class="form-group">
                            <label for="text_message">Text Message</label>
                            <textarea class="form-control" name="text_message" placeholder="Text Message"
                                      autocomplete="off" v-model="drop.text_message"></textarea>
                        </div>
                        <div class="form-group">
                            <p-check color="primary" class="p-default d-block mb-3" name="send_vehicle_image" v-model="drop.send_vehicle_image">Send Vehicle Image</p-check>
                        </div>
                        <div class="form-group" v-if="drop.send_vehicle_image">
                            <label for="text_message_image">Vehicle Image</label>
                            <input type="text" name="text_message_image" class="form-control" placeholder="Vehicle Image Location" v-model="drop.text_message_image">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn pm-btn pm-btn-purple mt-3" :disabled="loading">
                    <span v-if="!loading">Save</span>
                    <spinner-icon :size="'sm'" class="white" v-if="loading"></spinner-icon>
                </button>
            </form>
        </div>
    </div>
@endsection
{{--@extends('layouts.remark_campaign')--}}

{{--@section('header')--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/jquery-wizard/jquery-wizard.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/icheck/icheck.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/typeahead-js/typeahead.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-tokenfield/bootstrap-tokenfield.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-datepicker/bootstrap-datepicker.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/clockpicker/clockpicker.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/formvalidation/formValidation.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('css/sweetalert.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/ace/ace.min.css') }}">--}}
    {{--<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />--}}
    {{--<style type="text/css">--}}
        {{--#campaigns td {--}}
            {{--cursor: pointer;--}}
        {{--}--}}
        {{--#campaigns > tbody > tr > td > h5 {--}}
            {{--margin-bottom: -20px;--}}
        {{--}--}}
        {{--.round-button {--}}
            {{--border-radius: 40px;--}}
        {{--}--}}
        {{--.btn-circle {--}}
            {{--width: 30px;--}}
            {{--height: 30px;--}}
            {{--text-align: center;--}}
            {{--padding: 6px 0;--}}
            {{--font-size: 12px;--}}
            {{--line-height: 1.428571429;--}}
            {{--border-radius: 15px;--}}
        {{--}--}}
        {{--s {--}}
            {{--text-decoration: line-through;--}}
        {{--}--}}
        {{--#editor {--}}
            {{--min-height: 500px;--}}
        {{--}--}}
        {{--.btn.dropdown-toggle.btn-default {--}}
            {{--margin-top: 8px;--}}
        {{--}--}}
    {{--</style>--}}
{{--@endsection--}}

{{--@section('manualStyle')--}}
    {{--.panel-deployment>.panel-body {--}}
        {{--background: #f0f0f0;--}}
    {{--}--}}
    {{--.card {--}}
        {{--border: 1px solid #e0e0e0;--}}
        {{--box-shadow: 2px 2px 5px #ccc;--}}
    {{--}--}}
    {{--.card:hover {--}}
        {{--box-shadow: 0 0 0 #fff;--}}
    {{--}--}}
    {{--.card-block {--}}
        {{--border-top: 1px solid #e0e0e0;--}}
        {{--z-index: 100;--}}
    {{--}--}}
    {{--.ribbon {--}}
        {{--opacity: .75;--}}
        {{--z-index: 5000;--}}
    {{--}--}}
{{--@endsection--}}

{{--@section('campaign_content')--}}
    {{--<form class="form"--}}
          {{--method="post"--}}
          {{--action="{{ secure_url('/campaign/' . $campaign->id . '/drop/' . $drop->id . '/update') }}">--}}
        {{--{{ csrf_field() }}--}}
        {{--<h4>Drop Details</h4>--}}
        {{--<div class="row">--}}
            {{--<div class="form-group col-md-6">--}}
                {{--<label for="type" class="floating-label">Drop Type</label>--}}
                {{--<select id="drop_type" name="type" class="form-control" data-fv-field="type" required>--}}
                    {{--<option value='email' {{ old('type') ?: $drop->type == 'email' ? 'selected' : '' }}>Email</option>--}}
                    {{--<option value='sms' {{ old('type') ?: $drop->type == 'sms' ? 'selected' : '' }}>SMS</option>--}}
                    {{--<option disabled><s>Voice</s></option>--}}
                {{--</select>--}}
            {{--</div>--}}
            {{--<div class="form-group col-md-3">--}}
                {{--<label for="send_at_date" class="floating-label">Send Date</label>--}}
                {{--<input type="text"--}}
                       {{--name="send_at_date"--}}
                       {{--class="form-control datepicker"--}}
                       {{--value="{{ old('send_at_date') ? show_date(old('send_at_date')) : (new \Carbon\Carbon(show_date($drop->send_at)))->format('m/d/Y') }}">--}}
            {{--</div>--}}
            {{--<div class="form-group col-md-3">--}}
                {{--<label for="send_at_time" class="floating-label">Send Time</label>--}}
                {{--<input type="text"--}}
                       {{--name="send_at_time"--}}
                       {{--class="form-control timepicker"--}}
                       {{--value="{{ old('send_at_time') ? show_date(old('send_at_time')) : (new \Carbon\Carbon(show_date($drop->send_at)))->format('g:i A') }}">--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div id="email-fields">--}}
            {{--<div class="form-group">--}}
                {{--<label for="email_subject" class="floating-label">Email Subject</label>--}}
                {{--<input type="text"--}}
                       {{--class="form-control {{ ! empty(old('email_subject') ?: $drop->email_subject) ?: 'empty' }}"--}}
                       {{--name="email_subject"--}}
                       {{--placeholder="Email Subject"--}}
                       {{--autocomplete="off"--}}
                       {{--data-fv-field="email_subject"--}}
                       {{--value="{{ old('email_subject') ?: $drop->email_subject }}">--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--<label for="email_text" class="floating-label">Email Text</label>--}}
                {{--<textarea--}}
                        {{--class="form-control empty"--}}
                        {{--name="email_text"--}}
                        {{--placeholder="Email Plain Text"--}}
                        {{--autocomplete="off">{{ old('email_text') ?: $drop->email_text }}</textarea>--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--<label for="email_html" class="floating-label">Email HTML</label>--}}
                {{--<div id="editor">{{ old('email_html') ?: $drop->email_html }}</div>--}}
                {{--<input type="hidden" name="email_html" value="{{ old('email_html') ?: $drop->email_html }}">--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div id="sms-fields">--}}
            {{--<div class="form-group">--}}
                {{--<label for="text_message" class="floating-label">Text Message</label>--}}
                {{--<textarea--}}
                        {{--class="form-control empty"--}}
                        {{--name="text_message"--}}
                        {{--placeholder="Text Message"--}}
                        {{--autocomplete="off">{{ old('text_message') ?: $drop->text_message }}</textarea>--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--<div class="checkbox">--}}
                    {{--<label>--}}
                        {{--<input type="checkbox" name="send_vehicle_image" {{ empty(old('send_vehicle_image') ?: $drop->send_vehicle_image) ?: 'checked="checked"' }}>--}}
                        {{--Send Vehicle Image--}}
                    {{--</label>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--<label for="text_message_image" class="floating-label">Vehicle Image</label>--}}
                {{--<input type="text" name="text_message_image" class="form-control" placeholder="Vehicle Image Location" value="{{ old('text_message_image') ?: $drop->text_message_image }}">--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<button class="btn btn-success">Save Changes</button>--}}
    {{--</form>--}}
{{--@endsection--}}

{{--@section('scriptTags')--}}
    {{--<script src="{{ secure_url('js/Plugin/material.js') }}"></script>--}}
    {{--<script src="{{ secure_url('js/Plugin/jquery-wizard.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/jquery-wizard/jquery-wizard.js') }}"></script>--}}

    {{--<script src="{{ secure_url('js/Plugin/icheck.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/icheck/icheck.js') }}"></script>--}}

    {{--<script src="{{ secure_url('js/Plugin/bootstrap-tokenfield.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/bootstrap-tokenfield/bootstrap-tokenfield.js') }}"></script>--}}

    {{--<script src="{{ secure_url('js/Plugin/bootstrap-datepicker.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/timepicker/jquery.timepicker.js') }}"></script>--}}

    {{--<script src="{{ secure_url('vendor/typeahead-js/typeahead.bundle.min.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/formvalidation/formValidation.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/formvalidation/framework/bootstrap.js') }}"></script>--}}
    {{--<script src="{{ secure_url('js/Plugin/sweetalert.min.js') }}"></script>--}}

    {{--<script src="{{ secure_url('js/Plugin/formatter.min.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/formatter/jquery.formatter.min.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/formatter/formatter.min.js') }}"></script>--}}

    {{--<script src="{{ secure_url('vendor/ace/ace.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/ace/mode-html.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/ace/worker-html.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/ace/theme-monokai.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/ace/theme-solarized_light.js') }}"></script>--}}

    {{--<script type="text/javascript">--}}
        {{--$(document).ready(function() {--}}
            {{--var editor = ace.edit("editor");--}}
            {{--editor.setTheme("ace/theme/solarized_light");--}}
            {{--editor.getSession().setMode("ace/mode/html");--}}

            {{--editor.getSession().on('change', function(e) {--}}
                {{--$("input[name=email_html]").val(editor.getSession().getValue());--}}
            {{--});--}}

            {{--$('pre code').each(function(i, block) {--}}
                {{--hljs.highlightBlock(block);--}}
            {{--});--}}

            {{--$("#editor.ace_editor .ace_scroller .ace_content").on('change', function () {--}}
                {{--//alert('changed');--}}
                {{--$("input[name=email_html]").val(editor.getSession().getValue());--}}
            {{--});--}}

            {{--if ( $("select[name=type] option:selected").val() == 'email') {--}}
                {{--$("#sms-fields").hide();--}}
                {{--$("#email-fields").show();--}}
            {{--} else if ( $("select[name=type] option:selected").val() == 'sms') {--}}
                {{--$("#sms-fields").show();--}}
                {{--$("#email-fields").hide();--}}
            {{--}--}}

            {{--$("select[name=type]").change(function() {--}}
                {{--if ( $("select[name=type] option:selected").val() == 'email') {--}}
                    {{--$("#sms-fields").hide();--}}
                    {{--$("#email-fields").show();--}}
                {{--} else if ( $("select[name=type] option:selected").val() == 'sms') {--}}
                    {{--$("#sms-fields").show();--}}
                    {{--$("#email-fields").hide();--}}
                {{--}--}}
            {{--});--}}

            {{--$(".datepicker").datepicker({--}}
                {{--'autoclose': true--}}
            {{--});--}}
            {{--$(".timepicker").timepicker({--}}
                {{--'step': 5,--}}
                {{--'useSelect': true,--}}
                {{--'className': 'form-control'--}}
            {{--});--}}

        {{--});--}}
    {{--</script>--}}
{{--@endsection--}}
