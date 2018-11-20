@extends('layouts.remark')

@section('header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ secure_url('vendor/fullcalendar/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/icheck/icheck.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/morris/morris.css') }}">
    <style type="text/css" media="all">
        .fc-event, .fc-event-dot {
            background-color: #eafde1;
        }
        .data-link {
            cursor: pointer;
        }
        a.card:hover {
            text-decoration:none;
            color: lightgreen;
            border: 1px solid darkgreen;
            text-shadow: 1px 1px 4px green;
        }
    </style>
@endsection

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-md-12">
                    <h3 class="page-title text-default"></h3>
                </div>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="row">
                <div class="col-md-2">
                    <div class="card card-block p-300 bg-indigo-600">
                        <div class="card-watermark darker font-size-60 m-15"><i class="icon md-account-circle" aria-hidden="true"></i></div>
                        <div class="counter counter-inverse counter-md text-left">
                            <span class="counter-number">{{ number_format($stats->responses) }}</span>
                            <div class="counter-label text-uppercase">responses</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-block p-300 bg-light-blue-600">
                        <div class="card-watermark darker font-size-60 m-15"><i class="icon md-phone-in-talk" aria-hidden="true"></i></div>
                        <div class="counter counter-inverse counter-md text-left">
                            <div class="counter-number-group">
                                <span class="counter-number">{{ number_format($stats->calls) }}</span>
                            </div>
                            <div class="counter-label text-uppercase">calls</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-block p-300 bg-light-blue-600">
                        <div class="card-watermark darker font-size-60 m-15">
                            <i class="icon md-email" aria-hidden="true"></i>
                        </div>
                        <div class="counter counter-inverse counter-md text-left">
                            <span class="counter-number">{{ number_format($stats->emails) }}</span>
                            <div class="counter-label text-uppercase">emails</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-block p-300 bg-light-blue-600">
                        <div class="card-watermark darker font-size-60 m-15">
                            <i class="icon md-comment-text-alt" aria-hidden="true"></i>
                        </div>
                        <div class="counter counter-inverse counter-md text-left">
                            <div class="counter-number-group">
                                <span class="counter-number">{{ number_format($stats->sms) }}</span>
                            </div>
                            <div class="counter-label text-uppercase">sms</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-block p-300 bg-green-700">
                        <div class="card-watermark darker font-size-60 m-15">
                            <i class="icon md-calendar" aria-hidden="true"></i>
                        </div>
                        <div class="counter counter-inverse counter-md  text-left">
                            <div class="counter-number-group">
                                <span class="counter-number">{{ number_format($stats->appointments) }}</span>
                                <span class="counter-number-related text-capitalize"></span>
                            </div>
                            <div class="counter-label text-capitalize">appointments</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-block p-300 bg-green-700">
                        <div class="card-watermark darker font-size-60 m-15">
                            <i class="icon md-phone-forwarded" aria-hidden="true"></i>
                        </div>
                        <div class="counter counter-inverse counter-md text-left">
                            <div class="counter-number-group">
                                <span class="counter-number">{{ number_format($stats->callbacks) }}</span>
                                <span class="counter-number-related text-capitalize"></span>
                            </div>
                            <div class="counter-label text-capitalize">callbacks</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default panel-bordered anumation-scale-up"
                         style="animation-fill-mode: backwards; animation-duration: 250ms; animation-delay: 850ms;">
                        <div class="panel-heading">
                            <h5 class="panel-title">Calendars</h5>
                        </div>
                        <div class="panel-body">

                            <div class="nav-tabs-horizontal" data-plugin="tabs">
                                <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="appointmentsTabLink" data-toggle="tab" href="#appointmentsTab" aria-controls="appointmentsTab" role="tab">
                                            Appointments
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="dropsTabLink" data-toggle="tab" href="#dropsTab" aria-controls="dropsTab" role="tab">
                                            Scheduled Drops
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content pt-20">
                                    <div class="tab-pane active" id="appointmentsTab" role="tabpanel">
                                        <div id="appointments"></div>
                                    </div>
                                    <div class="tab-pane" id="dropsTab" role="tabpanel">
                                        <div id="drops"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default panel-bordered anumation-scale-up"
                         style="animation-fill-mode: backwards; animation-duration: 250ms; animation-delay: 850ms;">
                        <div class="panel-heading">
                            <h5 class="panel-title">Callback List</h5>
                        </div>
                        <div class="panel-body">
                            @if ($callbacks->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover datatable">
                                        <thead>
                                        <tr>
                                            <td>Called</td>
                                            <td>Name</td>
                                            <td>Called At</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($callbacks as $callback)
                                            <tr>
                                                <td><input type="checkbox" class="callback-button" data-callback_id="{{ $callback->id }}"></td>
                                                <td>
                                                    <div>{{ $callback->name }}</div>
                                                    <small>{{ $callback->email }}</small>
                                                    <div style="text-transform: uppercase; font-size: small; color: #555;">{{ $callback->vehicle }}</div>
                                                    <div>
                                                        <button class="btn btn-primary btn-pure button-link" data-url="tel:{{ $callback->phone_number}}">
                                                            <i class="icon md-phone" aria-hidden="true"></i>
                                                            {{ $callback->phone_number }}
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>{{ show_date($callback->created_at) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">None Found</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    @if ($campaigns->count() > 0)
                        @foreach ($campaigns as $campaign)
                            <div class="panel panel-bordered animation-scale-up" style="animation-fill-mode: backwards; animation-duration: 250ms; animation-delay: 850ms;">
                                <div class="panel-heading
                        @if ($campaign->status == 'Complete')
                                    bg-light-green-100
@endif
                                    ">
                                    <div class="float-right p-15">
                                        <small>{{ $campaign->status }}</small>
                                    </div>
                                    <h3 class="panel-title">
                                        <small>Campaign {{ $campaign->id}}</small><br>
                                        {{ $campaign->name}}<br>
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <button class="panel-action btn btn-info form-control data-link bg-blue-500" data-url="{{ secure_url('campaign/' . $campaign->id . '/response-console') }}">
                                                <i class=" icon fa-terminal" style="" aria-hidden="true"></i>
                                                <span style="">Go To Console</span>
                                            </button>
                                            <a href="{{ secure_url('campaign/' . $campaign->id . '/response-console/unread') }}"
                                               class="card card-block card-bordered p-300 mt-10
                                    @if ($campaign->unread >= 0 and $campaign->unread < 50)
                                                   bg-light-green-600
@elseif ($campaign->unread >= 50 && $campaign->unread < 100)
                                                   bg-orange-500
@elseif ($campaign->unread >= 100 && $campaign->unread < 150)
                                                   bg-deep-orange-500
@elseif ($campaign->unread >= 150)
                                                   bg-red-500
@else
                                                   bg-red-900
@endif
                                                   ">
                                                <div class="card-watermark darker font-size-60 m-15">
                                                    <i class="icon md-comment-text-alt" aria-hidden="true"></i>
                                                </div>
                                                <div class="counter counter-inverse counter-md text-left">
                                                    <div class="counter-number-group">
                                                        <span class="counter-number">{{ number_format($campaign->unread) }}</span>
                                                    </div>
                                                    <div class="counter-label text-uppercase">Unread</div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-lg-6">
                                            <table class="table table-condensed font-size-10 table-hover">
                                                <tbody>
                                                <tr class="data-link" data-url="{{ secure_url('campaign/' . $campaign->id . '/response-console/calls') }}">
                                                    <th>Calls</th>
                                                    <td>{{ number_format($campaign->phones) }}</td>
                                                </tr>
                                                <tr class="data-link" data-url="{{ secure_url('campaign/' . $campaign->id . '/response-console/email') }}">
                                                    <th>Emails</th>
                                                    <td>{{ number_format($campaign->emails) }}</td>
                                                </tr>
                                                <tr class="data-link" data-url="{{ secure_url('campaign/' . $campaign->id . '/response-console/sms') }}">
                                                    <th>SMS</th>
                                                    <td>{{ number_format($campaign->texts) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Appointments</th>
                                                    <td>{{ $appointmentCounts->has($campaign->id) ? number_format($appointmentCounts->get($campaign->id)->appointments) : 0 }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Callbacks</th>
                                                    <td>{{ $appointmentCounts->has($campaign->id) ? number_format($appointmentCounts->get($campaign->id)->callbacks) : 0 }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="exampleMorrisDonut"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h5 class="panel-title">No Campaigns Found</h5>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptTags')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/icheck/icheck.min.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/fullcalendar/fullcalendar.min.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/raphael/raphael.min.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/morris/morris.min.js') }}"></script>
    <script>

        $(document).ready(function() {
            $(".datatable").dataTable({"order": [[0, "desc"]]});

            $(".data-link").click(function() {
                window.location.href = $(this).data('url');
            });

            $('.icheckbox > input[type=checkbox]').iCheck({
                labelHover: false,
                cursor: true
            });

            $(".callback-button").change(function() {
                $.post(
                    "{{ secure_url('appointment/') }}" + "/" + $(this).data('callback_id') + "/update-called-status",
                    { "called_back": $(this).prop('checked') },
                    'json'
                );

                $(this).parent().closest('tr').hide();
            });

            $('#appointments').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'listDay,listWeek,month'
                },
                // customize the button names,
                // otherwise they'd all just say "list"
                views: {
                    listDay: { buttonText: 'day' },
                    listWeek: { buttonText: 'week' }
                },

                defaultView: 'month',
                defaultDate: '{{ \Carbon\Carbon::now()->format("Y-m-d") }}',
                navLinks: true, // can click day/week names to navigate views
                editable: false,
                eventLimit: true, // allow "more" link when too many events
                events: {!! $appointments !!}
            });

            $('#drops').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'listDay,listWeek,month'
                },
                // customize the button names,
                // otherwise they'd all just say "list"
                views: {
                    listDay: { buttonText: 'day' },
                    listWeek: { buttonText: 'week' }
                },

                defaultView: 'month',
                defaultDate: '{{ \Carbon\Carbon::now()->format("Y-m-d") }}',
                navLinks: true, // can click day/week names to navigate views
                editable: false,
                eventLimit: true, // allow "more" link when too many events
                events: {!! $drops !!}
            });

            $("#appointmentsTabLink").click(function() {
                setTimeout(function(){ $("#appointments").fullCalendar('render'); }, 50);

                console.log('re-rendered appointments calendar');
            });

            $("#dropsTabLink").click(function() {
                setTimeout(function(){ $("#drops").fullCalendar('render'); }, 50);

                console.log('re-rendered drops calendar');
            });
        });


    </script>
@endsection
