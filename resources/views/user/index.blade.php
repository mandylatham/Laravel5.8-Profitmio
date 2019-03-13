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
        window.userEditUrl = "{{ route('user.view', ['user' => ':userId']) }}";
        window.userImpersonateUrl = "{{ route('admin.impersonate', ['user' => ':userId']) }}";
        @if (!auth()->user()->isAdmin())
        window.userActivateUrl = "{{ route('user.activate', ['user' => ':userId', 'company' => get_active_company()]) }}";
        window.userDeactivateUrl = "{{ route('user.deactivate', ['user' => ':userId', 'company' => get_active_company()]) }}";
        @endif
    </script>
    @if (auth()->user()->isAdmin())
        <script src="{{ asset('js/site-admin-user-index.js') }}"></script>
    @else
        <script src="{{ asset('js/company-admin-user-index.js') }}"></script>
    @endif
@endsection

@section('main-content')
    @if (auth()->user()->isAdmin())
        <div class="container" id="user-index" v-cloak>
            <div class="row">
                <div class="col">
                    <a class="btn pm-btn pm-btn-blue float-right" href="{{ route('user.create') }}"><i class="fas fa-plus mr-3"></i>New User</a>
                </div>
            </div>
            <div class="row align-items-end mb-3">
                <div class="col-12 col-sm-5 col-lg-4">
                    <div class="form-group filter--form-group">
                        <label>Filter By Company</label>
                        <v-select :options="companies" label="name" v-model="companySelected" class="filter--v-select" @input="onCompanySelected"></v-select>
                    </div>
                </div>
                <div class="col-none col-sm-2 col-lg-4"></div>
                <div class="col-12 col-sm-5 col-lg-4">
                    <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                           placeholder="Search" @keyup.enter="fetchData()">
                </div>
            </div>
            <h1 class="page-title">Users</h1>
            <div class="list-container">
                <div class="loader-spinner" v-if="isLoading">
                    <spinner-icon></spinner-icon>
                </div>
                <div class="no-items-row" v-if="users.length === 0">
                    No Items
                </div>
                <div class="user-row" v-for="user in users">
                    <div class="row no-gutters">
                        <div class="col-12 col-md-8 col-xl-3">
                            <div class="user-row--id justify-content-center justify-content-xl-start">
                                <strong class="mr-2">ID: @{{ user.id }}</strong>
                                <span class="user-name" v-if="user.first_name && user.last_name">@{{ user.first_name }} @{{ user.last_name }}</span>
                                <span class="user-name" v-if="!user.first_name && !user.last_name">No Name</span>
                                <user-role class="ml-3" :role="'site_admin'" v-if="user.is_admin"></user-role>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-xl-2">
                            <div class="user-row--companies justify-content-center justify-content-xl-start">
                                <div>Active Companies <span class="ml-2 counter">@{{ user.active_companies }}</span></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="user-row--email justify-content-center justify-content-xl-start">
                                <i class="fas fa-envelope mr-2"></i>@{{ user.email || '--' }}
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-2">
                            <div class="user-row--phone-number justify-content-center justify-content-xl-start">
                                <span class="pm-font-phone-icon mr-2"></span>@{{ user.phone_number || '--' }}
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 col-xl-2">
                            <div class="user-row--options justify-content-center align-items-xl-start">
                                <a :href="generateRoute(userEditUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-warning" title="Edit">
                                    <i class="far fa-eye mr-3"></i> View
                                </a>
                                <a :href="generateRoute(userImpersonateUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-blue" title="Impersonate" v-if="user.has_active_companies">
                                    <i class="fas fa-lock-open mr-3"></i> Impersonate
                                </a>
                                <a :href="generateRoute(userEditUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-black" title="Has Pending Invitations" v-if="user.has_pending_invitations">
                                    <i class="fas fa-envelope mr-3"></i> Has Pending Invitations
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <pm-pagination class="mt-3" :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
            </div>
        </div>
    @else
        <div class="container" id="company-user-index" v-cloak>
            <div class="row">
                <div class="col">
                    <a class="btn pm-btn pm-btn-blue float-left" href="{{ route('user.create') }}"><i class="fas fa-plus mr-3"></i>New User</a>
                </div>
            </div>
            <div class="row align-items-end mb-3">
                <div class="col-12 col-sm-5 col-lg-4">
                </div>
                <div class="col-none col-sm-2 col-lg-4"></div>
                <div class="col-12 col-sm-5 col-lg-4">
                    <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                           placeholder="Search" @keyup.enter="fetchData()">
                </div>
            </div>
            <div class="list-container">
                <div class="loader-spinner" v-if="isLoading">
                    <spinner-icon></spinner-icon>
                </div>
                <div class="no-items-row" v-if="users.length === 0">
                    No Items
                </div>
                <div class="user-row" v-for="user in users">
                    <div class="row no-gutters">
                        <div class="col-12 col-md-8 col-xl-3">
                            <div class="user-row--id justify-content-center justify-content-xl-">
                                <strong class="mr-2">ID: @{{ user.id }}</strong>
                                <span class="user-name">@{{ user.first_name }} @{{ user.last_name }}</span>
                                <user-status class="ml-3" :is-active="user.is_active"></user-status>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-xl-2">
                            <div class="user-row--role justify-content-center">
                                <user-role :role="user.role" :short-version="false"></user-role>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="user-row--email justify-content-center justify-content-xl-start">
                                <i class="fas fa-envelope mr-2"></i>@{{ user.email || '--' }}
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-2">
                            <div class="user-row--phone-number justify-content-center justify-content-xl-start">
                                <span class="pm-font-phone-icon mr-2"></span>@{{ user.phone_number || '--' }}
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 col-xl-2">
                            <div class="user-row--options justify-content-center align-items-xl-start">
                                <a :href="generateRoute(userEditUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-warning" title="Edit" v-if="user.role !== 'site_admin'">
                                    <i class="far fa-eye mr-3"></i> View
                                </a>
                                <a :href="generateRoute(userEditUrl, {'userId': user.id})" class="btn btn-link pm-btn-link pm-btn-link-black" title="Has Pending Invitations" v-if="user.role !== 'site_admin' && user.has_pending_invitations">
                                    <i class="fas fa-envelope mr-3"></i> Has Pending Invitations
                                </a>
                                <a href="javascript:;" @click="activateUser(user)" class="btn btn-link pm-btn-link pm-btn-link-green" title="Activate" v-if="!user.is_active">
                                    <i class="far fa-check-circle mr-3"></i> Activate
                                </a>
                                <a href="javascript:;" @click="deactivateUser(user)" class="btn btn-link pm-btn-link pm-btn-link-danger" title="Deactivate" v-if="user.is_active">
                                    <i class="far fa-times-circle mr-3"></i> Deactivate
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <pm-pagination class="mt-3" :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
            </div>
        </div>
    @endif
@endsection
