@extends('layouts.remark_campaign')

@section('header')
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.min.css') }}">
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
    .dashboard .card, .dashboard .panel {
        height: inherit;
    }
    .card-button {
        z-index: 8000;
    }
@endsection

@section('campaign_content')
    <div class="col-md-12" style="margin-bottom: 15px;">
        <a class="badge badge-outline text-default" href="#" id="show-table" style="font-size: 24px; color: #666">
            <i class="icon fa-3x fa-list"></i>
        </a>
        <a class="badge badge-outline text-default" href="#" id="show-cards" style="font-size: 24px; color: #666">
            <i class="icon fa-3x fa-th"></i>
        </a>
        <a href="{{ secure_url('campaign/' . $campaign->campaign_id . '/drops/new') }}"
           class="btn btn-success waves-effect float-right">
            <i class="icon md-plus" aria-hidden="true"></i>
            New
        </a>
    </div>
    @if ($drops->count() > 0)
    <div class="drops-table">
        <table class="table table-condensed">
            <thead>
            <tr>
                <th>ID</th>
                <th>Status</th>
                <th>Drop Date</th>
                <th>Type</th>
                <th>Recipients</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($drops as $drop)
                <tr>
                    <td>{{ $drop->id }}</td>
                    <td>
                        @if ($drop->type != 'sms')
                            <span class="badge badge-outline
                            @if ($drop->status == 'Completed')
                                    badge-success
                            @elseif ($drop->status == 'Paused')
                                    badge-warning
                            @else
                                    badge-default
                            @endif
                                    ">
                                {{ ucwords($drop->status) }}
                            </span>
                        @else
                            <a href="{{ secure_url('campaign/' . $campaign->id . '/drop/' . $drop->id) }}"
                               class="btn btn-xs
                            @if ($drop->status == 'Completed')
                                btn-success
                            @elseif ($drop->status == 'Paused')
                                btn-warning
                            @else
                                btn-primary
                            @endif
                            @if ($drop->send_at > \Carbon\Carbon::now())
                                disabled
                            @endif
                                ">
                                @if ($drop->status == 'Completed')
                                Open
                                @elseif ($drop->status == 'Paused')
                                Paused
                                @else
                                Run
                                @endif
                            </a>
                        @endif
                    </td>
                    <td>
                        @if ($drop->status == 'Completed')
                            {{ show_date($drop->completed_at ?: $drop->send_at) }}
                        @else
                            {{ show_date($drop->send_at) }}
                        @endif
                    </td>
                    <td>
                        @if ($drop->type == 'sms')
                            <i style="padding-right: 8px;" class="icon oi-device-mobile"></i> SMS
                        @elseif ($drop->type == 'email')
                            <i style="padding-right: 8px;" class="icon fa-envelope"></i> Email
                        @else
                            <i style="padding-right: 8px;" class="icon fa-paper-plane"></i> Legacy
                        @endif
                    </td>
                    <td>{{ $drop->recipients }}</td>
                    <td>
                        @if ( in_array($drop->status, ['Completed', 'Cancelled', 'Processing', 'Deleted']))
                            <span class="badge badge-outline badge-success">No Actions Available</span>
                        @else
                            <a href="{{ secure_url('/campaign/' . $campaign->id . '/drop/' . $drop->id . '/edit') }}"
                               class="btn btn-xs btn-warning card-button">
                                <i class="icon fa-fw fa-pencil"></i>
                                Edit
                            </a>
                            <button data-url="{{ secure_url('/drop/' . $drop->id . '/delete') }}"
                                    class="btn btn-xs btn-danger waves-effect card-button delete-button">
                                <i class="icon fa-trash"></i>
                                Delete
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="drops-cards card-deck">
        @foreach ($drops as $drop)
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card">
                @if ($drop->type == 'sms')
                    <blockquote class="blockquote cover-quote card-blockquote">
                        <p>{!! $drop->text_message !!}</p>
                    </blockquote>
                @else
                    <img class="card-img-top w-full"
                         src="https://placeholdit.imgix.net/~text?txtsize=100&txt=No%20Image&w=500&h=500&txttrack=0"
                         alt="Card image cap">
                @endif
                <div class="card-block">
                    <h4 class="card-title
                        {{ $drop->status == 'Completed' ? 'text-success' : '' }}
                        {{ $drop->status == 'Paused' ? 'text-warning' : '' }}
                        {{ $drop->status == 'Error' ? 'text-danger' : '' }}
                    ">
                        @if ($drop->type == 'email')
                        <i class="icon md-email"></i>
                        @elseif ($drop->type == 'sms')
                        <i class="icon oi-device-mobile"></i>
                        @else
                        <i class="icon fa-paper-plane"></i>
                        @endif

                        @if ($drop->status == 'Completed')
                            {{ show_date($drop->completed_at ?: $drop->send_at) }}
                        @else
                            {{ show_date($drop->send_at) }}
                        @endif
                    </h4>
                    <p class="card-text">
                        <i class="icon fa-folder"></i> <strong>Group {{ $drop->target_group }}</strong><br>
                        <i class="icon md-account"></i> &nbsp;<strong>{{ $drop->recipients }} recipients</strong>
                    </p>
                    <p class="card-text">
                    </p>
                    @if ( in_array($drop->status, ['Completed', 'Cancelled', 'Processing', 'Deleted']))
                    <a href="{{ secure_url('/campaign/' . $campaign->id . '/drop/' . $drop->id) }}" class="btn btn-primary waves-effect">View</a>
                    @else
                        @if ($drop->type == 'sms')
                            @if ($drop->send_at > \Carbon\Carbon::now()->timezone('UTC'))
                            <a href="#"
                               class="btn btn-default disabled">
                                <i class="icon fa-hourglass-2"></i>
                                <span class="sr-only">Wait</span>
                            </a>
                            @else
                            <a href="{{ secure_url('/campaign/' . $campaign->id . '/drop/' . $drop->id ) }}"
                               class="btn btn-primary waves-effect">
                                <i class="icon md-play"></i>
                                <span class="sr-only">Run</span>
                            </a>
                            @endif
                        @endif
                    <a href="{{ secure_url('/campaign/' . $campaign->id . '/drop/' . $drop->id . '/edit') }}"
                       class="btn btn-warning card-button waves-effect">
                        <i class="icon fa-fw fa-pencil"></i>
                        <span class="sr-only">Edit</span>
                    </a>
                    <button data-url="{{ secure_url('/drop/' . $drop->id . '/delete') }}"
                            class="btn btn-danger waves-effect card-button delete-button">
                        <i class="icon fa-trash"></i>
                        <span class="sr-only">Delete</span>
                    </button>
                    @endif
                    <div class="ribbon ribbon-badge ribbon-bottom ribbon-reverse
                        {{ $drop->status == 'Completed' ? 'ribbon-success' : '' }}
                        {{ $drop->status == 'Processing' ? 'ribbon-primary' : '' }}
                        {{ $drop->status == 'Paused' ? 'ribbon-warning' : '' }}
                        {{ $drop->status == 'Error' ? 'ribbon-danger' : '' }}
                    ">
                        <span class="ribbon-inner">
                            @if ($drop->status == 'Processing' && $drop->percentage_complete > 0)
                                {{ $drop->percentage_complete }}%
                            @else
                                {{ ucwords($drop->status) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
@endsection

@section('scriptTags')
    <script type="text/javascript" src="{{ secure_url('js/Plugin/panel.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".drops-cards").hide();

            $("#show-cards").click(function(){
                $(".drops-table").hide();
                $(".drops-cards").show();
            });

            $("#show-table").click(function(){
                $(".drops-table").show();
                $(".drops-cards").hide();
            });

        });

        $(".delete-button").click(function() {
            var url = $(this).data('url');

            swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this drop!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false,
                    customClass: "deleteBox"
                },
                function(){
                    $.post(
                        url,
                        function(data) {
                            swal("All Done", "Drop Deleted!", "success");
                            location.reload();
                        }
                    );
                });
        });
    </script>
@endsection

@section('scripts')

@endsection
