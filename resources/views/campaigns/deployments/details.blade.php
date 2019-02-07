@extends('layouts.remark_campaign')

@section('header')
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/highlight/hightlight.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/highlight/styles/default.css') }}">
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
    .panel-deployment>.panel-body {
        background: #f0f0f0;
    }
    .card {
        border: 1px solid #e0e0e0;
        box-shadow: 2px 2px 5px #ccc;
    }
    .card:hover {
        box-shadow: 0 0 0 #fff;
    }
    .card-block {
        border-top: 1px solid #e0e0e0;
        z-index: 100;
    }
    .ribbon {
        opacity: .75;
        z-index: 5000;
    }
@endsection

@section('campaign_content')
    @if ($drop->type == 'sms')
        <div class="row">
            <div class="col-md-4">
                @if (! $campaign->phones()->whereCallSoureName('sms')->count() == 0)
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="icon fa-exclamation-triangle"></i>
                            Cannot send SMS
                        </h3>
                    </div>
                    <div class="panel-body" style="padding-top: 8px;">
                        <p>This campaign does not have a phone number from which to send SMS messages!</p>
                        <a href="{{ secure_url('campaign/' . $campaign->id . '/edit') }}" class="btn btn-success">Click here to add one</a>

                    </div>
                </div>
                @else
                <div id="sms-handling" class="responsive-table">
                    @if ($recipients->count() > 0)
                    <div class="card text-center">
                        <div class="counter counter-lg">
                            <h1 id="to_send"></h1>
                        </div>
                        <div class="counter-label text-uppercase">
                            <p>Pending SMS Messages</p>
                        </div>
                    </div>
                    @endif
                    <div class="card text-center">
                        <div class="counter counter-lg">
                            <h1 id="sent"></h1>
                        </div>
                        <div class="counter-label text-uppercase">
                            <p>Sent SMS Messages</p>
                        </div>
                    </div>
                    <button id="sendSms" class="btn btn-primary form-control">Send an SMS message to <span class="targetName"></span></button>
                    <!--
                    <button id="skipSms" class="btn btn-warning"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> Skip <span class="targetName"></span></button>
                    -->
                </div>
                @endif
            </div>
            <div class="col-md-8">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title text-uppercase">{{ $drop->type }} Message</h3>
                    </div>
                    <div class="panel-body">
                        @if ($drop->type == 'sms')
                            <p class="form-control-static">{!! $drop->text_message !!}</p>
                            @if ($drop->send_vehicle_image && $drop->text_vehicle_image)
                            <img src="{{ $drop->text_vehicle_image }}">
                            @endif
                        @endif
                        @if ($drop->type == 'email')
                            <p class="form-control-static">{{ $drop->email_subject }}</p>
                            <p class="form-control-static">{{ $drop->email_text }}</p>
                            <pre><code class="html" style="width: 100%; overflow:wrap;">{{ $drop->email_html }}</code></pre>
                        @endif
                    </div>
                    <div class="panel-footer" style="background-color: #f3f3f3">
                        <p style="padding-top: 10px;">Scheduled to drop at {{ $drop->send_at->format("m/d/Y g:i A") }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scriptTags')
    <script src="{{ secure_url('js/Plugin/material.js') }}"></script>
    <script src="{{ secure_url('js/Plugin/highlight.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('pre code').each(function(i, block) {
                hljs.highlightBlock(block);
            });

            @if ($drop->type == 'email')
            $("#sms-fields").hide();
            @endif
            @if ($drop->type == 'sms')
            $("#email-fields").hide();
            @endif

            $('pre code').each(function(i, block) {
                hljs.highlightBlock(block);
            });
        });
    </script>
    @if ($drop->type == 'sms')
    <script>
        $(document).ready(function() {
            var send = {!! $recipients->toJson() !!};
            var sent_count = {!! $sentRecipients !!};

            $("#to_send").text(send.length);
            $("#sent").text(sent_count);

            if (send.length > 0) {
                $(".targetName").text(send[0].first_name + ' ' + send[0].last_name);

/*
                $(document).keydown(function(e) {
                    switch(e.which) {
                        case 37: // left
                            $("#sendSms").click();
                            break;

                        case 38: // up
                            $("#sendSms").click();
                            break;

                        case 39: // right
                            $("#sendSms").click();
                            break;

                        case 40: // down
                            $("#sendSms").click();
                            break;

                        default: return; // exit this handler for other keys
                    }
                    e.preventDefault(); // prevent the default action (scroll / move caret)
                });
*/
            } else {
                $("#to_send").closest('div').hide();
                $("#sendSms").hide();
                $("#skipSms").hide();
            }

            $("#sendSms").click(function() {
                $(document).unbind('keydown');

                if (send.length > 0) {
                    var sending = send.shift();
                    $.post(
                        "{{ secure_url('campaign/' . $campaign->id . '/drop/' . $drop->id . '/send-sms/') }}" + '/' + sending.target_id,
                        function(data) {
                            if (data.success !== undefined && data.success === 1)
                            {
                                console.log("sms sent to " + sending.first_name + " " + sending.last_name);
                                sent_count++;

                                $(".targetName").text(sending.first_name + ' ' + sending.last_name);
                                console.log("sms sent - " + sending.target_id);

                                $("#to_send").text(send.length);
                                $("#sent").text(sent_count);

                                if (send.length > 0) {
/*
                                    $(document).keydown(function(e) {
                                        switch(e.which) {
                                            case 37: // left
                                                $("#sendSms").click();
                                                break;

                                            case 38: // up
                                                $("#sendSms").click();
                                                break;

                                            case 39: // right
                                                $("#sendSms").click();
                                                break;

                                            case 40: // down
                                                $("#sendSms").click();
                                                break;

                                            default: return; // exit this handler for other keys
                                        }
                                        e.preventDefault(); // prevent the default action (scroll / move caret)
                                    });
*/
                                } else {
                                    $("#sendSms").hide();
                                    $("#skipSms").hide();
                                }
                            }
                        }, 'json');
                } else {
                    $("#sendSms").hide();
                    $("#skipSms").hide();
                    $("#to_send").closest('div').hide();
                    $("#sendSms").attr("disabled", "disabled");
                }
            });
        });
    </script>
    @endif
@endsection
