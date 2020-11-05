@extends('layouts.console', [
    'hasSidebar' => true
])

@section('head-styles')
    <link href="{{ asset('css/console.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.baseUrl = @json(route("campaign.response-console.index", ['campaign' => $campaign->id]).'/');
        window.leadTags = @json($leadTags);
        window.positiveTags = @json($positiveTags);
        window.negativeTags = @json($negativeTags);
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
        window.displayFilter = 'all';
        // URLs
        // window.getRecipientsUrl = "{{ route('campaign.recipient.for-user-display', ['campaign' => $campaign->id]) }}";
        // @fixme: setup to work with new LeadController for search
        window.getRecipientsUrl = "{{ route('lead.index', ['campaign' => $campaign->id]) }}";
        window.openLeadUrl = "{{ route('lead.open', ['campaign' => $campaign->id, 'lead' => ':leadId']) }}";
        window.closeLeadUrl = "{{ route('lead.close', ['campaign' => $campaign->id, 'lead' => ':leadId']) }}";
        window.reopenLeadUrl = "{{ route('lead.reopen', ['campaign' => $campaign->id, 'lead' => ':leadId']) }}";
        window.getResponsesUrl = "{{ route('campaign.recipient.responses', ['campaign' => $campaign->id, 'recipient' => ':recipientId']) }}";
        window.updateNotesUrl = "{{ route('recipient.update-notes', ['lead' => ':leadId']) }}";
        window.appointmentUpdateCalledStatusUrl = "{{ route('appointment.update-called-status', ['appointment' => ':appointmentId']) }}";
        window.addAppointmentUrl = "{{ route('add-appointment', ['campaign' => $campaign->id, 'recipient' => ':recipientId']) }}";
        window.messageUpdateReadStatusUrl = "{{ route('response.update-read-status', ['response' => ':responseId']) }}";
        window.sendTextUrl = "{{ route('campaign.recipient.text-response', ['campaign' => $campaign->id, 'recipient' => ':recipientId']) }}";
        window.sendEmailUrl = "{{ route('campaign.recipient.email-response', ['campaign' => $campaign->id, 'recipient' => ':recipientId']) }}";
        window.addLabelUrl = "{{ route('recipient.add-label', ['campaign' => $campaign->id, 'recipient' => ':recipientId']) }}";
        window.removeLabelUrl = "{{ route('recipient.remove-label', ['campaign' => $campaign->id, 'recipient' => ':recipientId']) }}";
        window.recipientGetResponsesUrl = "{{ route('recipient.get-responses', ['campaign' => $campaign->id, 'recipient' => ':recipientId']) }}";
        window.sendCrmUrl = "{{ route('recipient.send-to-crm', ['lead' => ':recipientId']) }}";
        window.sendServiceUrl = "{{ route('recipient.send-to-service', ['campaign' => $campaign->id, 'lead' => ':leadId']) }}";
        window.textToValueRequestedTag = @json($textToValueRequestedTag);
        window.checkedInTextToValueTag = @json($checkedInTextToValueTag);
    </script>
    {{--<script src="//js.pusher.com/4.3/pusher.min.js"></script>--}}
    <script src="{{ asset('js/console.js') }}"></script>
@endsection

@section('sidebar-content')
    <div class="logo">
        <img src="/img/logo-large.png" height="40px" class="d-none d-xl-block">
        <img src="/img/logo.png" height="40px" class="d-block d-xl-none">
    </div>
    <nav id="sidebar-nav-content" class="wrapper-aside--navigation" v-cloak>
        <h4>Campaign</h4>
        <ul class="list-group campaign-nav">
            <li class="list-group-item" v-if="!campaign.is_legacy">
                <a class="{{ \Route::current()->getName() === 'campaigns.edit' ? 'active' : '' }}" href="{{ route('campaigns.stats', ['campaign' => $campaign->id]) }}">
                    <i class="far fa-chart-bar"></i>
                    <span>STATS</span>
                </a>
            </li>
            @if($campaign->enable_facebook_campaign)
            <li>
                <a class="{{ \Route::current()->getName() === 'campaigns.facebook-campaign' ? 'active' : '' }}" href="{{ route('campaigns.facebook-campaign', ['campaign' => $campaign->id]) }}">
                    <i class="far fa-chart-bar"></i>
                    <span>FACEBOOK CAMPAIGN</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->isAdmin())
            <li class="list-group-item">
                <a class="{{ \Route::current()->getName() === 'campaigns.drops.index' ? 'active' : '' }}" href="{{ route('campaigns.drops.index', ['campaign' => $campaign->id]) }}">
                    <i class="pm-font-drops-icon"></i>
                    <span>DROPS</span>
                </a>
            </li>
            <li class="list-group-item">
                <a class="{{ \Route::current()->getName() === 'campaigns.recipient-lists.index' ? 'active' : '' }}" href="{{ route('campaigns.recipient-lists.index', ['campaign' => $campaign->id]) }}">
                    <i class="pm-font-recipients-icon"></i>
                    <span>RECIPIENTS</span>
                </a>
            </li>
            <li class="list-group-item">
                <a class="{{ \Route::current()->getName() === 'campaigns.responses.index' ? 'active' : '' }}" href="{{ route('campaigns.responses.index', ['campaign' => $campaign->id]) }}">
                    <i class="pm-font-responses-icon"></i>
                    <span>RESPONSES</span>
                </a>
            </li>
            <li class="list-group-item">
                <a class="{{ \Route::current()->getName() === 'campaigns.edit' ? 'active' : '' }}" href="{{ route('campaigns.edit', ['campaign' => $campaign->id]) }}">
                    <i class="pm-font-edit-icon"></i>
                    <span>EDIT</span>
                </a>
            </li>
            @endif
        </ul>
        <h4>Leads</h4>
        <ul class="filter filter-nav">
            <li class="all-leads">
                <a class="all-filter" :class="{'active': activeFilterSection === 'all'}" href="javascript:;"
                   @click="changeFilter('reset')"><i class="fas fa-list"></i>
                    <span>All Leads</span>
                    <span class="counter">@{{ counters.total }}</span></a>
            </li>
            <li class="new-leads">
                <a class="unread-filter" :class="{'active': activeFilterSection === 'new'}" href="javascript:;"
                   @click="changeFilter('status', 'new')"><i class="far fa-star"></i>
                    <span>New Leads</span>
                    <span class="counter">@{{ counters.new }}</span></a>
            </li>
            <li class="open-leads">
                <a class="unread-filter" :class="{'active': activeFilterSection === 'open'}" href="javascript:;"
                   @click="changeFilter('status', 'open')"><i class="fa fa-address-card"></i>
                    <span>Open Leads</span>
                    <span class="counter">@{{ counters.open }}</span></a>
            </li>
            <li class="closed-leads">
                <a class="idle-filter" :class="{'active': activeFilterSection === 'closed'}" href="javascript:;"
                   @click="changeFilter('status', 'closed')"><i class="fa fa-bed"></i>
                    <span>Closed Leads</span>
                    <span class="counter">@{{ counters.closed }}</span></a>
            </li>
        </ul>

        <ul class="footer list-group">
            @impersonating
            <b-nav-item right href="{{ route('admin.impersonate-leave') }}">
                <i class="fas fa-sign-out-alt"></i>
            </b-nav-item>
            @endImpersonating
            <b-nav-item-dropdown class="profile" dropright variant="link" size="lg" no-caret>
                <template slot="button-content">
                    <img :src="loggedUser.image_url" alt="Avatar" class="avatar-image" v-if="loggedUser.image_url">
                    <div class="avatar-placeholder" v-if="!loggedUser.image_url">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-name">
                        <div>@{{ loggedUser.name || loggedUser.email }}</div>
                        @if (!auth()->user()->isAdmin())
                        <small>{{ get_active_company_model()->name }}</small>
                        @endif
                    </div>
                </template>
                @if (!auth()->user()->isAdmin() && auth()->user()->companies()->count() > 1)
                    <b-dropdown-item href="{{ route('selector.select-active-company') }}">Switch Company</b-dropdown-item>
                @endif
                <b-dropdown-item href="{{ route('profile.index') }}">Profile</b-dropdown-item>
                <b-dropdown-item @click="signout('{{ route('logout') }}')">Signout</b-dropdown-item>
            </b-nav-item-dropdown>
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

@section('main-content')
    <div id="console" class="container list-campaign-container" v-cloak>
        <div class="row">
            <div class="col-12 col-sm-7 col-lg-9 mb-3">
                <h3>{{ $campaign->name }}</h3>
            </div>
            <div class="col-12 col-sm-5 col-lg-3 mb-3">
                <input type="text" v-model="searchForm.search" class="form-control filter--search-box"
                           aria-describedby="search" placeholder="Search" @keypress.enter="fetchRecipients(1)">
            </div>
        </div>
        <div class="col-12 d-flex flex-wrap">
            <v-select class="mb-3 filter--v-select"
                      placeholder="Media"
                      :options="mediaOptions"
                      v-model="searchForm.media"
                      index="value"
                      @input="fetchRecipients(1)"
            ></v-select>
            <v-select class="mb-3 filter--v-select"
                      placeholder="Tags"
                      label="text"
                      :options="leadTags"
                      v-model="searchTags"
                      v-if="searchForm.status == 'closed'"
                      @input="fetchRecipients(1)"
                      multiple
                      taggable
            ></v-select>
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
                    <div class="status-icon">
                        <i class="fas fa-star mr-2" style="color: #ff0" v-if="recipient.status == 'New'"></i>
                        <i class="fas fa-door-open mr-2" style="color: #00e000" v-if="recipient.status == 'Open'"></i>
                        <i class="fas fa-door-closed mr-2" v-if="recipient.status == 'Closed'"></i>
                    </div>
                    <div class="status-text">@{{ recipient.status }}</div>
                </div>
                <div class="col-12 col-sm-5 col-md-6 no-gutters d-flex flex-column justify-content-center">
                    <div class="name-wrapper">
                        <strong>@{{ recipient.name }}</strong>
                        <div class="recipient-date mt-1" v-if="recipient.status === 'New'">@{{ recipient.last_status_changed_at | shortDate}}</div>
                        <div class="recipient-date mt-1" v-if="recipient.status !== 'New'">@{{ recipient.last_responded_at || recipient.last_status_changed_at | shortDate }}</div>
                    </div>
                    <div class="label-wrapper" v-if="recipient.labels" v-show="false">
                        <span v-for="(label, index) in recipient.labels" :class="index">@{{ label }}</span>
                    </div>
                </div>
                <div class="col-12 col-sm-5 col-md-4 no-gutters">
                    <div class="phone-email">
                        <div v-if="recipient.email"><i class="fa fa-envelope mr-2"></i> @{{ recipient.email }}</div>
                        <div v-if="recipient.phone"><i class="fa fa-phone mr-2"></i> @{{ recipient.phone }}</div>
                    </div>
                </div>
            </div>
        </div>
        <pm-pagination v-if="recipients.length > 0" class="mt-3" :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>

        <slideout-panel></slideout-panel>

        <b-modal ref="closeLeadModalRef"
                 id="close-lead-modal"
                 static="true"
                 title="Close Lead"
                 size="sm">
            <template v-slot:modal-header="">
                <h4>Close Lead</h4>
            </template>
                <div class="sentiment">
                    <button class="btn btn-success" @click="selectPositiveOutcome">
                        <i class="fa fa-thumbs-up"></i>
                    </button>
                    <button class="btn btn-danger" @click="selectNegativeOutcome"></i>
                        <i class="fa fa-thumbs-down"></i>
                    </button>
                </div>

                <div class="close-details" v-if="closeLeadForm.outcome">
                    <div v-if="closeLeadForm.outcome === 'positive'">
                        <b-form-checkbox
                            button
                            button-variant="info"
                            v-for="option in positiveOptions"
                            v-model="closeLeadForm.tags"
                            :key="option.name"
                            :value="option.name">
                            @{{ option.text }}
                        </b-form-checkbox>
                    </div>
                    <div v-if="closeLeadForm.outcome === 'negative'">
                        <b-form-checkbox
                            button
                            button-variant="info"
                            v-for="option in negativeOptions"
                            v-model="closeLeadForm.tags"
                            :key="option.name"
                            :value="option.name">
                            @{{ option.text }}
                        </b-form-checkbox>
                    </div>
                    <b-form-checkbox
                        button
                        button-variant="info"
                        v-model="closeLeadForm.tags"
                        v-if="closingLead.text_to_value_requested"
                        disabled
                        :key="textToValueRequestedTag.name"
                        :value="textToValueRequestedTag.name">
                        @{{ textToValueRequestedTag.text }}
                    </b-form-checkbox>
                    <b-form-checkbox
                        button
                        button-variant="info"
                        v-model="closeLeadForm.tags"
                        v-if="closingLead.checked_in"
                        disabled
                        :key="checkedInTextToValueTag.name"
                        :value="checkedInTextToValueTag.name">
                        @{{ checkedInTextToValueTag.text }}
                    </b-form-checkbox>
                </div>
            <template v-slot:modal-footer="">
                <button class="btn btn-secondary" @click="cancelCloseLead">
                    Cancel
                </button>
                <button class="btn btn-primary" @click="sendCloseForm" v-if="closeLeadForm.outcome && closeLeadForm.tags.length > 0">
                    Ok
                </button>
            </template>
        </b-modal>
    </div>
@endsection
