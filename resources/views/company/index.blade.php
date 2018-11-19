@extends('layouts.remark')

@section('header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">
@endsection

@section('manualStyle')
    .company-image {
        width: 40px;
        height: 40px;
        background-size: cover;
        background-position: 50% 50%;
        background-color: #dadada;
        border-radius: 50%;
    }
@endsection

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-xxl-8 offset-xxl-2 col-md-12">
                    <h3 class="page-title text-default">
                        Companies
                    </h3>
                    <div class="page-header-actions">
                        <a href="{{ route('company.create') }}"
                           class="btn btn-sm btn-success waves-effect">
                            <i class="icon md-plus" aria-hidden="true"></i>
                            Create new company
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
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Url</th>
                                        <th>Phone</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($companies as $company)
                                        <tr class="user-row" data-company="{{ $company->id }}">
                                            <td class="id-row v-center"><strong>{{ $company->id }}</strong></td>
                                            <td width="60px" class="text-center">
                                                <div class="company-image" style="background-image: url('{{ $company->image_url }}')"></div>
                                            </td>
                                            <td class="v-center">
                                                <a href="{{ route('company.campaign.index', ['company' => $company->id]) }}">{{ $company->name }}</a>
                                            </td>
                                            <td class="text-capitalize v-center">{{ $company->type }}</td>
                                            <td class="v-center">{{ $company->url }}</td>
                                            <td class="v-center">{{ $company->phone }}</td>
                                            <td>
                                                <a class="btn btn-pure btn-warning btn-round"
                                                   href="{{ route('company.edit', ['company' => $company->id]) }}">
                                                    <i class="fa fa-pencil"></i>
                                                    Edit
                                                </a>
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
            $(".datatable").DataTable({"order": [[0, "asc"]]});
        });
    </script>
@endsection

@section('scripts')
@endsection
