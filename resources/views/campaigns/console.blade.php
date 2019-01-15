@extends('layouts.base', [
    'hasSidebar' => true
])

@section('head-styles')
    <link href="{{ asset('css/console.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.filter = @json($filter);
        window.label = @json($label);
        window.counters = @json($counters);
        window.campaign = @json($campaign);
        window.getRecipientsUrl = "{{ route('campaign.recipient.for-user-display', ['campaign' => $campaign->id]) }}";
        window.user = @json(auth()->user());
    </script>
    <script src="{{ asset('js/console.js') }}"></script>
@endsection

@section('sidebar-content')
    <nav id="sidebar-nav-content" class="wrapper-aside--navigation">
        <ul class="filter">
            <li class="all">
                <a :class="{'active': activeFilterSection === 'all'}" href="javascript:;"
                   @click="changeFilter('all')">All
                    <span class="counter">@{{ this.counters.totalCount }}</span></a>
            </li>
            <li class="unread">
                <a :class="{'active': activeFilterSection === 'unread'}" href="javascript:;"
                   @click="changeFilter('unread')">Unread
                    <span class="counter">@{{ this.counters.unread }}</span></a>
            </li>
            <li class="idle">
                <a :class="{'active': activeFilterSection === 'idle'}" href="javascript:;"
                   @click="changeFilter('idle')">Idle
                    <span class="counter">@{{ this.counters.idle }}</span></a>
            </li>
        </ul>

        <hr>
        <h4>Media</h4>

        <ul class="media-type">
            <li class="calls">
                <a :class="{'active': activeMediaTypeSection === 'calls'}" href="javascript:;"
                   @click="changeMediaType('calls')">Calls
                    <span class="counter">@{{ this.counters.calls }}</span></a>
            </li>
            <li class="email">
                <a :class="{'active': activeMediaTypeSection === 'email'}" href="javascript:;"
                   @click="changeMediaType('email')">Email
                    <span class="counter">@{{ this.counters.email }}</span></a>
            </li>
            <li class="sms">
                <a :class="{'active': activeMediaTypeSection === 'sms'}" href="javascript:;"
                   @click="changeMediaType('sms')">SMS
                    <span class="counter">@{{ this.counters.sms }}</span></a>
            </li>
        </ul>

        <hr>
        <h4>Labels</h4>

        <ul class="labels">
            <li class="no-label">
                <a :class="{'active': activeLabelSection === 'no-label'}" href="javascript:;"
                   @click="changeLabel('no-label')">No Label</a>
            </li>
            <li class="interested">
                <a :class="{'active': activeLabelSection === 'interested'}" href="javascript:;"
                   @click="changeLabel('interested')">Interested</a>
            </li>
            <li class="appointment">
                <a :class="{'active': activeLabelSection === 'appointment'}" href="javascript:;"
                   @click="changeLabel('appointment')">Appointment</a>
            </li>
            <li class="callback">
                <a :class="{'active': activeLabelSection === 'callback'}" href="javascript:;"
                   @click="changeLabel('callback')">Callback</a>
            </li>
            <li class="service-dept">
                <a :class="{'active': activeLabelSection === 'service-dept'}" href="javascript:;"
                   @click="changeLabel('service-dept')">Service Dept</a>
            </li>
            <li class="not-interested">
                <a :class="{'active': activeLabelSection === 'not-interested'}" href="javascript:;"
                   @click="changeLabel('not-interested')">Not Interested</a>
            </li>
            <li class="wrong-tag">
                <a :class="{'active': activeLabelSection === 'wrong-tag'}" href="javascript:;"
                   @click="changeLabel('wrong-tag')">Wrong #</a>
            </li>
        </ul>
    </nav>
@endsection

@section('main-content')
    <div id="console" class="container-fluid list-campaign-container">
        <div class="row align-items-end no-gutters">
            <div class="col-12 col-sm-5 col-lg-3">
                <button class="btn pm-btn pm-btn-blue">
                    <i class="fas fa-chevron-left mr-2"></i>
                    Home
                </button>
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3">
                <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search">
            </div>
        </div>

        <div class="row align-items-end no-gutters mt-4 mb-3">
            <div class="col-12">
                <a class="icon" href="javascript:;"><img src="../../../../img/icons/folder.png" alt="folder"></a>
                <a class="icon" href="javascript:;"><img src="../../../../img/icons/tag.png" alt="tag"></a>
            </div>
        </div>

        {{-- TODO: check if we should use this.recipients.data or this.recipients --}}

        <div class="loader-spinner" v-if="loading">
            <spinner-icon></spinner-icon>
        </div>
        <div class="row no-gutters" v-for="recipient in recipients">
            <div class="col-4 col-md-2">
                <span>@{{ recipient.name }}</span>
            </div>
            <div class="col-4 col-md-2">
                <span>Email</span>
                <span>@{{ recipient.email }}</span>
            </div>
            <div class="col-4 col-md-3">
                <span>Email</span>
                <span>@{{ recipient.last_seen_ago }}</span>
            </div>
        </div>

        <slideout-panel></slideout-panel>
    </div>
@endsection
