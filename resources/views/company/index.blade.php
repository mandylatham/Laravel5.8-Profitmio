@extends('layouts.remark')

@section('header')
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.min.css') }}">
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
                    <h3 class="page-title text-default">Companies</h3>
                    <div class="page-header-actions">
                        <a href="{{ route('company.create') }}"
                           class="btn btn-sm btn-success waves-effect">
                            <i class="icon md-plus" aria-hidden="true"></i>
                            Create New Company
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
                            @if (count($companies) > 0)
                                <div class="table-responsive">
                                    <div class="container mb-20">
                                        <div class="row">
                                            <div class="col-md-6 offset-md-6">
                                                <form action="{{ route('company.index') }}" method="get">
                                                    {{ csrf_field() }}
                                                    <div class="input-search">
                                                        <i class="input-search-icon md-search" aria-hidden="true"></i>
                                                        <input type="text" class="form-control" name="q" placeholder="Search..." value="{{ request('q') }}">
                                                        <button type="button"
                                                                @if (request('q'))
                                                                onClick="window.location.href = '{{ route('company.index', ['q' => '']) }}'"
                                                                @endif
                                                                class="input-search-close icon md-close" aria-label="Close"></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="companies" class="table table-striped table-hover">
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
                                            <tr class="user-row">
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
                                                    <button class="btn btn-pure btn-danger btn-round delete-button"
                                                            data-deleteUrl="{{ route('company.delete', ['company' => $company->id]) }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div class="links">{{ $companies->links() }}</div>
                                </div>
                        </div>
                        @else
                            @if (request('q'))
                                <div class="container mb-20">
                                    <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                            <form action="{{ route('company.index') }}" method="get">
                                                {{ csrf_field() }}
                                                <div class="input-search">
                                                    <i class="input-search-icon md-search" aria-hidden="true"></i>
                                                    <input type="text" class="form-control" name="q" placeholder="Search..." value="{{ request('q') }}">
                                                    <button type="button"
                                                            @if (request('q'))
                                                            onClick="window.location.href = '{{ route('company', ['q' => '']) }}'"
                                                            @endif
                                                            class="input-search-close icon md-close" aria-label="Close"></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="alert alert-info"><p>No companies to show</p></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptTags')
    <script type="text/javascript" src="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $(".delete-button").click(function() {
                var url = $(this).data('deleteurl');

                swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this company!",
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
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            success: function() {
                                swal("All Done", "Company Deleted!", "success");
                                location.reload();
                            },
                            error: function (error) {
                                swal(error.responseJSON);
                            }
                        });
                    });
            });
        });
    </script>
@endsection
