@extends('layouts.remark')

@section('header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.min.css') }}">
@endsection

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-xxl-8 offset-xxl-2 col-md-12">
                    <h3 class="page-title text-default">Campaigns</h3>
                    <div class="page-header-actions">
                        <a href="{{ secure_url('/campaigns/new') }}"
                           class="btn btn-sm btn-success waves-effect">
                            <i class="icon md-plus" aria-hidden="true"></i>
                            New Campaign
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="row-fluid" data-plugin="matchHeight" data-by-row="true">
                <div class="col-xxl-8 offset-xxl-2 col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            @if (count($campaigns) > 0)
                            <div class="table-responsive">
                                <div class="container mb-20">
                                    <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                            <form action="{{ secure_url('campaigns/') }}" method="get">
                                                {{ csrf_field() }}
                                                <div class="input-search">
                                                    <i class="input-search-icon md-search" aria-hidden="true"></i>
                                                    <input type="text" class="form-control" name="q" placeholder="Search..." value="{{ request('q') }}">
                                                    <button type="button"
                                                            @if (request('q'))
                                                            onClick="window.location.href = '{{ secure_url('campaigns/?q=') }}'"
                                                            @endif
                                                            class="input-search-close icon md-close" aria-label="Close"></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <table id="campaigns" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th class="hidden-sm-down">ID</th>
                                        <th>Name</th>
                                        <th class="hidden-sm-down">Agency</th>
                                        <th class="hidden-sm-down">Dealership</th>
                                        <th class="hidden-md-down"><i class="fa fa-fw fa-user text-primary sr-hidden"></i> <span class="sr-only">Targets</span></th>
                                        <th class="hidden-md-down"><i class="fa fa-fw fa-phone text-primary sr-hidden"></i> <span class="sr-only">Phones</span></th>
                                        <th class="hidden-md-down"><i class="fa fa-fw fa-envelope text-primary sr-hidden"></i> <span class="sr-only">Emails</span></th>
                                        <th class="hidden-md-down"><i class="fa fa-fw fa-commenting text-primary sr-hidden"></i> <span class="sr-only">SMS/MMS</span></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach((object)$campaigns as $campaign)
                                        <tr class="campaign-row" data-campaign="{{ $campaign->id }}">
                                            <td valign="center" class="id-row hidden-sm-down">
                                                <strong>{{ $campaign->id }}
                                                </strong><br>
                                            </td>
                                            <td>{!! $campaign->getNameForTemplate() !!}</td>
                                            <td class="hidden-sm-down">{{ $campaign->agency->name }}</td>
                                            <td class="hidden-sm-down">{{ $campaign->dealership->name }}</td>
                                            <td class="hidden-md-down">{{ $campaign->recipients_count }}</td>
                                            <td class="hidden-md-down">{{ $campaign->phone_responses_count }}</td>
                                            <td class="hidden-md-down">{{ $campaign->email_responses_count }}</td>
                                            <td class="hidden-md-down">{{ $campaign->text_responses_count }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="links">{{ $campaigns->links() }}</div>
                            </div>
                        </div>
                        @else
                            @if (request('q'))
                                <div class="container mb-20">
                                    <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                            <form action="{{ secure_url('campaigns/') }}" method="get">
                                                {{ csrf_field() }}
                                                <div class="input-search">
                                                    <i class="input-search-icon md-search" aria-hidden="true"></i>
                                                    <input type="text" class="form-control" name="q" placeholder="Search..." value="{{ request('q') }}">
                                                    <button type="button"
                                                            @if (request('q'))
                                                            onClick="window.location.href = '{{ secure_url('campaigns/?q=') }}'"
                                                            @endif
                                                            class="input-search-close icon md-close" aria-label="Close"></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="alert alert-info"><p>No campaigns to show</p></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptTags')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $(".datatable").dataTable({"order": [[0, "desc"]]});

            $(".delete-button").click(function() {
                var url = $(this).data('deleteurl');

                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this campaign!",
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
                    $.get(
                        url,
                        function(data) {
                            swal("All Done", "Campaign Deleted!", "success");
                            location.reload();
                        }
                    );
                });
            });
        });
    </script>
@endsection

