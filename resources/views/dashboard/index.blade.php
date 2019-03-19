@extends('layouts.base', [
    'hasSidebar' => true
])

@section('head-styles')
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.campaignEditUrl = "{{ route('campaigns.edit', ['campaign' => ':campaignId']) }}";
        window.campaignStatsUrl = "{{ route('campaigns.stats', ['campaign' => ':campaignId']) }}";
        window.appointmentsUrl = "{{ route('appointment.for-calendar-display') }}";
        window.campaignViewUrl = "{{ route('campaigns.view', ['campaign' => ':campaignId']) }}";
        window.campaignDropIndex = "{{ route('campaigns.drops.index', ['campaign' => ':campaignId']) }}";
        window.campaignRecipientIndex = "{{ route('campaigns.recipient-lists.index', ['campaign' => ':campaignId']) }}";
        window.campaignResponseConsoleIndex = "{{ route('campaign.response-console.index', ['campaign' => ':campaignId']) }}";
        window.dropsUrl = "{{ route('drop.for-calendar-display') }}";
        window.getCompanyUrl = "{{ route('company.for-dropdown') }}";
        window.isAdmin = @json(auth()->user()->isAdmin());
        window.searchFormUrl = "{{ route('campaign.for-user-display') }}";
    </script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection

@section('sidebar-content')
    <div id="sidebar--container" v-cloak>
        <div class="calendar-filters">
            <p-radio @change="fetchDayEvents() && fetchMonthEvents()" class="p-default p-round" name="filter" v-model="filter" value="appointment">Appointments</p-radio>
            <p-radio @change="fetchDayEvents() && fetchMonthEvents()" class="p-default p-round" name="filter" v-model="filter" value="drop">Scheduled Drops</p-radio>
        </div>
        <div class="calendar-container mt-5">
            <date-pick class="event-calendar" :events="monthEvents" :parse-date="parseDate" :start-week-on-sunday="true" v-model="selectedDate" :has-input-element="false"></date-pick>
            <div class="table-loader-spinner" v-if="loading">
                <spinner-icon></spinner-icon>
            </div>
        </div>
        <div class="events">
            <header>
                <span class="date">@{{ selectedDate | amDateFormat('DD') }}</span>
                <span class="label">@{{ selectedDate | amDateFormat('MMMM Do, YYYY') }}</span>
            </header>
            <div class="event-list">
                <div class="event appointment" v-if="filter === 'appointment'" v-for="e in calendarEvents">
                    <div class="title">Campaign @{{ e.campaign_id }}</div>
                    <div class="info">@{{ e.first_name }} @{{ e.last_name }}</div>
                    <div>@{{ e.appointment_at_formatted }}</div>
                </div>
                <div class="event drop" v-if="filter === 'drop'" v-for="e in calendarEvents">
                    <div class="title">Campaign @{{ e.campaign_id }}</div>
                    <div class="info">@{{ e.type }} Drop</div>
                    <div>@{{ e.send_at | amDateTimeFormat('YYYY-MM-DD h:mm A') }}</div>
                </div>
                <div class="no-events" v-if="calendarEvents.length === 0">No Events.</div>
            </div>
        </div>
    </div>
@endsection

@section('main-content')
    <div class="container pt-3 pt-md-5" id="dashboard">
        <div class="row align-items-end no-gutters mb-md-3">
            <div class="col-12 col-sm-5 col-lg-3">
                <div class="form-group filter--form-group" v-if="companies.length > 1">
                    <label>Filter By Company</label>
                    <v-select :options="companies" label="name" v-model="companySelected" class="filter--v-select" @input="onCompanySelected"></v-select>
                </div>
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3">
                <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" @keyup.enter="fetchData">
            </div>
        </div>
        <div class="row align-items-end no-gutters mt-3">
            <div class="col-12">
                <div class="loader-spinner" v-if="isLoading">
                    <spinner-icon></spinner-icon>
                </div>
                <div class="no-items-row" v-if="countActiveCampaigns === 0 && countInactiveCampaigns === 0">
                    No Items
                </div>
                <div class="campaign-group-label" v-if="countActiveCampaigns > 0">ACTIVE</div>
                <campaign v-for="campaign in campaigns" v-if="campaign.status === 'Active'" :key="campaign.id" :campaign="campaign"></campaign>
                <div class="campaign-group-label" v-if="countInactiveCampaigns > 0">INACTIVE</div>
                <campaign v-for="campaign in campaigns" v-if="campaign.status !== 'Active'" :key="campaign.id" :campaign="campaign"></campaign>
                <pm-pagination :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
            </div>
        </div>
    </div>
@endsection
