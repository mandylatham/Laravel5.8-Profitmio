@extends('layouts.remark')

@section('header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">
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
                        <a href="{{ route('user.create') }}"
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
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Type</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td class="id-row v-center"><strong>{{ $user->id }}</strong></td>
                                            <td class="v-center">{{ $user->first_name }}</td>
                                            <td class="v-center">{{ $user->last_name }}</td>
                                            <td class="text-capitalize v-center">{{ $user->pivot->role }}</td>
                                            <td class="v-center">{{ $user->username }}</td>
                                            <td class="v-center">{{ $user->email }}</td>
                                            <td class="v-center">{{ $user->phone_number }}</td>
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

