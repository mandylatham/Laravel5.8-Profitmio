@extends('layouts.remark_company-manager')

@section('header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">
@endsection

@section('secondary_navbar')
    <a href="{{ route('company.user.create', ['company' => $company->id]) }}"
       class="btn btn-sm btn-success waves-effect navbar-btn pull-right">
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
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($company->users as $user)
                    <tr>
                        <td class="id-row v-center"><strong>{{ $user->id }}</strong></td>
                        <td class="v-center">{{ $user->first_name }}</td>
                        <td class="v-center">{{ $user->last_name }}</td>
                        <td class="text-capitalize v-center">@role($user->getRole($company))</td>
                        <td class="v-center">{{ $user->username }}</td>
                        <td class="v-center">{{ $user->email }}</td>
                        <td class="v-center">{{ $user->phone_number }}</td>
                        <td class="v-center text-center">@status($user->isActive($company->id))</td>
                        <td>
                            <a class="btn btn-sm btn-warning btn-round mb-5"
                               href="{{ route('company.user.edit', ['user' => $user->id, 'company' => $company->id]) }}">
                                Edit
                            </a>
                                    @if (!$user->isAdmin() && $user->isActive($company->id))
                            <a class="btn btn-sm btn-success btn-round mb-5"
                               href="{{ route('admin.impersonate', ['user' => $user->id, 'company' => $company->id]) }}">
                                Impersonate
                            </a>
                            @endif
                            @if (!$user->isCompanyProfileReady($company))
                                <a class="btn btn-sm btn-primary btn-round mb-5"
                                   href="{{ route('admin.resend-invitation', ['user' => $user->id, 'company' => $company->id ]) }}">
                                    Re-send Invitation
                                </a>
                            @endif
                            @if(!$user->isAdmin())
                                @if($user->isActive($company->id))
                                    <a class="btn btn-sm btn-danger btn-round mb-5"
                                       href="{{ route('user.deactivate', ['user' => $user->id, 'company' => $company->id]) }}">
                                        Deactivate
                                    </a>
                                @else
                                    <a class="btn btn-sm btn-success btn-round mb-5"
                                       href="{{ route('user.activate', ['user' => $user->id, 'company' => $company->id]) }}">
                                        Activate
                                    </a>
                                @endif
                            @endif
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
