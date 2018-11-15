@extends('layouts.remark')

@section('header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">
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
@endsection

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-xxl-8 offset-xxl-2 col-md-12">
                    <h3 class="page-title text-default">
                        Users
                    </h3>
                    <div class="page-header-actions">
                        <a href="{{ secure_url('/users/new') }}"
                           class="btn btn-sm btn-success waves-effect">
                            <i class="icon md-plus" aria-hidden="true"></i>
                            New User
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
                            <div class="table-responsive">
                                <table id="users" class="table table-striped table-hover datatable">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Org</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr class="user-row" data-user="{{ $user->id }}">
                                            <td valign="center" class="id-row"><strong>{{ $user->id }}</strong></td>
                                            <td width="60px">{{ $user->access }}</td>
                                            <td>
                                                <a href="{{ secure_url('/user/' . $user->id) }}">{{ $user->name }}</a>
                                            </td>
                                            <td>{{ join(',', collect($user->companies)->pluck('name')->toArray()) }}</td>
                                            <td>
                                                @if (trim($user->email) != '')
                                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a><br>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $user->phone_number }}
                                            </td>
                                            <td>
                                                <a class="btn btn-pure btn-warning btn-round"
                                                   href="{{ secure_url('user/' . $user->id . '/edit') }}">
                                                    <i class="fa fa-pencil"></i>
                                                    Edit
                                                </a>
                                                <a class="btn btn-pure btn-link btn-round"
                                                   href="{{route('auth.impersonate', ['user' => $user->id])}}">{{ __('Log in as') }}</a>

                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
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

    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".datatable").DataTable({"order": [[0, "desc"]]});
        });
    </script>
@endsection

@section('scripts')
@endsection

