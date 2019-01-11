@extends('layouts.base', [
    'hasSidebar' => true
])

@section('head-styles')
    <link href="{{ asset('css/user-view.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchCampaignFormUrl = "{{ route('campaign.for-user-display') }}";
        window.searchCompaniesFormUrl = "{{ route('company.for-user-display') }}";
        window.getCompanyUrl = "{{ route('company.for-dropdown') }}";
        window.campaignCompanySelected = @json($campaignCompanySelected);
        window.user = @json($user);
        window.q = @json($q);
    </script>
    <script src="{{ asset('js/user-view.js') }}"></script>
@endsection

@section('sidebar-toggle-content')
    <i class="fas fa-chevron-circle-left mr-2"></i>User Details
@endsection

@section('sidebar-content')
    <div class="avatar">
        <div class="avatar--image">
            <button class="avatar--edit" v-if="enableInputs">
                <i class="fas fa-pencil-alt"></i>
            </button>
        </div>
    </div>
    <button class="btn pm-btn pm-btn-blue edit-user" @click="enableInputs = !enableInputs">
        <i class="fas fa-pencil-alt"></i>
    </button>
    <button class="btn pm-btn pm-btn-danger delete-user" type="button"><i class="fas fa-trash-alt"></i></button>
    <form class="clearfix" class="form" method="post" action="{{ route('user.update', ['user' => $user->id]) }}">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control empty" name="first_name" placeholder="First Name" v-model="editUserForm.first_name" required v-if="enableInputs">
            <p class="form-control panel-data" v-if="!enableInputs">{{ $user->first_name }}</p>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" name="last_name" placeholder="Last Name"
                   value="{{ old('last_name') ?? $user->last_name }}" required v-if="enableInputs">
            <p class="form-control panel-data" v-if="!enableInputs">{{ $user->last_name }}</p>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" class="form-control" name="email" placeholder="Email"
                   value="{{ old('email') ?? $user->email }}" required v-if="enableInputs">
            <p class="form-control panel-data" v-if="!enableInputs">{{ $user->email }}</p>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" class="form-control" name="phone_number" placeholder="Phone Number"
                   value="{{ old('phone_number') ?? $user->phone_number }}" required v-if="enableInputs">
            <p class="form-control panel-data" v-if="!enableInputs">{{ $user->phone_number }}</p>
        </div>
        <button class="btn pm-btn pm-btn-purple float-left" type="button" v-if="enableInputs"><i class="fas fa-save mr-2"></i>Save</button>
    </form>
@endsection

@section('main-content')
    <div class="container" id="user-view">
        <b-card no-body>
            <b-tabs card>
                <b-tab title="CAMPAIGN" active>
                    <div class="row align-items-end no-gutters mb-md-3" v-if="countActiveCampaigns > 0 || countInactiveCampaigns > 0">
                        <div class="col-12 col-sm-5 col-lg-4">
                            <div class="form-group filter--form-group">
                                <label>Filter By Company</label>
                                <v-select :options="companies" label="name" v-model="campaignCompanySelected" class="filter--v-select" @input="onCampaignCompanySelected"></v-select>
                            </div>
                        </div>
                        <div class="col-none col-sm-2 col-lg-4"></div>
                        <div class="col-12 col-sm-5 col-lg-4">
                            <input type="text" v-model="searchCampaignForm.q" class="form-control filter--search-box" aria-describedby="search"
                                   placeholder="Search" @keyup.enter="fetchCampaigns">
                        </div>
                    </div>
                    <div class="row align-items-end no-gutters mt-3">
                        <div class="col-12">
                            <div class="loader-spinner" v-if="loadingCampaigns">
                                <spinner-icon></spinner-icon>
                            </div>
                            <div class="no-items-row" v-if="countActiveCampaigns === 0 && countInactiveCampaigns === 0">
                                No Items
                            </div>
                            <div class="campaign-group-label" v-if="countActiveCampaigns > 0">ACTIVE</div>
                            <campaign v-for="campaign in campaigns" v-if="campaign.status === 'Active'" :key="campaign.id" :campaign="campaign"></campaign>
                            <div class="campaign-group-label" v-if="countInactiveCampaigns > 0">INACTIVE</div>
                            <campaign v-for="campaign in campaigns" v-if="campaign.status !== 'Active'" :key="campaign.id" :campaign="campaign"></campaign>
                            <pm-pagination v-if="countActiveCampaigns > 0 || countInactiveCampaigns > 0" :pagination="campaignsPagination" @page-changed="onCampaignPageChanged"></pm-pagination>
                        </div>
                    </div>
                </b-tab>
                <b-tab title="COMPANY">
                    <div class="row align-items-end no-gutters mb-md-3" v-if="countCompanies > 0">
                        <div class="col-12 col-sm-5 col-lg-4">
                        </div>
                        <div class="col-none col-sm-2 col-lg-4"></div>
                        <div class="col-12 col-sm-5 col-lg-4">
                            <input type="text" v-model="searchCompanyForm.q" class="form-control filter--search-box" aria-describedby="search"
                                   placeholder="Search" @keyup.enter="fetchCompanies">
                        </div>
                    </div>
                    <div class="row align-items-end no-gutters mt-3">
                        <div class="col">
                            <div class="loader-spinner" v-if="loadingCompanies">
                                <spinner-icon></spinner-icon>
                            </div>
                            <div class="no-items-row" v-if="countCompanies === 0">
                                No Items
                            </div>
                            {{--<div class="company" v-for="company in companiesForList">--}}
                                <div class="row" v-for="company in companiesForList">
                                    <div class="col col-md-6 company-info">
                                        <div class="company-info--image"></div>
                                        <div class="company-info--data">
                                            <strong>@{{ company.name }}</strong>
                                            <p>@{{ company.address }}</p>
                                        </div>
                                    </div>
                                    <div class="col-4 col-md-2 company-role">
                                        <v-select :options="roles" label="name" v-model="company.role" class="filter--v-select" @input="onRoleSelected"></v-select>
                                    </div>
                                    <div class="col-4 col-md-2 company-phone">
                                        <i class="pm-font-phone-icon mr-2"></i>@{{ company.phone_number || '--' }}
                                    </div>
                                    <div class="col-4 col-md-2 active-campaigns">
                                        <small>Active Campaigns</small>
                                        <div>
                                            <span class="pm-font-campaigns-icon"></span>
                                            <span class="badge active-campaigns--counter">3</span>
                                        </div>
                                    </div>
                                </div>
                            {{--</div>--}}
                            <pm-pagination v-if="countCompanies > 0" :pagination="companiesPagination" @page-changed="onCompanyPageChanged"></pm-pagination>
                        </div>
                    </div>
                </b-tab>
            </b-tabs>
        </b-card>
    </div>
@endsection

{{--@extends('layouts.remark')--}}

{{--@section('content')--}}
    {{--<div class="page">--}}
        {{--<div class="page-header container-fluid">--}}
            {{--<div class="row-fluid">--}}
                {{--<div class="col-md-6 offset-md-3">--}}
                    {{--<button type="button" role="button"--}}
                            {{--data-url="{{ route('user.index') }}"--}}
                            {{--class="btn btn-sm float-left btn-default waves-effect campaign-edit-button"--}}
                            {{--data-toggle="tooltip" data-original-title="Go Back"--}}
                            {{--style="margin-right: 15px; background: rgba(255, 255, 255, 0.2); border-size: 0.5px;">--}}
                        {{--<i class="icon fa-angle-left" style="color: #efefef" aria-hidden="true"></i>--}}
                    {{--</button>--}}
                    {{--<h3 class="page-title text-default">--}}
                        {{--New User--}}
                    {{--</h3>--}}
                    {{--<div class="page-header-actions">--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="page-content container-fluid">--}}
            {{--<div class="row-fluid" data-plugin="matchHeight" data-by-row="true">--}}
                {{--<div class="col-md-6 offset-md-3">--}}
                    {{--<div class="panel">--}}
                        {{--<div class="panel-body" data-fv-live="enabled">--}}
                            {{--@if ($errors->count() > 0)--}}
                                {{--<div class="alert alert-danger">--}}
                                    {{--<h3>There were some errors:</h3>--}}
                                    {{--<ul>--}}
                                        {{--@foreach ($errors->all() as $message)--}}
                                            {{--<li>{{ $message }}</li>--}}
                                        {{--@endforeach--}}
                                    {{--</ul>--}}
                                {{--</div>--}}
                            {{--@endif--}}
                            {{--<form class="form" method="post" action="{{ route('user.store') }}">--}}
                                {{--{{ csrf_field() }}--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="role" class="floating-label">Role</label>--}}
                                    {{--<select name="role" value="{{ old('role') }}" class="form-control" required id="js-role">--}}
                                        {{--<option selected disabled>Choose role...</option>--}}
                                        {{--@if (auth()->user()->isAdmin())--}}
                                        {{--<option value="site_admin" {{ old('role') === 'site_admin' ? 'selected' : '' }}>@role('site_admin')</option>--}}
                                        {{--@endif--}}
                                        {{--<option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>@role('admin')</option>--}}
                                        {{--<option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>@role('user')</option>--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="first_name" class="floating-label">First Name</label>--}}
                                    {{--<input type="text" class="form-control empty" name="first_name"--}}
                                           {{--placeholder="First Name"--}}
                                           {{--value="{{ old('first_name') }}" required>--}}
                                {{--</div>--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="last_name" class="floating-label">Last Name</label>--}}
                                    {{--<input type="text" class="form-control empty" name="last_name"--}}
                                           {{--placeholder="Last Name"--}}
                                           {{--value="{{ old('last_name') }}" required>--}}
                                {{--</div>--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="email" class="floating-label">Email</label>--}}
                                    {{--<input type="email" class="form-control empty" name="email" placeholder="Email"--}}
                                           {{--value="{{ old('email') }}" required>--}}
                                {{--</div>--}}
                                {{--@if (auth()->user()->isAdmin())--}}
                                {{--<div class="form-group" id="js-company-select">--}}
                                    {{--<label for="company" class="floating-label">Company</label>--}}
                                    {{--<select name="company" value="{{ old('company') }}" class="form-control" required>--}}
                                        {{--<option selected disabled>Choose a Company...</option>--}}
                                        {{--@foreach ($companies as $company)--}}
                                        {{--<option value="{{ $company->id }}" {{ old('company') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                                {{--@endif--}}
                                {{--<button type="submit" class="btn btn-success">Add User</button>--}}
                            {{--</form>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--@endsection--}}

{{--@section('scriptTags')--}}
{{--<script type="text/javascript">--}}
{{--$(function () {--}}
    {{--$('#js-role').on('change', function () {--}}
        {{--if (this.value === 'site_admin') {--}}
            {{--$('#js-company-select').hide();--}}
        {{--} else {--}}
            {{--$('#js-company-select').show();--}}
        {{--}--}}
    {{--});--}}
{{--});--}}
{{--</script>--}}
{{--@endsection--}}
