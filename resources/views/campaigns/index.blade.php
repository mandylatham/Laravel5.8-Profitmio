@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/campaign-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchFormUrl = "{{ route('campaign.for-user-display') }}";
        window.getCompanyUrl = "{{ route('company.for-dropdown') }}";
        window.campaignEditUrl = "{{ route('campaigns.edit', ['campaign' => ':campaignId']) }}";
        window.campaignStatsUrl = "{{ route('campaigns.stats', ['campaign' => ':campaignId']) }}";
        window.campaignDropIndex = "{{ route('campaigns.drops.index', ['campaign' => ':campaignId']) }}";
        window.campaignRecipientIndex = "{{ route('campaigns.recipient-lists.index', ['campaign' => ':campaignId']) }}";
        window.campaignResponseConsoleIndex = "{{ route('campaign.response-console.index', ['campaign' => ':campaignId']) }}";
        window.isAdmin = @json(auth()->user()->isAdmin());
    </script>
    <script src="{{ asset('js/campaign-index.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="campaign-index" v-cloak>
        <div class="row">
            <div class="col">
                <a dusk="create-campaign-button" href="{{ route('campaigns.create') }}" class="btn pm-btn pm-btn-blue mb-3">
                    <i class="fas fa-plus mr-3"></i>New Campaign
                </a>
            </div>
        </div>
        <div class="row align-items-end no-gutters">
            <div class="col-12 col-sm-5 col-lg-3 mb-3">
                <div class="form-group filter--form-group">
                    <label>Filter By Company</label>
                    <v-select :options="companies" label="name" v-model="companySelected" class="filter--v-select" @input="onCompanySelected"></v-select>
                </div>
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3 mb-3">
                <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" @keyup.enter="fetchData">
            </div>
        </div>
        <div class="row align-items-end no-gutters mt-3">
            <div class="col-12">
                <div class="loader-spinner" v-if="isLoading">
                    <spinner-icon></spinner-icon>
                </div>
                <h1 class="page-title">Campaigns</h1>
                <div class="campaign-group-label" v-if="countActiveCampaigns > 0">ACTIVE</div>
                <campaign v-for="campaign in campaigns" v-if="campaign.status === 'Active'" :key="campaign.id" :campaign="campaign"></campaign>
                <div class="campaign-group-label" v-if="countInactiveCampaigns > 0">INACTIVE</div>
                <campaign v-for="campaign in campaigns" v-if="campaign.status !== 'Active'" :key="campaign.id" :campaign="campaign"></campaign>
                <pm-pagination :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
            </div>
        </div>
    </div>
@endsection
