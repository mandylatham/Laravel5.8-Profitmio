@extends('layouts.base', [
    'hasSidebar' => true
])

@section('head-styles')
    <link href="{{ asset('css/console.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.baseUrl = @json(route("campaign.response-console.index", ['campaign' => $campaign->id]).'/');
        window.counters = @json($counters);
        window.campaign = @json($campaign);
        window.user = @json(auth()->user());
        @if (!auth()->user()->isAdmin())
            window.activeCompany = @json(\App\Models\Company::findOrFail(get_active_company()));
        @endif
        @if (isset($filterApplied))
        window.filterApplied = @json($filterApplied);
        @endif
        window.pusherKey = "{{env('PUSHER_APP_KEY')}}";
        window.pusherCluster = "{{env('PUSHER_APP_CLUSTER')}}";
        window.pusherAuthEndpoint = "{{ url('/broadcasting/auth') }}";
        window.csrfToken = "{{ csrf_token() }}";
        // URLs
        // window.getRecipientsUrl = "{{ route('campaign.recipient.for-user-display', ['campaign' => $campaign->id]) }}";
        // @fixme: setup to work with new LeadController for search
        window.getRecipientsUrl = "{{ route('test', ['campaign' => $campaign->id]) }}";
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
        window.sendCrmUrl = "{{ route('recipient.send-to-crm', ['recipient' => ':recipientId']) }}";
    </script>
    {{--<script src="//js.pusher.com/4.3/pusher.min.js"></script>--}}
    <script src="{{ asset('js/console.js') }}"></script>
@endsection

@section('sidebar-content')
    <nav id="sidebar-nav-content" class="wrapper-aside--navigation" v-cloak>
        <ul class="filter">
            <li class="all-leads">
                <a class="all-filter" :class="{'active': activeFilterSection === 'all'}" href="javascript:;"
                   @click="changeFilter('reset')"><i class="fas fa-expand-arrows-alt"></i> All Leads
                    <span class="counter">@{{ counters.total }}</span></a>
            </li>
            <li class="new-leads">
                <a class="unread-filter" :class="{'active': activeFilterSection === 'new'}" href="javascript:;"
                   @click="changeFilter('status', 'new')"><i class="far fa-flag"></i> New Leads
                    <span class="counter">@{{ counters.new }}</span></a>
            </li>
            <li class="open-leads">
                <a class="unread-filter" :class="{'active': activeFilterSection === 'open'}" href="javascript:;"
                   @click="changeFilter('status', 'open')"><i class="far fa-flag"></i> Open Leads
                    <span class="counter">@{{ counters.open }}</span></a>
            </li>
            <li class="closed-leads">
                <a class="idle-filter" :class="{'active': activeFilterSection === 'closed'}" href="javascript:;"
                   @click="changeFilter('status', 'closed')"><i class="far fa-hourglass"></i> Closed Leads
                    <span class="counter">@{{ counters.closed }}</span></a>
            </li>
        </ul>

<!-- NOT SHOWING THESE RIGHT NOW
        <hr>
        <h4>Media</h4>

        <ul class="media-type">
            <li class="calls">
                <a class="call-filter" :class="{'active': activeFilterSection === 'calls'}" href="javascript:;"
                   @click="changeFilter('media', 'phone')"><i class="fas fa-phone"></i> Calls
                    <span class="counter">@{{ counters.calls }}</span></a>
            </li>
            <li class="email">
                <a class="email-filter" :class="{'active': activeFilterSection === 'email'}" href="javascript:;"
                   @click="changeFilter('media', 'email')"><i class="far fa-envelope"></i> Email
                    <span class="counter">@{{ counters.email }}</span></a>
            </li>
            <li class="sms">
                <a class="sms-filter" :class="{'active': activeFilterSection === 'sms'}" href="javascript:;"
                   @click="changeFilter('media', 'text')"><i class="far fa-comment-alt"></i> SMS
                    <span class="counter">@{{ counters.sms }}</span></a>
            </li>
        </ul>

        <hr>
        <h4>Labels</h4>

        <ul class="labels">
            <li class="no-label">
                <a class="none-filter" :class="{'active': activeFilterSection === 'none'}" href="javascript:;"
                   @click="changeFilter('filter', 'none')">
                    <span class="label no-label">No Label</span>
                    <span class="counter">@{{ counters.none }}</span></a>
            </li>
            <li class="interested">
                <a class="interested-filter" :class="{'active': activeFilterSection === 'interested'}" href="javascript:;"
                   @click="changeFilter('filter', 'interested')">
                    <span class="label interested">Interested</span>
                    <span class="counter">@{{ counters.interested }}</span></a>
            </li>
            <li class="appointment">
                <a class="appointment-filter" :class="{'active': activeFilterSection === 'appointment'}" href="javascript:;"
                   @click="changeFilter('filter', 'appointment')">
                    <span class="label appointment">Appointment</span>
                    <span class="counter">@{{ counters.appointment }}</span></a>
            </li>
            <li class="callback">
                <a class="callback-filter" :class="{'active': activeFilterSection === 'callback'}" href="javascript:;"
                   @click="changeFilter('filter', 'callback')">
                    <span class="label callback">Callback</span>
                    <span class="counter">@{{ counters.callback }}</span></a>
            </li>
            <li class="service-dept">
                <a class="service-filter" :class="{'active': activeFilterSection === 'service'}" href="javascript:;"
                   @click="changeFilter('filter', 'service')">
                    <span class="label service-dept">Service Dept</span>
                    <span class="counter">@{{ counters.service }}</span></a>
            </li>
            <li class="not-interested">
                <a class="not_interested-filter" :class="{'active': activeFilterSection === 'not_interested'}" href="javascript:;"
                   @click="changeFilter('filter', 'not_interested')">
                    <span class="label not-interested">Not Interested</span>
                    <span class="counter">@{{ counters.not_interested }}</span></a>
            </li>
            <li class="wrong-tag">
                <a class="wrong_number-filter" :class="{'active': activeFilterSection === 'wrong_number'}" href="javascript:;"
                   @click="changeFilter('filter', 'wrong_number')">
                    <span class="label wrong-number">Wrong #</span>
                    <span class="counter">@{{ counters.wrong_number }}</span></a>
            </li>
            <li class="wrong-tag">
                <a class="car_sold-filter" :class="{'active': activeFilterSection === 'car_sold'}" href="javascript:;"
                   @click="changeFilter('filter', 'car_sold')">
                    <span class="label car-sold">Car Sold</span>
                    <span class="counter">@{{ counters.car_sold }}</span></a>
            </li>
            <li class="wrong-tag">
                <a class="heat-filter" :class="{'active': activeFilterSection === 'heat'}" href="javascript:;"
                   @click="changeFilter('filter', 'heat')">
                    <span class="label heat-case">Heat Case</span>
                    <span class="counter">@{{ counters.heat }}</span></a>
            </li>
        </ul>
-->
    </nav>
@endsection

@section('sidebar-toggle-content')
    <i class="fas fa-bars mr-2"></i> Filters
@endsection

@section('main-content')
    <div id="console" class="container list-campaign-container" v-cloak>
        <div class="row">
            <div class="col-12 col-sm-5 col-lg-3 mb-3">
                <a class="btn pm-btn go-back" href="{{ auth()->user()->isAdmin() ? route('campaigns.index') : route('dashboard') }}">
                    <i class="fas fa-arrow-circle-left mr-2"></i> Go Back
                </a>
            </div>
            <div class="col-none col-sm-2 col-lg-6" style="text-align:center"><h3>{{ $campaign->name }}</div>
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
        <div class="recipient-row container" v-for="(recipient, key) in recipients" @click="showPanel(recipient, key)">
            <div class="row no-gutters">
                <div class="col-12 col-sm-2 lead-status no-gutters">
                    <i class="fas fa-star mr-2" style="color: #ff0" v-if="recipient.status == 'New'"></i>
                    <i class="fas fa-door-open mr-2" style="color: #00e000" v-if="recipient.status == 'Open'"></i>
                    <i class="fas fa-door-closed mr-2" v-if="recipient.status == 'Closed'"></i>
                    @{{ recipient.status }}
                </div>
                <div class="col-12 col-sm-7 no-gutters d-flex flex-column justify-content-center">
                    <div class="name-wrapper">
                        <strong>@{{ recipient.name }}</strong>
                    </div>
                    <div class="label-wrapper" v-if="recipient.labels">
                        <span v-for="(label, index) in recipient.labels" :class="index">@{{ label }}</span>
                    </div>
                </div>
                <div class="col-12 col-sm-3 no-gutters">
                    <div class="phone-email">
                        <div v-if="recipient.email"><i class="fa fa-envelope mr-2"></i> @{{ recipient.email }}</div>
                        <div v-if="recipient.phone"><i class="fa fa-phone mr-2"></i> @{{ recipient.phone }}</div>
                    </div>
                </div>
            </div>
        </div>
        <pm-pagination v-if="recipients.length > 0" class="mt-3" :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>

        <slideout-panel></slideout-panel>
    </div>
@endsection
