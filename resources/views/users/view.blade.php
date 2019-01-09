@extends('layouts.base', [
    'hasSidebar' => true
])

@section('head-styles')
    <link href="{{ asset('css/user-view.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchCampaignFormUrl = "{{ route('campaign.for-user-display') }}";
        window.getCompanyUrl = "{{ route('company.for-dropdown') }}";
        window.campaignCompanySelected = @json($campaignCompanySelected);
        window.user = @json($user);
        window.q = @json($q);
    </script>
    <script src="{{ asset('js/user-view.js') }}"></script>
@endsection

@section('sidebar-content')
    <div class="avatar">
        <div class="avatar--image"></div>
        <a href="" class="avagar--edit"></a>
    </div>
    <form class="form" method="post" action="{{ route('user.update', ['user' => $user->id]) }}">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="first_name" class="floating-label">First Name</label>
            <input type="text" class="form-control empty" name="first_name" placeholder="First Name"
                   value="{{ old('first_name') ?? $user->first_name }}" required>
        </div>
        <div class="form-group">
            <label for="last_name" class="floating-label">Last Name</label>
            <input type="text" class="form-control empty" name="last_name" placeholder="Last Name"
                   value="{{ old('last_name') ?? $user->last_name }}" required>
        </div>
        <div class="form-group">
            <label for="username" class="floating-label">Username</label>
            <input type="text" class="form-control empty" name="username" placeholder="Username"
                   value="{{ old('username') ?? $user->username }}" required>
        </div>
        <button class="btn pm-btn" type="button">Delete User</button>
    </form>
@endsection

@section('main-content')
    <div class="container" id="user-view">
        <b-card no-body>
            <b-tabs card>
                <b-tab title="CAMPAIGN" active>
                    <div class="row align-items-end no-gutters mb-md-3">
                        <div class="col-12 col-sm-5 col-lg-3">
                            <div class="form-group filter--form-group">
                                <label>Filter By Company</label>
                                <v-select :options="companies" label="name" v-model="campaignCompanySelected" class="filter--v-select" @input="onCampaignCompanySelected"></v-select>
                            </div>
                        </div>
                        <div class="col-none col-sm-2 col-lg-6"></div>
                        <div class="col-12 col-sm-5 col-lg-3">
                            <input type="text" v-model="searchCampaignForm.q" class="form-control filter--search-box" aria-describedby="search"
                                   placeholder="Search" @keyup.enter="fetchCampaigns">
                        </div>
                    </div>
                    <div class="row align-items-end no-gutters mt-3">
                        <div class="col-12">
                            <div class="loader-spinner" v-if="loadingCampaigns">
                                <spinner-icon></spinner-icon>
                            </div>
                            <div class="campaign-group-label" v-if="countActiveCampaigns > 0">ACTIVE</div>
                            <campaign v-for="campaign in campaigns" v-if="campaign.status === 'Active'" :key="campaign.id" :campaign="campaign"></campaign>
                            <div class="campaign-group-label" v-if="countInactiveCampaigns > 0">INACTIVE</div>
                            <campaign v-for="campaign in campaigns" v-if="campaign.status !== 'Active'" :key="campaign.id" :campaign="campaign"></campaign>
                            <pm-pagination :pagination="campaignsPagination" @page-changed="onCampaignPageChanged"></pm-pagination>
                        </div>
                    </div>
                </b-tab>
                <b-tab title="COMPANY">
                </b-tab>
            </b-tabs>
        </b-card>
    </div>
@endsection
