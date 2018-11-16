@extends('layouts.remark_company-manager')

@section('header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">
@endsection

@section('secondary_navbar')
    <a href=""
       class="btn btn-sm btn-success waves-effect navbar-btn navbar-right">
        <i class="icon md-plus" aria-hidden="true"></i>
        Add New User
    </a>
@endsection

@section('company_content')
    <div class="col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-hover datatable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Type</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($company->users as $user)
                    <tr>
                        <td class="id-row v-center"><strong>{{ $user->id }}</strong></td>
                        <td class="v-center">{{ $user->first_name }}</td>
                        <td class="v-center">{{ $user->last_name }}</td>
                        <td class="text-capitalize v-center">{{ $user->pivot->role }}</td>
                        <td class="v-center">{{ $user->username }}</td>
                        <td class="v-center">{{ $user->email }}</td>
                        <td class="v-center">{{ $user->phone_number }}</td>
                        <td>
                            {{--<a class="btn btn-pure btn-primary btn-round"--}}
                               {{--href="{{ route('company.impersonate_user', ['company' => $company->id]) }}">--}}
                                Access
                            {{--</a>--}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scriptTags')
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".datatable").DataTable({
                "order": [[0, "asc"]]
            });
        });
    </script>
@endsection
