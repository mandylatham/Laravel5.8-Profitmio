@extends('layouts.remark_company-manager')

@section('header')
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.min.css') }}">
@endsection

@section('company_content')
    <div class="col-xs-12">
        <div class="row">
            <div class="col-md-6 offset-md-6">
                <form action="{{ route('company.campaign.index', ['company' => $company->id]) }}" method="get">
                    <div class="input-search">
                        <i class="input-search-icon md-search" aria-hidden="true"></i>
                        <input type="text" class="form-control" name="q" placeholder="Search..." value="{{ request('q') }}">
                        <button type="button"
                                @if (request('q'))
                                onClick="window.location.href = ' {{ route('company.campaign.index', ['company' => $company->id, 'q' => '']) }}'"
                                @endif
                                class="input-search-close icon md-close" aria-label="Close"></button>
                    </div>
                </form>
            </div>
        </div>
        @if (count($campaigns) > 0)
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
                        <th><i class="fa fa-fw fa-envelope text-primary sr-hidden"></i> <span
                                class="sr-only">Emails</span>
                        </th>
                        <th><i class="fa fa-fw fa-commenting text-primary sr-hidden"></i> <span
                                class="sr-only">SMS/MMS</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($campaigns as $campaign)
                        <tr>
                            <td class="id-row v-center"><strong>{{ $campaign->id }}</strong></td>
                            <td class="v-center">{!! $campaign->getNameForTemplate() !!}</td>
                            <td class="v-center">{{ $campaign->agency->name }}</td>
                            <td class="v-center">{{ $campaign->dealership->name }}</td>
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
        @else
            <div class="alert alert-info mt-20"><p>No campaigns to show</p></div>
        @endif
    </div>
@endsection

@section('scriptTags')
    <script type="text/javascript" src="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".delete-button").click(function () {
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
                    function () {
                        $.get(
                            url,
                            function (data) {
                                swal("All Done", "Campaign Deleted!", "success");
                                location.reload();
                            }
                        );
                    });
            });
        });
    </script>
@endsection
