@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    @if (auth()->user()->isAdmin())
        <link href="{{ asset('css/site-admin-user-index.css') }}" rel="stylesheet">
    @else
        <link href="{{ asset('css/company-admin-user-index.css') }}" rel="stylesheet">
    @endif
@endsection

@section('body-script')
    <script>
        window.searchFormUrl = "{{ route('user.for-user-display') }}";
        window.getCompanyUrl = "{{ route('company.for-dropdown') }}";
        window.companySelected = @json($companySelected);
        window.userEditUrl = "{{ route('user.view', ['user' => ':userId']) }}";
        window.userImpersonateUrl = "{{ route('admin.impersonate', ['user' => ':userId']) }}";
        window.userActivateUrl = "{{ route('user.activate', ['user' => ':userId']) }}";
        window.userDeactivateUrl = "{{ route('user.deactivate', ['user' => ':userId']) }}";
        window.q = @json($q);
    </script>
    @if (auth()->user()->isAdmin())
        <script src="{{ asset('js/site-admin-user-index.js') }}"></script>
    @else
        <script src="{{ asset('js/company-admin-user-index.js') }}"></script>
    @endif
@endsection

@section('main-content')
    @if (auth()->user()->isAdmin())
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
                    <pm-responsive-table :rows="users" :columns="columnData" :pagination="pagination" :disable-toggle="true" :is-loading="isLoading" @page-changed="onPageChanged">
                        <template slot="id" slot-scope="{row: user}">
                            <span class="user-id">ID: @{{ user.id }}</span>
                            <span class="user-name">@{{ user.first_name }} @{{ user.last_name }}</span>
                            <user-role class="ml-2" :role="'site_admin'" v-if="user.is_admin"></user-role>
                        </template>
                        <template slot="phone_number" slot-scope="{row}">
                            <span class="pm-font-phone-icon mr-2"></span>@{{ row.phone_number || '--' }}
                        </template>
                        <template slot="companies" slot-scope="{row: user}">
                            <span v-if="user.is_admin">--</span>
                            <ul class="companies" v-if="!user.is_admin">
                                <li v-for="company in user.companies">@{{ company.name }} <user-role class="ml-2" :role="company.role"></user-role></li>
                            </ul>
                        </template>
                        <template slot="options" slot-scope="{row: user}">
                            <a :href="generateRoute(userEditUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-warning" title="Edit" v-if="!user.is_admin">
                                <i class="far fa-edit"></i>
                            </a>
                            <a :href="generateRoute(userImpersonateUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-blue" title="Impersonate" v-if="!user.is_admin && user.has_active_companies">
                                <i class="far fa-eye"></i>
                            </a>
                            <a :href="generateRoute(userEditUrl, {'userId': user.id})" class="btn btn-link" title="Has Pending Invitations" v-if="!user.is_admin && user.has_pending_invitations">
                                <i class="fas fa-envelope"></i>
                            </a>
                            @if ($companySelected)
                            <a :href="generateRoute(userActivateUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-green" title="Activate" v-if="!user.is_admin && isActiveInCompany(user, @json($companySelected->id))">
                                <i class="far fa-check-circle"></i>
                            </a>
                            <a :href="generateRoute(userDeactivateUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-danger" title="Deactivate" v-if="!user.is_admin && !isActiveInCompany(user, @json($companySelected->id))">
                                <i class="far fa-times-circle"></i>
                            </a>
                            @endif
                        </template>
                    </pm-responsive-table>
                </div>
            </div>
        </div>
    @else
        <div class="container" id="company-user-index">
            <div class="row align-items-end no-gutters mb-md-3">
                <div class="col-12 col-sm-5 col-lg-3">
                </div>
                <div class="col-none col-sm-2 col-lg-6"></div>
                <div class="col-12 col-sm-5 col-lg-3">
                    <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                           placeholder="Search" @keyup.enter="fetchData()">
                </div>
            </div>
            <div class="row align-items-end no-gutters">
                <div class="col-12">
                    <pm-responsive-table :rows="users" :columns="columnData" :pagination="pagination" :disable-toggle="true" :is-loading="isLoading" @page-changed="onPageChanged">
                        <template slot="id" slot-scope="{row: user}">
                            <span class="user-id">ID: @{{ user.id }}</span>
                            <span class="user-name">@{{ user.first_name }} @{{ user.last_name }}</span>
                        </template>
                        <template slot="type" slot-scope="{row: user}">
                            <user-role class="ml-2" :role="user.role"></user-role>
                        </template>
                        <template slot="mail" slot-scope="{row: user}">
                            <span class="pm-font-mail-icon mr-2"></span>@{{ user.mail || '--' }}
                        </template>
                        <template slot="phone_number" slot-scope="{row}">
                            <span class="pm-font-phone-icon mr-2"></span>@{{ row.phone_number || '--' }}
                        </template>
                        <template slot="status" slot-scope="{row: user}">
                            <user-status :status="user.status"></user-status>
                        </template>
                        <template slot="options" slot-scope="{row: user}">
                            <a :href="generateRoute(userEditUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-warning" title="Edit" v-if="!user.is_admin">
                                <i class="far fa-edit"></i>
                            </a>
                            <a :href="generateRoute(userImpersonateUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-blue" title="Impersonate" v-if="!user.is_admin && user.has_active_companies">
                                <i class="far fa-eye"></i>
                            </a>
                            <a :href="generateRoute(userEditUrl, {'userId': user.id})" class="btn btn-link" title="Has Pending Invitations" v-if="!user.is_admin && user.has_pending_invitations">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <a :href="generateRoute(userActivateUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-green" title="Activate" v-if="!user.is_admin && isActiveInCompany(user, @json($companySelected->id))">
                                <i class="far fa-check-circle"></i>
                            </a>
                            <a :href="generateRoute(userDeactivateUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-danger" title="Deactivate" v-if="!user.is_admin && !isActiveInCompany(user, @json($companySelected->id))">
                                <i class="far fa-times-circle"></i>
                            </a>
                        </template>
                    </pm-responsive-table>
                </div>
            </div>
        </div>
    @endif
@endsection
