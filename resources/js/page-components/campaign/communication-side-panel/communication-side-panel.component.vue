<template>
    <div>
        <div class="container-fluid list-campaign-container">
            <div class="row align-items-end no-gutters mt-4 mb-3">
                <div class="col-12">

                    <button class="btn btn-primary close-comms-panel" v-on:click.prevent="closePanel">Close</button>
                </div>
            </div>

            <div class="content" :class="{'show': !loading}">
                <div class="row align-items-end no-gutters mt-4 mb-3">
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
                            {{ recipient.phone }}
                        </div>
                    </div>
                    <div class="col-4">
                        <b-dropdown right text="Add Label" :disabled="Object.keys(labelsDropdown).length === 0" class="float-right">
                            <b-dropdown-item v-for="(label, index) in labelsDropdown" :key="index" :value="index"
                                             @click="addLabel(index)">{{ label }}
                            </b-dropdown-item>
                        </b-dropdown>
                    </div>
                </div>

                <div class="row align-items-end no-gutters mt-4 mb-3">
                    <div class="col-12 labels-wrapper" v-if="Object.keys(this.labels).length > 0">
                        <ul class="labels">
                            <li :class="index" v-for="(label, index) in this.labels">{{ label }}<i
                                    class="fas fa-times" @click="removeLabel(index)"></i></li>
                        </ul>
                    </div>
                </div>

                <div class="notes-wrapper">
                    <div class="form-group">
                        <textarea class="form-control" placeholder="Notes..." name="notes" rows="4"
                                  v-model="notes">{{ this.recipient.notes }}</textarea>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary" @click="addNotes(recipientId)">Save note</button>
                    </div>
                </div>
                <div class="call-in-wrapper" v-if="appointments.length">
                    <h4>Call Ins</h4>
                    <ul class="list-group">
                        <li class="list-group-item" v-for="appointment in appointments">
                            <div v-if="appointment.type === 'callback'">
                                <i class="fas fa-phone mr-2"></i>
                                {{ appointment.name }} @ {{ appointment.phone_number }}
                                <div class="checkbox" style="padding-top: 6px; margin-left: 8px;">
                                    <label>
                                        <input type="checkbox" class="toggle_called" :checked="appointment.called_back"
                                               @click="appointmentCalledBackToggle($event, appointment.id)">
                                        Called
                                    </label>
                                </div>
                            </div>
                            <div v-else-if="appointment.type === 'discussion'">
                                <i class="fas fa-question-circle mr-2"></i>
                                <span class="mr-2">Just Curious</span>
                                <span>{{ recipient.name }} called, but did not elect to reserve a callback or an appointment</span>
                            </div>
                            <div v-else-if="appointment.type === 'appointment'">
                                <i class="fas fa-calendar mr-2"></i>
                                {{ appointment.appointment_at_formatted }}
                            </div>
                        </li>
                    </ul>
                </div>
                <div id="new-appointment" class="mail-attachments mb-3" v-else>
                    <div class="alert alert-info" role="alert">
                        {{ recipient.name }} does not have any appointments yet.
                        <button class="btn pm-btn btn-primary" @click="showNewApptForm = !showNewApptForm">Add new appointment</button>
                    </div>
                    <div id="add-appointment-form" class="card" v-if="showNewApptForm">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="appointment_date" class="form-check-label">Select Appointment Date</label>
                                <date-picker id="appointment_date" class="form-control" v-model="appointmentSelectedDateUnformatted"
                                             type="datetime" format="YYYY-MM-DD hh:mm" :lang="timePickerLang"
                                             :minute-step="5" confirm></date-picker>
                            </div>
                            <button class="btn btn-primary" role="button"
                                    @click="addAppointment(campaign.id, recipientId)">Save
                                Appointment
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mail-attachments" v-if="threads.phone">
                    <h4>Calls</h4>
                    <ul class="list-group">

                        <li class="list-group-item" v-for="call in threads.phone">
                            <i class="fas fa-phone"></i>
                            Called at {{ call.created_at }}

                            <div v-if="currentUser.is_admin === 1">
                                <div class="audio-player" v-if="call.recording_url">
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

                <div class="panel panel-primary messaging-panel sms-messages" v-if="threads.text">
                    <div class="panel-heading">
                        <h3 class="panel-title">SMS Messaging</h3>
                    </div>

                    <div class="panel-body">
                        <div v-if="threads.textDrop">
                            <strong class="vertical-text">Original Message</strong>
                            <div class="message-time" style="margin-left: 25px">{{ threads.textDrop.send_at_formatted
                                }}
                            </div>
                            <p class="message original-message">
                                {{ threads.textDrop.text_message }}
                            </p>
                        </div>

                        <div class="sms-message-container">

                            <div v-for="msg in threads.text">

                                <div class="message-wrapper"
                                     :class="{'inbound-message': msg.incoming, 'outbound-message': !msg.incoming}">

                                    <div class="message-time" v-if="msg.created_at_formatted">{{
                                        msg.created_at_formatted }}
                                    </div>
                                    <div class="message-time" v-else><span
                                            class="text-danger">UNKNOWN RECEIVE DATE</span></div>

                                    <div class="message unread">{{ msg.message_formatted }}</div>

                                    <div class="checkbox" v-if="msg.incoming">
                                        <label>
                                            <input type="checkbox" class="message-read" :checked="msg.read"
                                                   @click="messageUpdateReadStatus($event, msg.id)">
                                            Read
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="sms-form" style="margin-top: 20px;" v-if="!campaign.is_expired">
                            <div class="input-group">
                                <input type="text" id="sms-message" class="form-control message-field" name="message"
                                       placeholder="Type your message..." v-model="textMessage">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary waves-effect send-sms"
                                            @click="sendText">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="panel panel-primary messaging-panel email-messages" v-if="threads.email">
                    <div class="panel-heading">
                        <h3 class="panel-title">Email Messaging</h3>
                    </div>

                    <div class="panel-body">

                        <div v-if="threads.emailDrop">
                            <div class="message-time" style="margin-left: 25px">{{ threads.emailDrop.send_at_formatted
                                }}
                            </div>
                            <strong class="vertical-text">Original Message</strong>

                            <div class="message original-message email-original" v-html="threads.emailDrop.email_html">
                            </div>
                        </div>

                        <div class="email-message-container">

                            <div v-for="msg in threads.email">

                                <div class="message-wrapper"
                                     :class="{'inbound-message': msg.incoming, 'outbound-message': !msg.incoming}">

                                    <div class="message-time" v-if="msg.created_at_formatted">{{
                                        msg.created_at_formatted }}
                                    </div>
                                    <div class="message-time" v-else><span
                                            class="text-danger">UNKNOWN RECEIVE DATE</span></div>

                                    <div class="message unread">{{ msg.message_formatted }}</div>

                                    <div class="checkbox" v-if="msg.incoming">
                                        <label>
                                            <input type="checkbox" class="message-read" :checked="msg.read"
                                                   @click="messageUpdateReadStatus($event, msg.id)">
                                            Read
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="email-form" style="margin-top: 20px;" v-if="!campaign.is_expired">
                            <div class="input-group">
                                <input type="text" id="email-message" class="form-control message-field" name="message"
                                       placeholder="Type your message..." v-model="emailMessage">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary waves-effect send-email"
                                            @click="sendEmail">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import {generateRoute} from './../../../common/helpers'
    import DatePicker from 'vue2-datepicker'

    export default {
        mounted() {
            this.resetVars();
            this.getResponses(this.campaign.id, this.recipientId);
        },
        components: {
            // 'date-pick': require('./../../../components/date-pick/date-pick'),
            DatePicker
        },
        data() {
            return {
                showNewApptForm: false,
                disableBgClick: false,
                recipient: [],
                threads: [],
                appointments: [],
                rest: [],
                loading: false,
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
        props: ['campaign', 'recipientId', 'currentUser', 'recipientKey'],
        computed: {
            labelDropdownOptions: function () {
                let options = this.labelDropdownItems;
                this.labels.forEach((label,text) => {
                    options.delete(label);
                });
                return options
            }
        },
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
        },
        methods: {
            updateLabelDropdown() {
                for (label in this.labelDropdownItems) {
                    if (this.labels[label] === undefined) {
                        this.$set(this.labelsDropdown, label, this.labelDropdownItems[label]);
                    } else {
                        this.$delete(this.labelsDropdown, label);
                    }
                }
            },
            closePanel() {
                window['app'].pusherUnbindEvent('private-campaign.' + this.campaign.id, 'response.' + this.recipientId + '.updated');
                this.resetVars();
                this.$emit('closePanel', {});
            },
            resetVars() {
                this.recipient = [];
                this.threads = [];
                this.appointments = [];
                this.rest = [];
                this.loading = false;
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
            getResponses: function (campaignId, recipientId) {
                this.setLoading(true);

                axios.get(generateRoute(window.getResponsesUrl, {'recipientId': recipientId}))
                    .then((response) => {
                        let r = response.data;
                        this.recipient = r.recipient;
                        this.threads = r.threads;
                        this.appointments = r.appointments;
                        this.rest = r.rest;
                        this.notes = r.recipient.notes;
                        this.labels = r.recipient.labels.length === 0 ? {} : r.recipient.labels;

                        this.updateResponses(this.recipient);
                        this.updateLabelDropdown();
                        this.setLoading(false);
                    })
                    .catch((response) => {
                        this.setLoading(false);
                        this.$toastr.error("Couldn't fetch responses.");
                    });
            },
            pad2: function (number) {
                return (number < 10 ? '0' : '') + number;
            },
            setLoading: function (bool) {
                // Local loading variable
                this.loading = bool;
                // Main vue instance loading variable
                this.$set(window['app'], 'loading', bool);
            },
            addNotes: function (recipientId) {
                axios.post(generateRoute(window.updateNotesUrl, {'recipientId': recipientId}),
                    {
                        notes: this.notes
                    })
                    .then((response) => {
                        this.$toastr.success('Note added.');
                    })
                    .catch((response) => {
                        this.$toastr.error('Failed to add note.');
                    });
            },
            appointmentCalledBackToggle: function (event, appointmentId) {
                axios.post(generateRoute(window.appointmentUpdateCalledStatusUrl, {'appointmentId': appointmentId}),
                    {
                        called_back: event.target.checked
                    })
                    .then((response) => {
                        this.$toastr.success('Called status updated.');
                    })
                    .catch((response) => {
                        this.$toastr.error('Failed to update called status.');
                    });
            },
            addAppointment: function (campaignId, recipientId) {
                axios.post(generateRoute(window.addAppointmentUrl, {'recipientId': recipientId}),
                    {
                        appointment_date: this.appointmentSelectedDate,
                        appointment_time: this.appointmentSelectedTime
                    })
                    .then((response) => {
                        this.$toastr.success('Appointment added.');
                    })
                    .catch((response) => {
                        this.$toastr.error('Failed to add an appointment.');
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
                        this.$toastr.error('Failed to update message read status.');
                    });
            },
            sendText: function () {
                axios.post(generateRoute(window.sendTextUrl, {'recipientId': this.recipientId}),
                    {
                        message: this.textMessage
                    })
                    .then((response) => {
                        this.$toastr.success('Text sent.');
                    })
                    .catch((response) => {
                        this.$toastr.error('Failed to send text.');
                    });
            },
            sendEmail: function () {
                axios.post(generateRoute(window.sendEmailUrl, {'recipientId': this.recipientId}),
                    {
                        message: this.emailMessage
                    })
                    .then((response) => {
                        this.$toastr.success('Email sent.');
                    })
                    .catch((response) => {
                        this.$toastr.error('Failed to send email.');
                    });
            },
            selectLabel: function (label) {
                this.selectedLabel = label;
            },
            addLabel: function (label) {
                axios.post(generateRoute(window.addLabelUrl, {'recipientId': this.recipientId}),{ label: label })
                    .then((response) => {
                        this.$set(this.labels, response.data.label, response.data.labelText);
                        this.$toastr.success('Label added.');
                        this.updateLabelDropdown();
                    })
                    .catch((response) => {
                        this.$toastr.error('Failed to add label.');
                    });
            },
            removeLabel: function (label) {
                axios.post(generateRoute(window.removeLabelUrl, {'recipientId': this.recipientId}),
                    {
                        label: label
                    })
                    .then((response) => {
                        this.$delete(this.labels, label);
                        this.$toastr.success('Label removed.');
                        this.updateLabelDropdown();
                    })
                    .catch((response) => {
                        this.$toastr.error('Failed to remove label.');
                    });
            },
            updateResponses: function (recipient) {

                if (recipient) {

                    window['app'].pusher('private-campaign.' + this.campaign.id, 'response.' + recipient.id + '.updated', (data) => {

                        if (data) {
                            // TODO: check why is whole slidePanel flickering when loading is enabled
                            // this.setLoading(true);

                            axios.get(generateRoute(window.recipientGetResponsesUrl, {'recipientId': this.recipientId}),
                                {
                                    params: {
                                        list: data
                                    }
                                })
                                .then((response) => {

                                    if (response.data.appointments) {
                                        this.appointments = response.data.appointments;
                                    }

                                    if (response.data.threads) {
                                        this.threads = response.data.threads;
                                    }

                                    if (response.data.recipient) {
                                        this.recipient = response.data.recipient;
                                    }

                                    if (response.data.recipient.labels) {
                                        this.labels = response.data.recipient.labels;
                                    }

                                    // TODO: check why is whole slidePanel flickering when loading is enabled
                                    // this.setLoading(false);
                                })
                                .catch((response) => {
                                    this.$toastr.error('Failed to update responses.');
                                    // TODO: check why is whole slidePanel flickering when loading is enabled
                                    // this.setLoading(false);
                                });
                        }
                    });
                }
            },
        }
    }
</script>
