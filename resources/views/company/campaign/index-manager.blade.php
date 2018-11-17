@extends('layouts.remark_company-manager')

@section('header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">
@endsection

@section('company_content')
    <div class="col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-hover datatable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Agency</th>
                    <th>Dealership</th>
                    <th><i class="fa fa-fw fa-user text-primary sr-hidden"></i> <span class="sr-only">Targets</span>
                    </th>
                    <th><i class="fa fa-fw fa-phone text-primary sr-hidden"></i> <span class="sr-only">Phones</span>
                    </th>
                    <th><i class="fa fa-fw fa-envelope text-primary sr-hidden"></i> <span class="sr-only">Emails</span>
                    </th>
                    <th><i class="fa fa-fw fa-commenting text-primary sr-hidden"></i> <span
                            class="sr-only">SMS/MMS</span></th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($campaigns as $campaign)
                    <tr>
                        <td class="id-row v-center"><strong>{{ $campaign->id }}</strong></td>
                        <td class="v-center">{{ $campaign->getNameForTemplate() }}</td>
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
