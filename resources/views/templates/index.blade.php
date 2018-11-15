@extends('layouts.remark')

@section('header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.min.css') }}">
@endsection

@section('manualStyle')
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
    @media (max-width: 575) {
        .hidden-md {
            display: none;
        }
    }
@endsection

@section('content')
<div class="page">
    <div class="page-header container-fluid">
        <div class="row-fluid">
            <div class="col-xxl-8 offset-xxl-2 col-md-12">
                <h3 class="page-title text-default">
                    Templates
                </h3>
                <div class="page-header-actions">
                    <a href="{{ secure_url('/templates/new') }}"
                       class="btn btn-sm btn-success waves-effect">
                        <i class="icon md-plus" aria-hidden="true"></i>
                        New
                    </a>
                    <a href="{{ secure_url('/template-builder/editor') }}"
                       class="btn btn-sm btn-primary waves-effect">
                        <i class="icon md-group" aria-hidden="true"></i>
                        Builder
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="page-content container-fluid">
        <div class="row-fluid" data-plugin="matchHeight" data-by-row="true">
            <div class="col-xxl-8 offset-xxl-2 col-md-12">
                <div class="panel panel-info">
                    <div class="panel-body">
                        @if ($templates->count() > 0)
                        <div class="table-responsive">
                            <table id="clients" class="table table-striped table-hover datatable">
                                <thead>
                                <tr>
                                    <th class="hidden-md">ID</th>
                                    <th width="50px">Type</th>
                                    <th>Title</th>
                                    <th>Created</th>
                                    <th>Delete</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($templates as $template)
                                    <tr class="client-row" data-client="{{ $template->id }}">
                                        <td valign="center" class="id-row hidden-md">{{ $template->id }}</td>
                                        <td>
                                            {{ ucwords(strtolower($template->type)) }}
                                        </td>
                                        <td>
                                            @if ($template->type == 'legacy')
                                                <i class="icon fa-paper-plane" aria-hidden="true"></i>
                                                <span class="sr-only">Legacy</span>
                                            @elseif ($template->type == 'email')
                                                <i class="icon fa-envelope" aria-hidden="true"></i>
                                                <span class="sr-only">Email</span>
                                            @elseif ($template->type == 'sms')
                                                <i class="icon fa-commenting" aria-hidden="true"></i>
                                                <span class="sr-only">SMS</span>
                                            @else
                                                <i class="icon fa-exclamation-triangle" aria-hidden="true"></i>
                                                <span class="sr-only">INVALID TYPE</span>
                                            @endif
                                            <a href="{{ secure_url('/template/' . $template->id . '/edit') }}"
                                                style="margin-left: 15px;">
                                                {{ $template->name }}
                                            </a>
                                        </td>
                                        <td>{{ show_date($template->created_at) }}</td>
                                        <td>
                                            <button class="btn btn-pure btn-danger btn-round delete-button"
                                                    data-deleteUrl="{{ secure_url('template/' . $template->id . '/delete') }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert=info">There don't appear to be any templates!  <a href="{{ secure_url('templates/new') }}">Create one now</a></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptTags')
    <script src="{{ secure_url('js/Plugin/material.js') }}"></script>
    <script src="{{ secure_url('js/Plugin/sweetalert.min.js') }}"></script>

    <script type="text/javascript" src="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $(".datatable").DataTable({"order": [[ 0, "desc" ]]});

            $(".delete-button").click(function() {
                var url = $(this).data('deleteurl');

                swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this template!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: false,
                        closeOnCancel: true,
                        customClass: "deleteBox"
                    },
                    function(){
                        $.get(
                            url,
                            function(data) {
                                swal("All Done", "Template Deleted! This page will now reload", "success");
                                location.reload();
                            }
                        );
                    });
            });
        });
    </script>
@endsection

@section('scripts')
@endsection
