<script src="{{ secure_url('js/Plugin/sweetalert.min.js') }}"></script>
<link type="text/css" rel="stylesheet" href="{{ secure_url('css/sweetalert.css') }}">

<style type="text/css">
    .ui-timepicker-wrapper {
        z-index: 5000;
    }
</style>
<header class="slidePanel-header">
    <button type="button" class="btn btn-icon btn-inverse slidePanel-close actions-top icon md-close"
            aria-hidden="true"></button>
</header>
<div class="slidePanel-inner">
    <section class="slidePanel-inner-section">
        <div id="recipient-data"
             data-name="{{ $recipient->name }}"
             data-id="{{ $recipient->id }}"></div>
        <div class="mail-header">
            <div class="mail-header-main">
                <div>
                    <div class="name text-primary"><strong>{{ $recipient->name }}</strong>
                        <small><em>{{ $recipient->city . ', ' . $recipient->state }}</em></small>
                    </div>
                    <div class="">
                        <i class="icon fa-car"></i>
                        {{ $recipient->vehicle }}
                    </div>
                    @if ($recipient->email)
                        <div class="">
                            <i class="icon fa-envelope"></i>
                            {{ str_replace('@', '&#64;', strtolower($recipient->email)) }}
                        </div>
                    @endif
                    @if ($recipient->phone)
                        <div class="">
                            <i class="icon fa-phone"></i>
                            {{ $recipient->phone }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="mail-header-right">
                <div class="dropdown">
                    <button type="button" class="btn btn-pure waves-effect" data-toggle="dropdown" aria-expanded="false">
                        Add Label
                        <span class="icon md-chevron-down" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right animation-scale-up animation-top-right animation-duration-250" role="menu" data-postback="{{ secure_url('recipient/' . $recipient->id . '/add-label') }}">
                        <button class="dropdown-item add-label" data-label="interested">
                            <i class="md-circle green-600" aria-hidden="true"></i>
                            Interested
                        </button>
                        @if($recipient->campaign->service_dept && !$recipient->service)
                            <button class="dropdown-item add-label" data-label="service" id="departmentAlert" style="display: none">
                                <i class="md-circle green-600" aria-hidden="true"></i>
                                Service Dept
                            </button>
                            <button class="dropdown-item add-label" onclick="serviceDepartmentAlert(event, this)">
                                <i class="md-circle green-600" aria-hidden="true"></i>
                                Service Dept
                            </button>
                        @else
                            <button class="dropdown-item add-label" data-label="service">
                                <i class="md-circle green-600" aria-hidden="true"></i>
                                Service Dept
                            </button>
                        @endif
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item add-label" data-label="not_interested">
                            <i class="md-circle red-600" aria-hidden="true"></i>
                            Not Interested
                        </button>
                        <button class="dropdown-item add-label" data-label="wrong_number">
                            <i class="md-circle red-600" aria-hidden="true"></i>
                            Wrong Number
                        </button>
                        <button class="dropdown-item add-label" data-label="car_sold">
                            <i class="md-circle red-600" aria-hidden="true"></i>
                            Car Sold
                        </button>
                        <button class="dropdown-item add-label" data-label="heat">
                            <i class="md-circle red-600" aria-hidden="true"></i>
                            Heat Case
                        </button>
                    </div>
                </div>
                <div class="labels">
                    @if ($recipient->interested)
                        <span class="badge badge-success">
                        Interested
                        <button class="btn btn-pure btn-xs icon fa-close remove-label" data-postback="{{ secure_url('recipient/' . $recipient->id . '/remove-label') }}" data-label="interested"></button>
                    </span>
                    @endif
                    @if ($recipient->appointment)
                        <span class="badge badge-success">
                    Appointment
                </span>
                    @endif
                    @if ($recipient->not_interested)
                        <span class="badge badge-danger">
                        Not Interested
                        <button class="btn btn-pure btn-xs waves-effect icon fa-close remove-label" data-postback="{{ secure_url('recipient/' . $recipient->id . '/remove-label') }}" data-label="not_interested"></button>
                    </span>
                    @endif
                    @if ($recipient->wrong_number)
                        <span class="badge badge-danger">
                        Wrong Number
                        <button class="btn btn-pure btn-xs waves-effect icon fa-close remove-label" data-postback="{{ secure_url('recipient/' . $recipient->id . '/remove-label') }}" data-label="wrong_number"></button>
                    </span>
                    @endif
                    @if ($recipient->service)
                        <span class="badge badge-success">
                        Service Department
                    </span>
                    @endif
                    @if ($recipient->heat)
                        <span class="badge badge-danger">
                        Heat Case
                        <button class="btn btn-pure btn-xs waves-effect icon fa-close remove-label" data-postback="{{ secure_url('recipient/' . $recipient->id . '/remove-label') }}" data-label="heat"></button>
                    </span>
                    @endif
                    @if ($recipient->car_sold)
                        <span class="badge badge-danger">
                        Car Sold
                        <button class="btn btn-pure btn-xs waves-effect icon fa-close remove-label" data-postback="{{ secure_url('recipient/' . $recipient->id . '/remove-label') }}" data-label="car_sold"></button>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="mail-content">
            <div class="form-group">
                <textarea class="form-control" placeholder="Notes..." data-postback="{{ secure_url('recipient/' . $recipient->id . '/update-notes') }}" name="notes">{{ $recipient->notes }}</textarea>
            </div>
        </div>
        @if ($appointments->count() > 0)
            <div class="mail-attachments">
                <h4>Appointments</h4>
                <ul class="list-group">
                    @foreach ($appointments as $appointment)
                        <li class="list-group-item">
                            @if ($appointment->type == 'callback')
                                <i class="icon fa-phone"></i>
                                {{ ucwords(strtolower($appointment->first_name)) }} {{ ucwords(strtolower($appointment->last_name)) }} @
                                {{ $appointment->phone_number }}
                                <div class="checkbox" style="padding-top: 6px; margin-left: 8px;">
                                    <label>
                                        <input type="checkbox"
                                               data-appointment_id="{{ $appointment->id }}"
                                               data-phone="{{ $appointment->phone_number }}"
                                               data-postback='{{ secure_url("appointment/{$appointment->id}/update-called-status") }}'
                                               class="toggle_called"
                                                {{ $appointment->called_back ? 'checked="checked"' : '' }}>
                                        Called
                                    </label>
                                </div>
                            @else
                                <i class="icon fa-calendar"></i>
                                {{ $appointment->appointment_at->timezone(\Auth::user()->timezone)->format("m/d/Y @ g:i A T") }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div id="new-appointment" class="mail-attachments col-md-10">
                <button id="add-appointment-form-button" class="btn btn-micro btn-primary mb-10">Add Appointment</button>
                <div id="add-appointment-form" style="padding:25px; border:1px solid #e0e0e0;">
                    <h4>Add Appointment</h4>
                    <form id="add-appointment-form">
                        <div class="form-group">
                            <label for="appointment_date" class="form-check-label">Select Appiontment Date</label>
                            <input type="text" class="form-control datepicker" name="appointment_date" placeholder="Appt Date" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="appointment_time" class="form-check-label">Select Appointment Time</label>
                            <select name="appointment_time" class="form-control">
                                <option selected="selected">Appt Time</option>
                                {!! get_times('', '+15 minutes') !!}
                            </select>
                        </div>
                        <button id="add-appointment" class="btn btn-primary" role="button">Add Appointment</button>
                    </form>
                </div>
            </div>
        @endif
        @if ($threads->get('phone')->count() > 0)
            <div class="mail-attachments">
                <h4>Calls</h4>
                <ul class="list-group">
                    @foreach ($threads->get('phone') as $phone)
                        <li class="list-group-item">
                            <i class="icon fa-phone"></i>
                            Called at {{ show_date($phone->created_at) }}
                            @if (\Auth::user()->access == 'Admin')
                                @if (strlen($phone->recording_uri) > 0)
                                    <div class="audio-player"
                                         style="background-color: #f0f0f0;
                                padding: 15px;
                                border: 1px solid #ddd;
                                width: 100%;
                                border-radius: 10px;
                                margin: 8px;">
                                        <audio controls preload="none" style="width:100%;">
                                            <source src="https://api.twilio.com/{{ $phone->recording_uri }}" type="audio/mpeg">
                                        </audio>
                                    </div>
                                @else
                                 (No recording for this call)
                                @endif
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        <style type="text/css">
            .messaging-panel > .panel-body {
                padding: 12px;
                background-color: #ffffff;
                display: flex;
                flex-direction: column;
            }
            .message-wrapper {
                padding: 6px;
                width: 100%;
            }
            .message-time {
                font-family: 'Operator Mono', 'Source Sans Pro', Menlo, Monaco, Consolas, Courier New, monospace;
                font-size: 10px;
                letter-spacing: .2em;
                color: #4d4d4d;
                margin: 0 8px;
            }
            .inbound-message {
                text-align: left;
                align-self: flex-start;
            }
            .inbound-message > .message {
                background-color: #eee;
                border: 0.5px solid #fff;
                color: #000;
            }
            .unread {
                background-color: #77e02a;
                border: 0.5px solid #a5df81;
            }
            .outbound-message {
                text-align: right;
                align-self: flex-end;
            }
            .outbound-message > .message {
                background-color: #0b96e5;
                border: 1px solid #9cc2ff;
                color: #fff;
            }
            .message {
                padding: 12px 20px;
                border-radius: 8px;
                width: 100%;
                text-align: left;
            }
            .checkbox {
                color: #333;
                margin-left: 8px;
            }
            .original-message{
                background-color: #f8e7aa;
                border: 0.5px solid #d2e8b9;
                color: #000;
            }
            #email-message-toggle {
                margin-top: -25px;
                margin-left: auto;
                margin-right: auto;
            }
            #email-message-toggle > i {
                font-size: 24px;
            }
            #email-message {
                width: 100%;
                background: #fff;
            }
        </style>
        @if ($threads->get('text')->count() > 0)
        <!-- start of chat -->
            <div class="panel panel-primary messaging-panel sms-messages">
                <div class="panel-heading">
                    <h3 class="panel-title">SMS Messaging</h3>
                </div>
                <di class="panel-body">
                    @if ($textDrop)
                        <strong class="vertical-text">Original Message</strong>
                        <div class="message-time" style="margin-left: 25px">{{ $textDrop->send_at->timezone(\Auth::user()->timezone)->format('Y-m-d g:i A T') }}</div>
                        <p class="message original-message">
                            {{ $textDrop->text_message }}
                        </p>
                    @endif
                    <div class="sms-message-container">
                        @foreach ($threads->get('text') as $text)
                            <div class="message-wrapper {{ $text->incoming ? 'inbound-message' : 'outbound-message' }}">
                                @if ($text->created_at instanceof \Carbon\Carbon)
                                    <div class="message-time">{{ $text->created_at->timezone(\Auth::user()->timezone)->format('Y-m-d g:i A T') }} ({{ $text->created_at->timezone(\Auth::user()->timezone)->diffForHumans() }})</div>
                                @else
                                    <div class="message-time"><span class="text-danger">UNKNOWN RECEIVE DATE</span></div>
                                @endif
                                <div class="message unread">{{ str_replace('@', '&#64;', $text->message) }}</div>
                                @if ($text->incoming)
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"
                                                   class="message-read"
                                                   data-postback='{{ secure_url("response/{$text->id}/update-read-status") }}'
                                                   data-response_id="{{ $text->id }}"
                                                   data-response_time="{{ $text->created_at->format('m/d/Y g:i A') }}"
                                                    {{ $text->read ? 'checked="checked"' : '' }}>
                                            Read
                                        </label>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if (!$campaign->isExpired())
                        <form id="sms-form"
                              style="margin-top: 20px;">
                            {{ csrf_field() }}
                            <div class="input-group">
                                <input type="text" id="sms-message" class="form-control message-field" name="message" placeholder="Type your message...">
                                <span class="input-group-btn">
                            <button type="button" data-postback="{{ secure_url('campaign/' . $campaign->id . '/text-response/' . $recipient->id) }}" class="btn btn-primary waves-effect send-sms">
                                <i class="icon md-mail-send" aria-hidden="true"></i>
                            </button>
                        </span>
                            </div>
                        </form>
                    @endif
                </di>
            </div>
        @endif
        @if ($threads->get('email')->count() > 0)
            <div class="panel panel-primary messaging-panel email-messages">
                <div class="panel-heading">
                    <h3 class="panel-title">Email Messaging</h3>
                </div>
                <div class="panel-body">
                    @if ($emailDrop)
                        <div class="message-time" style="margin-left: 25px">{{ $emailDrop->send_at->timezone(\Auth::user()->timezone)->format('Y-m-d g:i A T') }}</div>
                        <div class="message original-message">Original Message <iframe id="email-message" class="email-original"></iframe></div>
                        <a href="#" id="email-message-toggle"><i class="icon fa fa-2x fa-chevron-circle-down"></i></a>
                    @endif
                    <div class="email-message-container">
                        @foreach ($threads->get('email') as $email)
                            <div class="message-wrapper {{ $email->incoming ? 'inbound-message' : 'outbound-message' }}">
                                <div class="message-time">{{ $email->created_at->timezone(\Auth::user()->timezone)->format('m/d/Y g:i A') }}</div>
                                <div class="message unread">{{ str_replace('@', '&#64;', $email->message) }}</div>
                                @if ($email->incoming)
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"
                                                   class="message-read"
                                                   data-postback='{{ secure_url("response/{$email->id}/update-read-status") }}'
                                                   data-response_id="{{ $email->id }}"
                                                   data-response_time="{{ $email->created_at->format('m/d/Y g:i A') }}"
                                                    {{ $email->read ? 'checked="checked"' : '' }}>
                                            Read
                                        </label>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if (!$campaign->isExpired())
                        <form id="email-form"
                              style="margin-top: 20px;">
                            {{ csrf_field() }}
                            <div class="input-group">
                                <input type="text" id="email-message" class="form-control message-field" name="message" placeholder="Type your message...">
                                <span class="input-group-btn">
                            <button type="button" data-postback="{{ secure_url('campaign/' . $campaign->id . '/email-response/' . $recipient->id) }}" class="btn btn-primary waves-effect send-email">
                                <i class="icon md-mail-send" aria-hidden="true"></i>
                            </button>
                        </span>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </section>
</div>
<script type="text/html" id="appointment-template">
    <h4>Appointments</h4>
    <i class="icon fa-calendar"></i> __DATE__
</script>
<script type="text/javascript">
    function serviceDepartmentAlert(event, object)
    {
        //event.preventDefault();
        event.stopPropagation();
        swal({title: "Are you sure you want to do this?", showCancelButton: true, allowOutsideClick: true}, function() {
            $('#departmentAlert').click();
        });
    }
    $(document).ready(function() {
        var email_thread_md = '';
        var text_thread_md = '';

        var bindBehaviors = function() {
            $("input.message-read").unbind('click');
            $("button.add-label").unbind('click');
            $("button.remove-label").unbind('click');
        };

        $(".datepicker").datepicker({
            autoclose: true,
            zIndexOffset: 2000
        });
        $(".timepicker").timepicker({
            autoclose: true,
            zIndexOffset: 2000
        });

        $(".timepicker").on('changeTime', function(ev){
            ev.preventDefault();
        });

        bindBehaviors();

        $("#add-appointment-form").hide();
        $("#add-appointment-form-button").click(function (ev) {
            $("#add-appointment-form").slideToggle();
        });

        $("#add-appointment").click(function (ev) {
            ev.preventDefault();
            var formData = {
                "appointment_date": $("[name=appointment_date]").val(),
                "appointment_time": $("[name=appointment_time] option:selected").val()
            };

            var request = $.ajax({
                url: "{{ route("add-appointment", [$campaign->id, $recipient->id]) }}",
                method: "POST",
                data: formData,
                dataType: "json"
            });

            request.done(function (response) {
                console.log(response);
                $("#new-appointment").empty();
                var template = $("#appointment-template").html();
                var appointmentDisplay = template.replace(/__DATE__/g, response.appointment_at);
                $("#new-appointment").html(appointmentDisplay);

            });

            request.fail(function (response) {
                alert("Unable to create appointment");
            });
        });

        $("#email-message-toggle").click(function (ev) {
            $("#email-message").slideToggle();
            if ($("#email-message-toggle i").hasClass("fa-chevron-circle-down")) {
                $("#email-message-toggle i").addClass("fa-chevron-circle-up").removeClass("fa-chevron-circle-down");
            } else {
                $("#email-message-toggle i").removeClass("fa-chevron-circle-up").addClass("fa-chevron-circle-down");
            }
        });

        @if ($emailDrop && $threads->get('email'))
        if ($("#email-message").length) {
            // Assemble the email into the iframe
            var emailContent = {!! json_encode($emailDrop->email_html) !!};
            var iframe = document.getElementById("email-message");
            var frameDoc = iframe.document;
            if (iframe.contentWindow) {
                frameDoc = iframe.contentWindow.document; // IE
                // Write into iframe
                frameDoc.open();
                frameDoc.writeln(emailContent);
                frameDoc.close();
            }

            // Set the height of the iframe to match the contents
            var contentHeight = iframe.contentWindow.document.body.scrollHeight + 10;
            iframe.height = contentHeight + "px";

            // Neuter all email links to prevent statistics skew
            $('#email-message').contents().find('a').click(function(event) {
                alert("demo only");
                event.preventDefault();
            });

            // Minimize the email for convenience
            $("#email-message").slideUp();
        }
        @endif
    });
</script>
