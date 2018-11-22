@extends('layouts.remark')

@section('header')
    <link rel="stylesheet" href="{{ secure_url('fonts/ionicons/ionicons.css') }}">
    <link rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-datepicker/bootstrap-datepicker.css') }}">
@endsection

@section('content')
    <div class="page">
        <div class="page-header container">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-title text-default">Drop Management</h3>
                    <div class="page-header-actions">
                        <a href="{{ secure_url('/system/stop') }}" role="button" class="btn btn-danger waves-effect disabled">
                            <i class="icon md-stop"></i>
                            STOP ALL
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-3 col-md-4 text-center">
                                    <div id="deployment-date" data-date="{{ request()->has('date') ? request()->get('date') : \Carbon\Carbon::now()->format('m/d/Y') }}" class="text-center"></div>
                                </div>
                                <div class="col-lg-9 col-md-8" style="padding-left: 30px">
                                    <div class="card card-primary card-inverse border border-primary">
                                        <div class="card-block">
                                            <h4 class="card-title">Drops for week <span class="badge badge-default badge-pill ml-10">{{ $scheduleQueue->count() }}</span></h4>
                                            <p class="card-text">
                                                <span class="h5 mr-10 white">{{ $startDate }}</span> through <span class="h5 ml-10 white">{{ $endDate }}</span>
                                            </p>

                                        </div>
                                    </div>
                                    <div class="responsive-table">
                                    @if ($scheduleQueue->count() > 0)
                                        <table class="table table-hover table-striped table-responsive">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Drop Date</th>
                                                <th>Campaign</th>
                                                <th>Type</th>
                                                <th>Emails</th>
                                                <th>SMS</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($scheduleQueue as $schedule)
                                            <tr class="
                                                {{ $schedule->status == "Processing" ? 'table-success' : '' }}
                                                {{ $schedule->status == "Paused" ? 'table-warning' : '' }}
                                                ">
                                                <td><a href="{{ secure_url('campaign/' . $schedule->campaign_id . '/drop/' . $schedule->id) }}" class="btn btn-primary btn-xs"><strong>#{{ $schedule->campaign_schedule_id }}</strong></a></td>
                                                <td>{{ (new \Carbon\Carbon($schedule->send_at))->format('m/d/Y g:i A') }}</td>
                                                <td><div><a href="{{ secure_url('campaign/' . $schedule->campaign_id . '/') }}" class="btn btn-xs btn-info">Campaign #{{ $schedule->campaign_id }} </a> {{ $schedule->name }} </div></td>
                                                <td>{{ ucwords($schedule->type) }}</td>
                                                <td>
                                                    @if ($schedule->type != 'sms')
                                                        {{ $schedule->recipients_count }}
                                                    @else
                                                        <i class="icon md-block text-danger" aria-hidden="true"></i><span class="sr-only">None</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($schedule->type != 'email')
                                                        {{ $schedule->recipients_count }}
                                                    @else
                                                        <i class="icon md-block text-danger" aria-hidden="true"></i><span class="sr-only">None</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                @if ($schedule->status == "Processing")
                                                    <i class="icon fa fa-cog fa-spin fa-3x fa-fw" aria-hidden="true"></i>
                                                    <span class="sr-only">Loading...</span>
                                                @else
                                                    @if ($schedule->status == "Completed")
                                                    <div class="badge badge-outline badge-primary">Completed</div>
                                                    @else
                                                        @if ($schedule->status == "Pending")
                                                    <button type="button" class="btn btn-xs btn-warning pause-button" data-url="{{ secure_url('drop/' . $schedule->id . '/pause') }}" data-toggle="tooltip" data-original-title="Pause Drop">
                                                        <i class="icon md-pause"></i>
                                                    </button>
                                                        @elseif ($schedule->status == "Paused")
                                                    <button type="button" class="btn btn-xs btn-success resume-button" data-url="{{ secure_url('drop/' . $schedule->id . '/resume') }}" data-toggle="tooltip" data-original-title="Resume Drop">
                                                        <i class="icon md-play"></i>
                                                    </button>
                                                        @endif
                                                    <button type="button" class="btn btn-xs btn-danger delete-button" data-url="{{ secure_url('drop/' . $schedule->id . '/delete') }}" data-toggle="tooltip" data-original-title="Delete Drop">
                                                        <i class="icon fa-trash"></i>
                                                    </button>
                                                    @endif
                                                @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        </table>
                                        {{ $scheduleQueue->appends(request()->all())->links() }}
                                        @else
                                        <div class="alert alert-default"><p>No drops are scheduled for the week selected</p></div>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptTags')
    <script type="text/javascript" src="{{ secure_url('/vendor/moment/moment.js') }}"></script>
    <script src="{{ secure_url('js/Plugin/bootstrap-datepicker.js') }}"></script>
    <script src="{{ secure_url('vendor/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('/vendor/bootstrap-sweetalert/sweetalert.js') }}"></script>

<script type="text/javascript">
$(document).ready(function() {
    $(".pause-button").click(function() {
        var url = $(this).data('url');

        $.get(
            url,
            function(data) {
                if (data.status == "success") {
                    swal("All Done", "Drop Paused!", "success");
                } else if (data.status == "error") {
                    swal("Failed", data.message, "error");
                } else {
                    swal("Failed", "There was an error processing your request", "error");
                }

                location.reload();
            }
        );
    });

    $(".resume-button").click(function() {
        var url = $(this).data('url');

        $.get(
            url,
            function(data) {
                if (data.status == "success") {
                    swal("All Done", "Drop Resumed!", "success");
                } else if (data.status == "error") {
                    swal("Failed", data.message, "error");
                }

                location.reload();
            }
        );
    });

    $(".delete-button").click(function() {
        var url = $(this).data('url');

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this drop!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            showLoaderOnConfirm: true,
            closeOnConfirm: false,
        },
        function(){
            $.get(
                url,
                function(data) {
                    if (data.status == "success") {
                        swal("All Done", "Drop Deleted!", "success");
                    } else if (data.status == "error") {
                        swal("Failed", data.message, "error");
                    }

                    location.reload();
                }
            );
        });
    });

    $('#deployment-date').datepicker();
    $("#deployment-date").on('changeDate', function(event) {
        window.location.href = "{{ secure_url('/system/drops/?date=') }}" + $("#deployment-date").datepicker('getFormattedDate');
    });
});
</script>
@endsection

@section('scripts')

@endsection

