<template>
    <div class="container-fluid list-campaign-container">
        <div class="clearfix mt-3 mb-1">
            <button class="btn pm-btn pm-btn-blue float-right " v-on:click.prevent="closePanel">&times;</button>
        </div>

        <div class="table-loader-spinner" v-if="loading">
            <spinner-icon></spinner-icon>
        </div>

        <div class="content" :class="{'show': !loading}">
            <div class="row align-items-end no-gutters mb-3">
                <div class="col-12">
                    <div class="name text-purple"><h1>{{ recipient.name }}</h1>
                        <small v-if="recipient.location"><em>{{ recipient.location }}</em></small>
                    </div>
                </div>
                <div class="col-8">
                    <div class="vehicle" v-if="recipient.vehicle && recipient.vehicle.length > 0">
                        <i class="fas fa-car mr-2"></i>
                        {{ recipient.vehicle }}
                    </div>
                    <div class="email" v-if="recipient.email && recipient.email.length > 0">
                        <i class="fas fa-envelope mr-2"></i>
                        {{ recipient.email }}
                    </div>
                    <div class="phone" v-if="recipient.phone && recipient.phone.length > 0">
                        <i class="fas fa-phone mr-2"></i>
                        <a :href="this.phone_link">{{ recipient.phone }}</a>
                    </div>
                </div>
                <div class="col-4" v-show="false">
                    <b-dropdown right text="Add Label" :disabled="Object.keys(labelDropdownOptions).length === 0"
                                class="float-right" v-if="campaign.status == 'Active'">
                        <b-dropdown-item v-for="(label, index) in labelDropdownOptions" :key="index" :value="index"
                                         @click="addLabel(index, label)">{{ label }}
                        </b-dropdown-item>
                    </b-dropdown>
                </div>
                <div class="col-4" v-if="recipient.status == 'Closed'">
                    <button class="btn btn-secondary"
                            v-show="true"
                            style="width: 100%; font-size: 1.2rem;"
                            @click="reopenLead(recipient.id)">
                        ReOpen Lead
                    </button>
                    <p v-show="false" class="alert alert-secondary">Closed Lead</p>
                </div>
                <div class="col-4" v-if="recipient.status == 'Open'">
                    <button class="btn btn-warning"
                            style="width: 100%; font-size: 1.2rem;"
                            @click="closeLead(recipient)">
                        Close Lead
                    </button>
                </div>
                <div class="col-4 no-gutters" v-if="recipient.status == 'New'">
                    <button class="btn btn-success"
                            style="width: 100%; font-size: 1.2rem;"
                            @click="openLead(recipient.id)">
                        Open Lead
                    </button>
                </div>
            </div>

            <div class="row align-items-end no-gutters mt-4 mb-3" v-show="false" v-if="recipient.status != 'New' && Object.keys(labels).length > 0">
                <div class="col-12 labels-wrapper">
                    <ul class="labels">
                        <li :class="index" v-for="(label, index) in labels">{{ label }}<i
                            class="fas fa-times" @click="removeLabel(label, index)" v-if="index != 'appointment' && index != 'callback'"></i></li>
                    </ul>
                </div>
            </div>

            <div class="notes-wrapper" v-if="recipient.status != 'New'">
                <div class="form-group">
                    <textarea class="form-control" placeholder="Notes..." name="notes" rows="4" :class="recipient.status != 'Open' ? 'disabled' : ''" :disabled="recipient.status != 'Open'"
                              v-model="notes"></textarea>
                </div>
                <div class="form-group" v-if="campaign.status == 'Active'">
                    <button type="button" class="btn btn-primary" v-if="starting_notes !== notes" @click="addNotes(recipientId)">Save note</button>
                </div>
            </div>
            <div class="call-in-wrapper" v-if="recipient.status != 'New' && appointments.length">
                <ul class="list-group">
                    <li class="list-group-item" v-for="appointment in appointments">
                        <div v-if="appointment.type === 'callback'" class="alert" :class="{'alert-success': appointment.called_back, 'alert-warning': !appointment.called_back}">
                            <span class="btn recipient-action mr-2" :class="{'btn-success': appointment.called_back, 'btn-warning': !appointment.called_back}">
                                <i class="fa fa-phone-square mr-2"></i>
                                Callback Requested
                            </span>
                            {{ appointment.name }} @ {{ appointment.phone_number }}
                            <label class="ml-2">
                                <input type="checkbox" class="toggle_called" :class="recipient.status == 'Open' ? '' : 'disabled'" :checked="appointment.called_back"
                                        @click="appointmentCalledBackToggle($event, appointment)" :disabled="recipient.status != 'Open'">
                                Called
                            </label>
                        </div>
                        <div v-else-if="appointment.type === 'discussion'" class="alert alert-info">
                            <span class="btn btn-info recipient-action mr-2">
                                <i class="fas fa-question-circle mr-2"></i>
                                Curiosity Call
                            </span>
                            <span>{{ recipient.first_name }} called, but did not elect to reserve a callback or an appointment</span>
                        </div>
                        <div v-else-if="appointment.type === 'appointment'" class="alert alert-success">
                            <span class="btn btn-success recipient-action mr-2">
                                <i class="fa fa-calendar-check mr-2"></i>
                                Appointment
                            </span>
                            {{ appointment.appointment_at_formatted }}
                        </div>
                    </li>
                </ul>
            </div>
            <div id="new-appointment" class="mail-attachments mt-2 mb-3" v-if="recipient.status != 'New' && needsAppointment()">
                <div class="alert alert-info" role="alert">
                    <button class="btn pm-btn btn-primary mr-2"
                            :class="recipient.status != 'Open' ? 'disabled' : ''"
                            @click="showNewApptForm = !showNewApptForm"
                            :disabled="recipient.status != 'Open'"
                            v-if="campaign.status == 'Active'">
                        Add Appointment
                    </button>
                    {{ recipient.first_name }} has no appointments.
                </div>
                <div id="add-appointment-form" class="card" v-if="showNewApptForm">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="appointment_date" class="form-check-label">Select Appointment Date</label>
                            <date-picker id="appointment_date" class="form-control"
                                         v-model="appointmentSelectedDateUnformatted"
                                         type="datetime" format="YYYY-MM-DD hh:mm" :lang="timePickerLang"
                                         :time-picker-options="{ start: '07:00', step: '00:15', end: '20:00' }"
                                         :minute-step="5" confirm></date-picker>
                        </div>
                        <button class="btn btn-primary" role="button"
                                @click="addAppointment(campaign.id, recipientId)">Save
                            Appointment
                        </button>
                    </div>
                </div>
            </div>

            <div class="alert alert-info" role="alert" v-if="recipient.status != 'New' && campaign.adf_crm_export && !recipient.sent_to_crm">
                <button class="btn pm-btn btn-primary mr-2" :class="recipient.status != 'Open' ? 'disabled' : ''"
                        @click.once="sendToCrm()" :disabled="recipient.status != 'Open'">
                    Send to CRM
                </button>
                {{ recipient.first_name }} has not been sent to the CRM.
            </div>
            <div class="alert alert-success" role="alert" v-if="recipient.status != 'New' && campaign.adf_crm_export && recipient.sent_to_crm">
                <span class="btn btn-success recipient-action mr-2">
                    <i class="fa fa-database mr-2"></i>
                    CRM
                </span>
                {{ recipient.first_name }} has already been sent to the CRM.
            </div>

            <div class="alert alert-info" role="alert" v-if="recipient.status != 'New' && campaign.service_dept && recipient.service === 0">
                <button class="btn pm-btn btn-primary mr-2" :class="recipient.status != 'Open' ? 'disabled' : ''" @click.once="sendToService()"
                        v-if="campaign.status == 'Active'" :disabled="recipient.status != 'Open'">
                    Send To Service Department
                </button>
                {{ recipient.first_name }} not sent to Service.
            </div>

            <div class="alert alert-success" role="alert" v-if="recipient.status != 'New' && recipient.checked_in">
                <span class="btn btn-success recipient-action mr-2">
                    <i class="fa fa-calendar-check mr-2"></i>
                    Checked in
                </span>
                Checked in at {{ recipient.checked_in_at_formatted | mUtcParse('YYYY-MM-DD HH:mm:ss') | mFormatLocalized('MM/DD/YYYY hh:mm A') }}.
            </div>

            <div class="alert alert-success" role="alert" v-if="recipient.status != 'New' && campaign.service_dept && recipient.service === 1">
                <span class="btn btn-success recipient-action mr-2">
                    <i class="fa fa-wrench mr-2"></i>
                    Service
                </span>
                {{ recipient.first_name }} already sent to Service.
            </div>

            <div class="mail-attachments" v-if="recipient.status != 'New' && threads.phone && threads.phone.length">
                <h5>Call History</h5>
                <ul class="list-group">
                    <li class="list-group-item" v-for="call in threads.phone">
                        <i class="fas fa-phone"></i>
                        Called at {{ call.created_at | mUtcParse('YYYY-MM-DD HH:mm:ss') | mFormatLocalized('MM/DD/YYYY hh:mm A') }}
                        ({{ call.duration | humanizeWithNumber }})
                        <div v-if="currentUser.is_admin === 1">
                            <div class="audio-player" v-if="call.recording_url && call.duration > 0">
                                <audio controls preload="none" style="width:100%;">
                                    <source :src="call.recording_url" type="audio/mpeg">
                                </audio>
                            </div>
                            <div v-else>
                                (No recording for this call)
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="panel panel-primary messaging-panel mailer-messages" v-if="recipient.status != 'New' && threads.mailer && threads.mailer.length">
                <div class="panel-heading">
                    <h3 class="panel-title">Text To Value Messages</h3>
                </div>

                <div class="panel-body" v-if="threads.mailer.length > 1">
                    <div class="sms-message-container">
                        <div v-for="(msg, idx) in threads.mailer">
                            <div class="message-wrapper" :class="{'outbound-message': !msg.incoming}">
                                <div class="message-user">
                                    <template v-if="msg.impersonation">
                                        {{ msg.impersonation.impersonator.name }} (id: {{ msg.impersonation.impersonator.id}})
                                        <p><small>on behalf of <strong>{{ msg.reply_user }}</strong></small></p>
                                    </template>
                                    <template v-else>
                                        {{ msg.reply_user }}
                                    </template>
                                </div>

                                <div class="message-time" v-if="msg.created_at">{{
                                    msg.created_at | mUtcParse('YYYY-MM-DD HH:mm:ss') | mFormatLocalized('MM/DD/YYYY hh:mm A') }} - {{
                                    msg.created_at | mUtcParse('YYYY-MM-DD HH:mm:ss') | mDurationForHumans('MM/DD/YYYY hh:mm A')}}
                                </div>
                                <div class="message-time"  :class="{'inbound-message': msg.incoming == 1, 'outbound-message': msg.incoming == 0}" v-else><span
                                    class="text-danger">UNKNOWN RECEIVE DATE</span></div>

                                <div class="message" :class="{'inbound-message': msg.incoming == 1, 'outbound-message': msg.incoming == 0}">{{ msg.message_formatted }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-primary messaging-panel sms-messages" v-if="recipient.status != 'New' && threads.text && threads.text.length">
                <div class="panel-heading">
                    <h3 class="panel-title">SMS Messaging</h3>
                </div>

                <div class="message-drop-text" v-if="threads.textDrop && threads.textDrop.length > 0 && threads.textDrop[0].text_message">
                    <strong class="mb-3">Original Message</strong>
                    <div>{{ threads.textDrop[0].text_message }}</div>
                </div>

                <div class="panel-body">

                    <div class="sms-message-container">
                        <div v-for="msg in threads.text">
                            <div class="message-wrapper" :class="{'outbound-message': !msg.incoming}">
                                <div class="message-user">
                                    <template v-if="msg.impersonation">
                                        {{ msg.impersonation.impersonator.name }} (id: {{ msg.impersonation.impersonator.id}})
                                        <p><small>on behalf of <strong>{{ msg.reply_user }}</strong></small></p>
                                    </template>
                                    <template v-else>
                                        {{ msg.reply_user }}
                                    </template>
                                </div>

                                <div class="message-time" v-if="msg.created_at">{{
                                    msg.created_at | mUtcParse('YYYY-MM-DD HH:mm:ss') | mFormatLocalized('MM/DD/YYYY hh:mm A') }} - {{
                                    msg.created_at | mUtcParse('YYYY-MM-DD HH:mm:ss') | mDurationForHumans('MM/DD/YYYY hh:mm A')}}
                                </div>
                                <div class="message-time"  :class="{'inbound-message': msg.incoming == 1, 'outbound-message': msg.incoming == 0}" v-else><span
                                    class="text-danger">UNKNOWN RECEIVE DATE</span></div>

                                <div class="message" :class="{'inbound-message': msg.incoming == 1, 'outbound-message': msg.incoming == 0}">{{ msg.message_formatted }}</div>
                                <div class="checkbox" v-if="msg.incoming">
                                    <label>
                                        <input type="checkbox" class="message-read" :class="recipient.status != 'Open' ? 'disabled' : ''"
                                               :checked="msg.read" :disabled="recipient.status != 'Open'"
                                               @click="messageUpdateReadStatus($event, msg.id)">
                                        Read
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- @todo: refactor v-if into discrete checker method -->
                    <form @submit.prevent="sendText"
                            v-if="campaign.status == 'Active' && recipient.status == 'Open' &&
                                (isAdmin || ((!isAdmin || isImpersonated) && activeCompany.type === 'dealership'))">
                        <div id="sms-form" style="margin-top: 20px;">
                            <div class="input-group">
                                <vue-simple-suggest
                                    v-model="textMessage"
                                    placeholder="Type your message..."
                                    :styles="{defaultInput: 'form-control message-field'}"
                                    :min-length="0"
                                    :list="cannedResponses"
                                    :filter-by-query="true">
                                </vue-simple-suggest>
                                <div class="input-group-btn">
                                    <button type="submit" :disabled="textMessage.length === 0" class="btn btn-primary waves-effect send-sms">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel panel-primary messaging-panel email-messages" v-if="recipient.status != 'New' && threads.email && threads.email.length">
                <div class="panel-heading">
                    <h3 class="panel-title">Email Messaging</h3>
                </div>

                <div class="message-drop-text" v-if="threads.emailDrop && threads.emailDrop[0].email_html">
                    <div class="cursor-pointer" v-b-toggle.emailTemplateCollapse>
                        <strong class="mb-3">Original Message</strong>&nbsp;
                        <i class="fa fa-angle-down closed"></i>
                        <i class="fa fa-angle-up opened"></i>
                    </div>

                    <b-collapse id="emailTemplateCollapse" class="mt-2">
                        <div v-html="threads.emailDrop[0].email_html"></div>
                    </b-collapse>
                </div>

                <div class="panel-body">
                    <div class="email-message-container">
                        <div v-for="msg in threads.email">
                            <div class="message-wrapper" :class="{'outbound-message': !msg.incoming}">
                                <div class="message-user">
                                    <template v-if="msg.impersonation">
                                        {{ msg.impersonation.impersonator.name }} (id: {{ msg.impersonation.impersonator.id}})
                                        <p><small>on behalf of <strong>{{ msg.reply_user }}</strong></small></p>
                                    </template>
                                    <template v-else>
                                        {{ msg.reply_user }}
                                    </template>
                                </div>
                                <div class="message-time" v-if="msg.created_at">{{
                                    msg.created_at | mUtcParse('YYYY-MM-DD HH:mm:ss') | mFormatLocalized('MM/DD/YYYY hh:mm A') }} - {{
                                    msg.created_at | mUtcParse('YYYY-MM-DD HH:mm:ss') | mDurationForHumans('MM/DD/YYYY hh:mm A')}}
                                </div>
                                <div class="message-time" :class="{'inbound-message': msg.incoming, 'outbound-message': !msg.incoming}" v-else><span
                                    class="text-danger">UNKNOWN RECEIVE DATE</span></div>

                                <div class="message unread" :class="{'inbound-message': msg.incoming, 'outbound-message': !msg.incoming}">{{ msg.message_formatted }}</div>

                                <div class="checkbox" v-if="msg.incoming">
                                    <label>
                                        <input type="checkbox" class="message-read" :class="recipient.status != 'Open' ? 'disabled' : ''"
                                               :checked="msg.read" :disabled="recipient.status != 'Open'"
                                               @click="messageUpdateReadStatus($event, msg.id)">
                                        Read
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


                    <form class="mt-3" @submit.prevent="sendEmail"
                            v-if="campaign.status == 'Active' && recipient.status == 'Open' &&
                                (isAdmin || ((!isAdmin || isImpersonated) && activeCompany.type === 'dealership'))">
                        <div id="email-form">
                            <div class="input-group">
                                <input type="text" id="email-message" class="form-control message-field" name="message"
                                       placeholder="Type your message..." v-model="emailMessage">
                                <div class="input-group-btn">
                                    <button type="submit" :disabled="emailMessage.length === 0" class="btn btn-primary waves-effect send-email">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import moment from 'moment';
    import {generateRoute, replacePlaceholders} from './../../../common/helpers';
    import DatePicker from 'vue2-datepicker';
    import {pickBy} from 'lodash';
    import VueSimpleSuggest from 'vue-simple-suggest';
    import PusherService from './../../../common/pusher-service';
    import './../../../filters/m-utc-parse.filter';
    import './../../../filters/m-format-localized.filter';
    import './../../../filters/m-duration-for-humans.filter';
    import './../../../filters/humanize-with-number.filter';

    let pusherService = null;

    export default {
        beforeDestroy() {
            pusherService.disconnect();
        },
        components: {
            'spinner-icon': require('./../../../components/spinner-icon/spinner-icon').default,
            DatePicker,
            VueSimpleSuggest
        },
        computed: {
            phone_link: function () {
                return "tel://" + this.recipient.phone
                    .replace(/[ext\.|ext|x]/,",,")
                    .replace(/\s/,"");
            },
            labelDropdownOptions: function () {
                return pickBy(this.labelDropdownItems, (label, index) => {
                    return !this.labels[index];
                });
            }
        },
        data() {
            return {
                cannedResponses: [],
                showNewApptForm: false,
                disableBgClick: false,
                recipient: [],
                threads: {
                    email: [],
                    phone: [],
                    text: [],
                    textDrop: {},
                    emailDrop: {}
                },
                activeCompany: {},
                appointments: [],
                isAdmin: false,
                rest: [],
                loading: false,
                starting_notes: '',
                notes: '',
                calledCheckbox: false,
                appointmentSelectedDateUnformatted: '',
                appointmentSelectedDate: '',
                appointmentSelectedTime: '',
                textMessage: '',
                emailMessage: '',
                selectedLabel: '',
                labels: {},
                labelsDropdown: {},
                labelDropdownItems: {
                    interested: "Interested",
                    service: "Service Dept",
                    not_interested: "Not Interested",
                    wrong_number: "Wrong Number",
                    car_sold: "Car Sold",
                    heat: "Heat Case",
                },
                timePickerLang: {
                    days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    pickers: ['next 7 days', 'next 30 days', 'previous 7 days', 'previous 30 days'],
                    placeholder: {
                        date: 'Select Date',
                        dateRange: 'Select Date Range'
                    }
                }
            }
        },
        methods: {
            closePanel() {
                this.$emit('closePanel', {});
            },
            resetVars() {
                this.recipient = [];
                this.threads = [];
                this.appointments = [];
                this.rest = [];
                this.loading = false;
                this.starting_notes = '';
                this.notes = '';
                this.calledCheckbox = false;
                this.appointmentSelectedDateUnformatted = '';
                this.appointmentSelectedDate = '';
                this.appointmentSelectedTime = '';
                this.textMessage = '';
                this.emailMessage = '';
                this.selectedLabel = '';
                this.labels = {};
            },
            getResponses: function () {
                this.setLoading(true);
                const recipientId = this.recipientId;

                axios.get(generateRoute(window.getResponsesUrl, {'recipientId': recipientId}))
                    .then(({data: r}) => {
                        this.recipient = r.data.lead;
                        this.threads = r.data.threads;
                        this.appointments = r.data.appointments;
                        // this.rest = r.rest;
                        this.starting_notes = r.data.lead.notes;
                        this.notes = r.data.lead.notes;
                        this.labels = r.data.lead.labels.length === 0 ? {} : r.data.lead.labels;

                        if (this.threads.textDrop && this.threads.textDrop.length > 0 && this.threads.textDrop[0].text_message) {
                            this.threads.textDrop[0].text_message = replacePlaceholders(this.threads.textDrop[0].text_message, r.data.lead);
                        }
                        if (this.threads.emailDrop && this.threads.emailDrop.email_html) {
                            this.threads.emailDrop.email_html = replacePlaceholders(this.threads.emailDrop && this.threads.emailDrop.email_html, r.data.lead);
                        }

                        this.cannedResponses = this.threads.textCannedResponses.map(response => response.response);

                        this.registerPusherListeners();
                        this.setLoading(false);
                    })
                    .catch((response) => {
                        this.setLoading(false);
                        console.log(response);
                        window.PmEvent.fire('errors.api', "Couldn't fetch responses.");
                    });
            },
            pad2: function (number) {
                return (number < 10 ? '0' : '') + number;
            },
            setLoading: function (bool) {
                // Local loading variable
                this.loading = bool;
            },
            addNotes: function (recipientId) {
                axios.post(generateRoute(window.updateNotesUrl, {'leadId': recipientId}), {
                    notes: this.notes
                })
                .then((response) => {
                    this.recipient.notes = this.notes;
                    this.$toastr.success('Note added.');
                })
                .catch((response) => {
                    window.PmEvent.fire('errors.api', 'Failed to add note.');
                });
            },
            openLead: function (leadId) {
                axios.post(generateRoute(window.openLeadUrl, {'leadId': leadId}))
                    .then(response => {
                        this.$toastr.success('Lead Opened with status: ' + response.data.data.status);
                        window.app.$set(this.recipient, 'status', response.data.data.status);
                        window.PmEvent.fire('changed.recipient.status', response.data.data);
                    })
                    .catch((error) => {
                        console.error(error);
                        window.PmEvent.fire('errors.api', 'Failed to open lead');
                    });
            },
            closeLead: function (lead) {
               // show modal with requirements
                window.PmEvent.fire('lead.close-request', lead);
                /*
                axios.post(generateRoute(window.closeLeadUrl, {'leadId': leadId}))
                    .then(response => {
                        this.$toastr.success('Lead Closed');
                        window.app.$set(this.recipient, 'status', response.data.recipient.status);
                        window.PmEvent.fire('changed.recipient.status', response.data.recipient);
                    })
                    .catch((error) => {
                        console.error(error);
                        window.PmEvent.fire('errors.api', 'Failed to open lead');
                    });
                */
            },
            reopenLead: function (leadId) {
                axios.post(generateRoute(window.reopenLeadUrl, {'leadId': leadId}))
                    .then(response => {
                        this.$toastr.success('Lead Reopened');
                        window.app.$set(this.recipient, 'status', response.data.data.status);
                        window.PmEvent.fire('changed.recipient.status', response.data.data);
                    })
                    .catch((error) => {
                        console.error(error);
                        window.PmEvent.fire('errors.api', 'Failed to open lead');
                    });
            },
            needsAppointment: function () {
                return _.filter(this.appointments, {type: "appointment"}).length == 0;
            },
            appointmentCalledBackToggle: function (event, appointment) {
                axios.post(generateRoute(window.appointmentUpdateCalledStatusUrl, {'appointmentId': appointment.id}),
                    {
                        called_back: event.target.checked
                    })
                    .then((response) => {
                        appointment.called_back = !appointment.called_back;
                        this.$toastr.success('Called status updated.');
                    })
                    .catch((response) => {
                        window.PmEvent.fire('errors.api', 'Failed to update called status.');
                    });
            },
            addAppointment: function (campaignId, recipientId) {
                axios.post(generateRoute(window.addAppointmentUrl, {'recipientId': recipientId}),
                    {
                        appointment_date: this.appointmentSelectedDate,
                        appointment_time: this.appointmentSelectedTime
                    })
                    .then((response) => {
                        this.showNewApptForm = false;
                        this.appointments.push(response.data);
                        this.$toastr.success('Appointment added.');
                    })
                    .catch((response) => {
                        window.PmEvent.fire('errors.api', 'Failed to add an appointment.');
                    });
            },
            messageUpdateReadStatus: function (event, textId) {
                axios.post(generateRoute(window.messageUpdateReadStatusUrl, {'responseId': textId}),
                    {
                        read: event.target.checked
                    })
                    .then((response) => {
                        this.$toastr.success('Read status updated.');
                    })
                    .catch((response) => {
                        window.PmEvent.fire('errors.api', 'Failed to update message read status.');
                    });
            },
            sendToCrm: function () {
                axios
                    .post(generateRoute(window.sendCrmUrl, {recipientId: this.recipientId}))
                    .then(response => {
                        this.recipient.sent_to_crm = true;
                        this.$toastr.success("Recipient sent to CRM");
                    })
                    .catch(error => {
                        console.error(error);
                        window.PmEvent.fire('errors.api', "Unable to send recipient to CRM at this time");
                    });
            },
            sendToService: function () {
                axios.post(generateRoute(window.sendServiceUrl, {'leadId': this.recipientId}))
                    .then(response => {
                        this.recipient.service = 1;
                        this.$toastr.success("Lead sent to Service");
                    })
                    .catch(error => {
                        console.error(error);
                        window.PmEvent.fire('errors.api', "Unable to send lead to Service at this time");
                    });
            },
            sendText: function () {
                if (this.textMessage.length === 0) return;
                const textMessage = this.textMessage;
                // Add temporal message, this message will be replaced
                this.threads.text.push({
                    created_at: moment.utc().format('YYYY-MM-DD HH:mm:dd'),
                    incoming: 0,
                    message: textMessage,
                    message_formatted: textMessage,
                    read: 1,
                    type: "text",
                });
                this.textMessage = '';
                const indexOfMessage = this.threads.text.length - 1;
                axios.post(generateRoute(window.sendTextUrl, {'recipientId': this.recipientId}),
                    {
                        message: textMessage
                    })
                    .then(response => {
                        this.threads.text.splice(indexOfMessage, 1, response.data.response);
                    }, () => {
                        // Reset text message if empty
                        if (!this.textMessage) {
                            this.textMessage = textMessage;
                        }
                        this.threads.text.splice(indexOfMessage, 1);
                        window.PmEvent.fire('errors.api', 'Failed to send text.');
                    });
            },
            sendEmail: function () {
                if (this.emailMessage.length === 0) return;
                const emailMessage = this.emailMessage;
                // Add temporal message, this message will be replaced
                this.threads.email.push({
                    created_at: moment.utc().format('YYYY-MM-DD HH:mm:ss'),
                    incoming: 0,
                    message: emailMessage,
                    message_formatted: emailMessage,
                    type: "email",
                });
                this.emailMessage = '';
                const indexOfMessage = this.threads.email.length - 1;
                axios.post(generateRoute(window.sendEmailUrl, {'recipientId': this.recipientId}),
                    {
                        message: emailMessage
                    })
                    .then(response => {
                        this.threads.email.splice(indexOfMessage, 1, response.data.response);
                    }, () => {
                        // Reset text message if empty
                        if (!this.emailMessage) {
                            this.emailMessage = emailMessage;
                        }
                        this.threads.email.splice(indexOfMessage, 1);
                        window.PmEvent.fire('errors.api', 'Failed to send email.');
                    });
            },
            addLabel: function (label, labelText) {
                this.$set(this.labels, label, labelText);
                window.PmEvent.fire('added.recipient.label', {
                    recipientId: this.recipientId,
                    label,
                    labelText
                });
                axios.post(generateRoute(window.addLabelUrl, {'recipientId': this.recipientId}), {label})
                    .then((response) => {
                    }, () => {
                        this.$delete(this.labels, label);
                        window.PmEvent.fire('added.recipient.label', {
                            recipientId: this.recipientId,
                            label,
                            labelText
                        });
                        window.PmEvent.fire('errors.api', 'Failed to add label.');
                    });
            },
            removeLabel: function (label, key) {
                this.$delete(this.labels, key);
                window.PmEvent.fire('removed.recipient.label', {
                    recipientId: this.recipientId,
                    label: key,
                    labelText: label
                });
                axios.post(generateRoute(window.removeLabelUrl, {'recipientId': this.recipientId}),
                    {
                        label: key
                    })
                    .then(() => {
                    }, () => {
                        this.$set(this.labels, key, label);
                        window.PmEvent.fire('errors.api', 'Failed to remove label.');
                        window.PmEvent.fire('removed.recipient.label', {
                            recipientId: this.recipientId,
                            label: key,
                            labelText: label
                        });
                    });
            },
            registerPusherListeners: function () {
                // Sms
                pusherService
                    .subscribe('private-campaign.' + this.campaign.id)
                    .bind('recipient.' + this.recipient.id + '.text-response-received', data => {
                        this.threads.text.push(data.response);
                    });

                // Email
                pusherService
                    .subscribe('private-campaign.' + this.campaign.id)
                    .bind('recipient.' + this.recipient.id + '.email-response-received', data => {
                        this.threads.email.push(data.response);
                    });

                // Phone
                pusherService
                    .subscribe('private-campaign.' + this.campaign.id)
                    .bind('recipient.' + this.recipient.id + '.phone-response-received', data => {
                        this.threads.phone.push(data.response);
                    });
            }
        },
        mounted() {
            this.activeCompany = window.activeCompany;
            this.isAdmin = window.isAdmin;
            this.isImpersonated = window.isImpersonated;
            pusherService = new PusherService();
            this.getResponses();
            window.PmEvent.listen('recipient.closed', (data) => {
                this.recipient.status = 'Closed';
                setTimeout(() => {
                    this.$emit('closePanel', {});
                }, 800);
            });
        },
        props: ['campaign', 'recipientId', 'currentUser', 'recipientKey'],
        watch: {
            selectedLabel: function (newVal) {
                this.addLabel(newVal.value);
            },
            appointmentSelectedDateUnformatted: function (newVal) {
                let d = new Date(newVal);
                // date format: YYYY-MM-DD
                let formattedDate = d.getFullYear() + '-' + this.pad2(d.getMonth() + 1) + '-' + this.pad2(d.getDate());
                // time format: HH:mm
                let formattedTime = this.pad2(d.getHours()) + ':' + this.pad2(d.getMinutes());
                this.appointmentSelectedDate = formattedDate;
                this.appointmentSelectedTime = formattedTime;
            },
        }
    }
</script>
