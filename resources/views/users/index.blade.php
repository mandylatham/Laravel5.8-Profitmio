@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/company-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchFormUrl = "{{ route('user.for-user-display') }}";
        window.getCompanyUrl = "{{ route('company.for-dropdown') }}";
        window.companySelected = @json($companySelected);
        window.q = @json($q);
    </script>
    <script src="{{ asset('js/user-index.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="user-index">
        <div class="row align-items-end no-gutters mb-md-3">
            <div class="col-12 col-sm-5 col-lg-3">
                <div class="form-group filter--form-group">
                    <label>Filter By Company</label>
                    <v-select :options="companies" label="name" v-model="companySelected" class="filter--v-select" @input="onCompanySelected"></v-select>
                </div>
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3">
                <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" @keyup.enter="fetchData()">
            </div>
        </div>
        <div class="row align-items-end no-gutters">
            <div class="col-12">
                <pm-responsive-table :rows="users" :columns="columnData" :pagination="pagination" :is-loading="isLoading" @page-changed="onPageChanged">
                    <template slot="id" slot-scope="{row}">
                        <span class="iser-id">ID: @{{ row.id }}</span>
                        <span class="user-name">@{{ row.first_name }} @{{ row.last_name }}</span>
                    </template>
                    <template slot="options" slot-scope="{row}">
                        <a class="btn btn-link pm-btn-link pm-btn-link-warning" href="">
                            <edit-2-icon></edit-2-icon>
                        </a>
                        <a href="" class="btn btn-link pm-btn-link pm-btn-link-danger">
                            <trash-icon></trash-icon>
                        </a>
                    </template>
                </pm-responsive-table>
            </div>
        </div>
    </div>
@endsection
{{--@extends('layouts.remark')--}}

{{--@section('header')--}}
    {{--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">--}}
    {{--<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">--}}
    {{--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">--}}
{{--@endsection--}}

{{--@section('content')--}}
    {{--<div class="page">--}}
        {{--<div class="page-header container-fluid">--}}
            {{--<div class="row-fluid">--}}
                {{--<div class="col-xxl-8 offset-xxl-2 col-md-12">--}}
                    {{--<h3 class="page-title text-default">--}}
                        {{--Users--}}
                    {{--</h3>--}}
                    {{--<div class="page-header-actions">--}}
                        {{--<a href="{{ route('user.create') }}"--}}
                           {{--class="btn btn-sm btn-success waves-effect">--}}
                            {{--<i class="icon md-plus" aria-hidden="true"></i>--}}
                            {{--New User--}}
                        {{--</a>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="page-content container-fluid">--}}
            {{--<div class="row-fluid" data-plugin="matchHeight" data-by-row="true">--}}
                {{--<div class="col-xxl-8 offset-xxl-2 col-md-12">--}}
                    {{--<div class="panel panel-info">--}}
                        {{--<div class="panel-body">--}}
                            {{--@if (auth()->user()->isAdmin())--}}
                                {{--<form method="get" action="{{ route('user.index') }}">--}}
                                    {{--<div class="form-group floating">--}}
                                        {{--<label class="floating-label" for="company">Filter By Company</label>--}}
                                        {{--<select class="form-control" name="company" required onchange="this.form.submit()">--}}
                                            {{--<option value="" {{ !$selectedCompanyId ? 'selected' : '' }}>All Companies</option>--}}
                                            {{--@foreach ($companies as $company)--}}
                                                {{--<option value="{{ $company->id }}" {{ $company->id == $selectedCompanyId ? 'selected' : '' }}>{{ $company->name }}</option>--}}
                                            {{--@endforeach--}}
                                        {{--</select>--}}
                                    {{--</div>--}}
                                {{--</form>--}}
                            {{--@endif--}}
                            {{--<div class="table-responsive">--}}
                                {{--<table id="users" class="table table-striped table-hover datatable">--}}
                                    {{--<thead>--}}
                                    {{--<tr>--}}
                                        {{--<th>ID</th>--}}
                                        {{--<th>First Name</th>--}}
                                        {{--<th>Last Name</th>--}}
                                        {{--@if (!auth()->user()->isAdmin() || $selectedCompanyId)--}}
                                            {{--<th>Type</th>--}}
                                        {{--@else--}}
                                            {{--<th>Company / Type</th>--}}
                                        {{--@endif--}}
                                        {{--<th>Username</th>--}}
                                        {{--<th>Email</th>--}}
                                        {{--<th>Phone Number</th>--}}
                                        {{--@if(!auth()->user()->isAdmin() || (auth()->user()->isAdmin() && $selectedCompanyId))--}}
                                            {{--<th>Status</th>--}}
                                        {{--@endif--}}
                                        {{--<th>Actions</th>--}}
                                    {{--</tr>--}}
                                    {{--</thead>--}}
                                    {{--<tbody>--}}
                                    {{--@foreach($users as $user)--}}
                                        {{--<tr>--}}
                                            {{--<td class="id-row v-center"><strong>{{ $user->id }}</strong></td>--}}
                                            {{--<td class="v-center">{{ $user->first_name }}</td>--}}
                                            {{--<td class="v-center">{{ $user->last_name }}</td>--}}
                                            {{--@if ($user->isAdmin())--}}
                                                {{--<td class="text-capitalize v-center">@role('site_admin')</td>--}}
                                            {{--@elseif (!auth()->user()->isAdmin() || $selectedCompanyId)--}}
                                                {{--<td class="text-capitalize v-center">@role($user->pivot->role)</td>--}}
                                            {{--@else--}}
                                                {{--<td class="text-capitalize v-center">--}}
                                                    {{--<ul>--}}
                                                        {{--@foreach ($user->companies as $company)--}}
                                                        {{--<li>{{ $company->name }} @role($user->getRole($company))</li>--}}
                                                        {{--@endforeach--}}
                                                    {{--</ul>--}}
                                                {{--</td>--}}
                                            {{--@endif--}}
                                            {{--<td class="v-center">{{ $user->username }}</td>--}}
                                            {{--<td class="v-center">{{ $user->email }}</td>--}}
                                            {{--<td class="v-center">{{ $user->phone_number }}</td>--}}
                                            {{--@if(!auth()->user()->isAdmin())--}}
                                                {{--<td class="v-center text-center">@status($user->isActive($company->id))</td>--}}
                                            {{--@elseif(auth()->user()->isAdmin() && $selectedCompanyId)--}}
                                                {{--<td class="v-center text-center">@status($user->isActive($selectedCompanyId))</td>--}}
                                            {{--@endif--}}
                                            {{--<td>--}}
                                                {{--@if (auth()->user()->isAdmin() || !$user->isAdmin())--}}
                                                {{--<a class="btn btn-sm btn-warning btn-round mb-5"--}}
                                                   {{--href="{{ route('user.edit', ['user' => $user->id]) }}">--}}
                                                    {{--Edit--}}
                                                {{--</a>--}}
                                                {{--@endif--}}
                                                {{--@if (auth()->user()->isAdmin() && !$user->isAdmin() && $user->hasActiveCompanies())--}}
                                                    {{--<a class="btn btn-sm btn-success btn-round mb-5"--}}
                                                       {{--href="{{ route('admin.impersonate', ['user' => $user->id]) }}">--}}
                                                        {{--Impersonate--}}
                                                    {{--</a>--}}
                                                {{--@endif--}}
                                                {{--@if(auth()->user()->isAdmin() && !$user->isAdmin() && $user->hasPendingInvitations())--}}
                                                    {{--<a class="btn btn-link mb-5"--}}
                                                       {{--href="{{ route('user.edit', ['user' => $user->id]) }}">--}}
                                                        {{--Has Pending Invitations--}}
                                                    {{--</a>--}}
                                                {{--@endif--}}
                                                {{--@if (!auth()->user()->isAdmin() && !$user->isCompanyProfileReady($company))--}}
                                                    {{--<a class="btn btn-sm btn-primary btn-round mb-5"--}}
                                                       {{--href="{{ route('admin.resend-invitation', ['user' => $user->id, 'company' => $company->id ]) }}">--}}
                                                        {{--Re-send Invitation--}}
                                                    {{--</a>--}}
                                                {{--@endif--}}
                                                {{--@if((!auth()->user()->isAdmin() || (auth()->user()->isAdmin() && $selectedCompanyId)) && !$user->isAdmin())--}}
                                                    {{--@php--}}
                                                        {{--$companyIdToUser = auth()->user()->isAdmin() && $selectedCompanyId ? $selectedCompanyId : $company->id;--}}
                                                    {{--@endphp--}}
                                                    {{--@if($user->isActive($companyIdToUser))--}}
                                                        {{--<a class="btn btn-sm btn-danger btn-round mb-5"--}}
                                                           {{--href="{{ route('user.deactivate', ['user' => $user->id, 'company' => $companyIdToUser]) }}">--}}
                                                            {{--Deactivate--}}
                                                        {{--</a>--}}
                                                    {{--@else--}}
                                                        {{--<a class="btn btn-sm btn-success btn-round mb-5"--}}
                                                           {{--href="{{ route('user.activate', ['user' => $user->id, 'company' => $companyIdToUser]) }}">--}}
                                                            {{--Activate--}}
                                                        {{--</a>--}}
                                                    {{--@endif--}}
                                                {{--@endif--}}
                                            {{--</td>--}}
                                        {{--</tr>--}}
                                    {{--@endforeach--}}
                                    {{--</tbody>--}}
                                {{--</table>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--@endsection--}}

{{--@section('scriptTags')--}}
    {{--<script src="{{ secure_url('js/Plugin/material.js') }}"></script>--}}
    {{--<script src="{{ secure_url('js/Plugin/sweetalert.min.js') }}"></script>--}}

    {{--<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>--}}

    {{--<script type="text/javascript">--}}
        {{--$(document).ready(function () {--}}
            {{--$(".datatable").DataTable({"order": [[0, "desc"]]});--}}
        {{--});--}}
    {{--</script>--}}
{{--@endsection--}}

{{--@section('scripts')--}}
{{--@endsection--}}

