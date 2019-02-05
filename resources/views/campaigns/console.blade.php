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
        window.user = @json(auth()->user());
        window.pusherKey = "{{env('PUSHER_APP_KEY')}}";
        window.pusherCluster = "{{env('PUSHER_CLUSTER')}}";
        window.pusherAuthEndpoint = "{{ url('/broadcasting/auth') }}";
        window.csrfToken = "{{ csrf_token() }}";
        // URLs
        window.getRecipientsUrl = "{{ route('campaign.recipient.for-user-display', ['campaign' => $campaign->id]) }}";
        window.getResponsesUrl = "{{ route('campaign.recipient.responses', ['campaign' => $campaign->id, 'recipient' => ':recipientId']) }}";
        window.updateNotesUrl = "{{ route('recipient.update-notes', ['recipient' => ':recipientId']) }}";
        window.appointmentUpdateCalledStatusUrl = "{{ route('appointment.update-called-status', ['appointment' => ':appointmentId']) }}";
        window.addAppointmentUrl = "{{ route('add-appointment', ['campaign' => $campaign->id, 'recipient' => ':recipientId']) }}";
        window.messageUpdateReadStatusUrl = "{{ route('response.update-read-status', ['response' => ':responseId']) }}";
        window.sendTextUrl = "{{ route('campaign.recipient.text-response', ['campaign' => $campaign->id, 'recipient' => ':recipientId']) }}";
        window.sendEmailUrl = "{{ route('campaign.recipient.email-response', ['campaign' => $campaign->id, 'recipient' => ':recipientId']) }}";
        window.addLabelUrl = "{{ route('recipient.add-label', ['recipient' => ':recipientId']) }}";
        window.removeLabelUrl = "{{ route('recipient.remove-label', ['recipient' => ':recipientId']) }}";
        window.recipientGetResponsesUrl = "{{ route('recipient.get-responses', ['recipient' => ':recipientId']) }}";
    </script>
    <script src="//js.pusher.com/4.3/pusher.min.js"></script>
    <script src="{{ asset('js/console.js') }}"></script>
@endsection

@section('sidebar-content')
    <nav id="sidebar-nav-content" class="wrapper-aside--navigation" v-cloak>
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
                   @click="changeFilter('labelled', 'none')">No Label
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

@section('sidebar-toggle-content')
    <i class="fas fa-bars mr-2"></i> Filters
@endsection

@section('main-content')
    <div id="console" class="container-fluid list-campaign-container" v-cloak>
        <div class="row align-items-end no-gutters">
            <div class="col-12 offset-sm-7 col-sm-5 offset-lg-9 col-lg-3 search-wrapper mb-3">
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

        <div id="recipients-list" class="container-fluid" v-if="recipients.length">
            <div class="row" v-for="(recipient, key) in recipients" @click="showPanel(recipient, key)">
                <div class="col-12 col-md-1 align-items-center">
                    @{{ recipient.last_seen_ago }}
                </div>
                <div class="col-12 col-md-5">
                    <div class="name-wrapper">
                        <strong>@{{ recipient.name }}</strong>
                    </div>
                    <div class="label-wrapper" v-if="recipient.labels">
                        <span v-for="(label, index) in recipient.labels" :class="index">@{{ label }}</span>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div v-if="recipient.email"><i class="fa fa-envelope mr-2"></i> @{{ recipient.email }}</div>
                    <div v-if="recipient.phone"><i class="fa fa-phone mr-2"></i> @{{ recipient.phone }}</div>
                </div>
            </div>
        </div>
        <div id="recipients-list" v-else>
            <p>No recipients found.</p>
        </div>

        <slideout-panel></slideout-panel>
    </div>
@endsection
