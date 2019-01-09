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
        {{ csrf_field() }}
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control empty" name="first_name" placeholder="First Name"
                   value="{{ old('first_name') ?? $user->first_name }}" required v-if="enableInputs">
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
                </b-tab>
            </b-tabs>
        </b-card>
    </div>
@endsection
