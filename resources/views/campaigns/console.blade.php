@extends('layouts.base', [
    'hasSidebar' => true
])

@section('head-styles')
    <link href="{{ asset('css/console.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.counters = @json($counters);
        window.campaign = @json($campaign);
        window.user = @json(auth()->user());
        window.pusherKey = "{{env('PUSHER_APP_KEY')}}";
        window.pusherCluster = "{{env('PUSHER_APP_CLUSTER')}}";
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
    {{--<script src="//js.pusher.com/4.3/pusher.min.js"></script>--}}
    <script src="{{ asset('js/console.js') }}"></script>
@endsection

@section('sidebar-content')
    <nav id="sidebar-nav-content" class="wrapper-aside--navigation" v-cloak>
        <ul class="filter">
            <li class="all">
                <a :class="{'active': activeFilterSection === 'all'}" href="javascript:;"
                   @click="changeFilter('filter', 'all')"><i class="fas fa-expand-arrows-alt"></i> All
                    <span class="counter">@{{ counters.total }}</span></a>
            </li>
            <li class="unread">
                <a :class="{'active': activeFilterSection === 'unread'}" href="javascript:;"
                   @click="changeFilter('filter', 'unread')"><i class="far fa-flag"></i> Unread
                    <span class="counter">@{{ counters.unread }}</span></a>
            </li>
            <li class="idle">
                <a :class="{'active': activeFilterSection === 'idle'}" href="javascript:;"
                   @click="changeFilter('filter', 'idle')"><i class="far fa-hourglass"></i> Idle
                    <span class="counter">@{{ counters.idle }}</span></a>
            </li>
        </ul>

        <hr>
        <h4>Media</h4>

        <ul class="media-type">
            <li class="calls">
                <a :class="{'active': activeFilterSection === 'calls'}" href="javascript:;"
                   @click="changeFilter('filter', 'calls')"><i class="fas fa-phone"></i> Calls
                    <span class="counter">@{{ counters.calls }}</span></a>
            </li>
            <li class="email">
                <a :class="{'active': activeFilterSection === 'email'}" href="javascript:;"
                   @click="changeFilter('filter', 'email')"><i class="far fa-envelope"></i> Email
                    <span class="counter">@{{ counters.email }}</span></a>
            </li>
            <li class="sms">
                <a :class="{'active': activeFilterSection === 'sms'}" href="javascript:;"
                   @click="changeFilter('filter', 'sms')"><i class="far fa-comment-alt"></i> SMS
                    <span class="counter">@{{ counters.sms }}</span></a>
            </li>
        </ul>

        <hr>
        <h4>Labels</h4>

        <ul class="labels">
            <li class="no-label">
                <a :class="{'active': activeFilterSection === 'none'}" href="javascript:;"
                   @click="changeFilter('filter', 'none')">No Label
                    <span class="counter">@{{ counters.none }}</span></a>
            </li>
            <li class="interested">
                <a :class="{'active': activeFilterSection === 'interested'}" href="javascript:;"
                   @click="changeFilter('filter', 'interested')">Interested
                    <span class="counter">@{{ counters.interested }}</span></a>
            </li>
            <li class="appointment">
                <a :class="{'active': activeFilterSection === 'appointment'}" href="javascript:;"
                   @click="changeFilter('filter', 'appointment')">Appointment
                    <span class="counter">@{{ counters.appointment }}</span></a>
            </li>
            <li class="callback">
                <a :class="{'active': activeFilterSection === 'callback'}" href="javascript:;"
                   @click="changeFilter('filter', 'callback')">Callback
                    <span class="counter">@{{ counters.callback }}</span></a>
            </li>
            <li class="service-dept">
                <a :class="{'active': activeFilterSection === 'service'}" href="javascript:;"
                   @click="changeFilter('filter', 'service')">Service Dept
                    <span class="counter">@{{ counters.service }}</span></a>
            </li>
            <li class="not-interested">
                <a :class="{'active': activeFilterSection === 'not_interested'}" href="javascript:;"
                   @click="changeFilter('filter', 'not_interested')">Not Interested
                    <span class="counter">@{{ counters.not_interested }}</span></a>
            </li>
            <li class="wrong-tag">
                <a :class="{'active': activeFilterSection === 'wrong_number'}" href="javascript:;"
                   @click="changeFilter('filter', 'wrong_number')">Wrong #
                    <span class="counter">@{{ counters.wrong_number }}</span></a>
            </li>
            <li class="wrong-tag">
                <a :class="{'active': activeFilterSection === 'car_sold'}" href="javascript:;"
                   @click="changeFilter('filter', 'car_sold')">Car Sold
                    <span class="counter">@{{ counters.car_sold }}</span></a>
            </li>
            <li class="wrong-tag">
                <a :class="{'active': activeFilterSection === 'heat'}" href="javascript:;"
                   @click="changeFilter('filter', 'heat')">Heat Case
                    <span class="counter">@{{ counters.heat }}</span></a>
            </li>
        </ul>
    </nav>
@endsection

@section('sidebar-toggle-content')
    <i class="fas fa-bars mr-2"></i> Filters
@endsection

@section('main-content')
    <div id="console" class="container-fluid list-campaign-container" v-cloak>
        <div class="row">
            <div class="col-12 col-sm-5 col-lg-3 mb-3">
                <a class="btn pm-btn go-back" href="{{ auth()->user()->isAdmin() ? route('campaigns.index') : route('dashboard') }}">
                    <i class="fas fa-arrow-circle-left mr-2"></i> Go Back
                </a>
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3 mb-3">
                <input type="text" v-model="searchForm.search" class="form-control filter--search-box"
                           aria-describedby="search" placeholder="Search" @keypress.enter="fetchRecipients">
            </div>
        </div>
        <div class="no-items-row" v-if="recipients.length === 0">
            No recipients found.
        </div>
        <div class="table-loader-spinner" v-if="loading">
            <spinner-icon></spinner-icon>
        </div>
        <div class="recipient-row" v-for="(recipient, key) in recipients" @click="showPanel(recipient, key)">
            <div class="row no-gutters">
                <div class="col-12 col-md-5 d-flex flex-column justify-content-center">
                    <div class="name-wrapper">
                        <strong>@{{ recipient.name }}</strong>
                    </div>
                    <div class="label-wrapper" v-if="recipient.labels">
                        <span v-for="(label, index) in recipient.labels" :class="index">@{{ label }}</span>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="phone-email">
                        <div v-if="recipient.email"><i class="fa fa-envelope mr-2"></i> @{{ recipient.email }}</div>
                        <div v-if="recipient.phone"><i class="fa fa-phone mr-2"></i> @{{ recipient.phone }}</div>
                    </div>
                </div>
                <div class="col-12 col-md-3 text-center d-flex flex-column justify-content-center">
                    @{{ recipient.last_responded_at | mUtcParse('YYYY-MM-DD HH:mm:ss') | mDurationForHumans('MM/DD/YYYY hh:mm A') }}
                </div>
            </div>
        </div>
        <pm-pagination v-if="recipients.length > 0" class="mt-3" :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>

        <slideout-panel></slideout-panel>
    </div>
@endsection
