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
        window.pusherKey = "{{env('PUSHER_APP_KEY')}}";
    </script>
    <script src="//js.pusher.com/4.3/pusher.min.js"></script>
    <script src="{{ asset('js/console.js') }}"></script>
@endsection

@section('sidebar-content')
    <nav id="sidebar-nav-content" class="wrapper-aside--navigation">
        <ul class="filter">
            <li class="all">
                <a :class="{'active': activeFilterSection === 'all'}" href="javascript:;"
                   @click="changeFilter('all')"><i class="fas fa-expand-arrows-alt"></i> All
                    <span class="counter">@{{ this.counters.totalCount }}</span></a>
            </li>
            <li class="unread">
                <a :class="{'active': activeFilterSection === 'unread'}" href="javascript:;"
                   @click="changeFilter('unread')"><i class="far fa-flag"></i> Unread
                    <span class="counter">@{{ this.counters.unread }}</span></a>
            </li>
            <li class="idle">
                <a :class="{'active': activeFilterSection === 'idle'}" href="javascript:;"
                   @click="changeFilter('idle')"><i class="far fa-hourglass"></i> Idle
                    <span class="counter">@{{ this.counters.idle }}</span></a>
            </li>
        </ul>

        <hr>
        <h4>Media</h4>

        <ul class="media-type">
            <li class="calls">
                <a :class="{'active': activeFilterSection === 'calls'}" href="javascript:;"
                   @click="changeFilter('calls')"><i class="fas fa-phone"></i> Calls
                    <span class="counter">@{{ this.counters.calls }}</span></a>
            </li>
            <li class="email">
                <a :class="{'active': activeFilterSection === 'email'}" href="javascript:;"
                   @click="changeFilter('email')"><i class="far fa-envelope"></i> Email
                    <span class="counter">@{{ this.counters.email }}</span></a>
            </li>
            <li class="sms">
                <a :class="{'active': activeFilterSection === 'sms'}" href="javascript:;"
                   @click="changeFilter('sms')"><i class="far fa-comment-alt"></i> SMS
                    <span class="counter">@{{ this.counters.sms }}</span></a>
            </li>
        </ul>

        <hr>
        <h4>Labels</h4>

        <ul class="labels">
            <li class="no-label">
                <a :class="{'active': activeLabelSection === 'no-label'}" href="javascript:;"
                   @click="changeFilter('labelled', 'no-label')">No Label
                    <span class="counter">@{{ this.labelCounts.not_labelled }}</span></a>
            </li>
            <li class="interested">
                <a :class="{'active': activeLabelSection === 'interested'}" href="javascript:;"
                   @click="changeFilter('labelled', 'interested')">Interested
                    <span class="counter">@{{ this.labelCounts.interested }}</span></a>
            </li>
            <li class="appointment">
                <a :class="{'active': activeLabelSection === 'appointment'}" href="javascript:;"
                   @click="changeFilter('labelled', 'appointment')">Appointment
                    <span class="counter">@{{ this.labelCounts.appointment }}</span></a>
            </li>
            <li class="callback">
                <a :class="{'active': activeLabelSection === 'callback'}" href="javascript:;"
                   @click="changeFilter('labelled', 'callback')">Callback
                    <span class="counter">@{{ this.labelCounts.callback }}</span></a>
            </li>
            <li class="service-dept">
                <a :class="{'active': activeLabelSection === 'service-dept'}" href="javascript:;"
                   @click="changeFilter('labelled', 'service-dept')">Service Dept
                    <span class="counter">@{{ this.labelCounts.service }}</span></a>
            </li>
            <li class="not-interested">
                <a :class="{'active': activeLabelSection === 'not-interested'}" href="javascript:;"
                   @click="changeFilter('labelled', 'not-interested')">Not Interested
                    <span class="counter">@{{ this.labelCounts.not_interested }}</span></a>
            </li>
            <li class="wrong-tag">
                <a :class="{'active': activeLabelSection === 'wrong-tag'}" href="javascript:;"
                   @click="changeFilter('labelled', 'wrong-tag')">Wrong #
                    <span class="counter">@{{ this.labelCounts.wrong_number }}</span></a>
            </li>
        </ul>
    </nav>
@endsection

@section('main-content')
    <div id="console" class="container-fluid list-campaign-container">
        <div class="row align-items-end no-gutters mb-4 ">
            <div class="col-12 col-sm-5 col-lg-3">
                <a href="{{ route('campaign.index') }}" class="btn pm-btn pm-btn-blue">
                    <i class="fas fa-chevron-left mr-2"></i>Home</a>
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3 search-wrapper">
                <div class="clearable">
                    <input type="text" v-model="searchForm.search" class="form-control filter--search-box"
                           aria-describedby="search" placeholder="Search" @keypress.enter="fetchRecipients">
                    <i class="clearable__clear" :class="{'show': searchForm.search}" @click="clearSearch">&times;</i>
                </div>
            </div>
        </div>

        <div class="loader-spinner" v-if="loading">
            <spinner-icon></spinner-icon>
        </div>

        <div id="recipients-list" v-if="recipients.length">
            <div class="row no-gutters" v-for="recipient in recipients" @click="showPanel(recipient)">
                <div class="col-4">
                    <span>@{{ recipient.name }}</span>
                </div>
                <div class="col-4">
                    <span>@{{ recipient.email }}</span>
                </div>
                <div class="col-4">
                    <span>@{{ recipient.last_seen_ago }}</span>
                </div>
            </div>
        </div>
        <div id="recipients-list" v-else>
            <p>No recipients found.</p>
        </div>

        <slideout-panel></slideout-panel>
    </div>
@endsection
