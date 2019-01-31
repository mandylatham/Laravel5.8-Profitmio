<template>
    <div>
        <div class="container-fluid list-campaign-container">
            <div class="row align-items-end no-gutters mt-4 mb-3">
                <div class="col-12">

                    <button class="btn btn-primary float-right" v-on:click.prevent="closePanel">Close Panel</button>
                </div>
            </div>

            <div class="content" :class="{'show': !loading}">
                <div class="row align-items-end no-gutters mt-4 mb-3">
                    <div class="col-12">

                        <div class="name text-primary"><strong>{{ this.recipient.name }}</strong>
                            <small><em>{{ this.recipient.location }}</em></small>
                        </div>
                        <div class="vehicle" v-if="this.recipient.vehicle">
                            <i class="fas fa-car"></i>
                            {{ this.recipient.vehicle }}
                        </div>
                        <div class="email" v-if="this.recipient.email">
                            <i class="fas fa-envelope"></i>
                            {{ this.recipient.email }}
                        </div>
                        <div class="phone" v-if="this.recipient.phone">
                            <i class="fas fa-phone"></i>
                            {{ this.recipient.phone }}
                        </div>
                    </div>
                </div>

                <div class="row align-items-end no-gutters mt-4 mb-3">
                    <div class="col-12">

                        <label for="labels" class="form-check-label">Add Label</label>
                        <v-select :options="labelsDropdown" name="label" class="form-control filter--v-select"
                                  id="labels" v-model="selectedLabel"></v-select>
                    </div>

                    <div class="col-12" v-if="this.labels">
                        <ul class="labels">
                            <li :class="index" v-for="(label, index) in this.labels">{{ label }}<i
                                    class="fas fa-times" @click="removeLabel(index)"></i></li>
                        </ul>
                    </div>
                </div>

                <div class="mail-content">
                    <div class="form-group">
                        <textarea class="form-control" placeholder="Notes..." name="notes"
                                  v-model="notes">{{ this.recipient.notes }}</textarea>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-primary" @click="addNotes(recipientId)">Save note</button>
                    </div>
                </div>

                <div class="mail-attachments" v-if="appointments.length">
                    <h4>Appointments</h4>


                    <ul class="list-group">
                        <li class="list-group-item" v-for="appointment in appointments">

                            <div v-if="appointment.type === 'callback'">
                                <i class="fas fa-phone"></i>
                                {{ appointment.name }} @ {{ appointment.phone_number }}
                                <div class="checkbox" style="padding-top: 6px; margin-left: 8px;">
                                    <label>
                                        <input type="checkbox" class="toggle_called" :checked="appointment.called_back"
                                               @click="appointmentCalledBackToggle($event, appointment.id)">
                                        Called
                                    </label>
                                </div>
                            </div>
                            <div v-else>
                                <i class="fas fa-calendar"></i>
                                {{ appointment.appointment_at_formatted }}
                            </div>
                        </li>
                    </ul>
                </div>
                <div id="new-appointment" class="mail-attachments col-md-10" v-else>
                    <div id="add-appointment-form" style="padding:25px; border:1px solid #e0e0e0;">
                        <h4>Add Appointment</h4>
                        <div>
                            <div class="form-group">
                                <label for="appointment_date" class="form-check-label">Select Appointment Date</label>

                                <date-pick id="appointment_date" class="event-calendar"
                                           v-model="appointmentSelectedDate"
                                           :has-input-element="false"></date-pick>
                            </div>
                            <div class="form-group">
                                <label for="appointment_time" class="form-check-label">Select Appointment Time</label>
                                <select name="appointment_time" class="form-control" id="appointment_time"
                                        v-html="rest.appointmentTimes" v-model="appointmentSelectedTime">
                                </select>
                            </div>
                            <button class="btn btn-primary" role="button"
                                    @click="addAppointment(campaign.id, recipientId)">Add
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

                        <div id="sms-form" style="margin-top: 20px;" v-if="campaign.is_expired">
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

                        <div id="email-form" style="margin-top: 20px;" v-if="campaign.is_expired">
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

    export default {
        mounted() {
            this.resetVars();
            this.getResponses(this.campaign.id, this.recipientId);
        },
        components: {
            'date-pick': require('./../../../components/date-pick/date-pick')
        },
        data() {
            return {
                recipient: [],
                threads: [],
                appointments: [],
                rest: [],
                loading: false,
                notes: '',
                calledCheckbox: false,
                appointmentSelectedDate: '',
                appointmentSelectedTime: '',
                textMessage: '',
                emailMessage: '',
                selectedLabel: '',
                labels: [],
                labelsDropdown: [
                    {value: 'interested', label: 'Interested'},
                    {value: 'service', label: 'Service Dept'},
                    {value: 'not_interested', label: 'Not Interested'},
                    {value: 'wrong_number', label: 'Wrong Number'},
                    {value: 'car_sold', label: 'Car Sold'},
                    {value: 'heat', label: 'Heat Case'},
                ]
            }
        },
        props: ['campaign', 'recipientId', 'currentUser', 'recipientKey'],
        computed: {
            //
        },
        watch: {
            selectedLabel: function (newVal) {
                this.addLabel(newVal.value)
            }
        },
        methods: {
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
                this.appointmentSelectedDate = '';
                this.appointmentSelectedTime = '';
                this.textMessage = '';
                this.emailMessage = '';
                this.selectedLabel = '';
                this.labels = [];
            },
            getResponses: function (campaignId, recipientId) {
                this.setLoading(true);

                axios.get(generateRoute(window.getResponsesUrl, {'recipientId': recipientId}))
                    .then((response) => {
                        this.recipient = response.data.recipient;
                        this.threads = response.data.threads;
                        this.appointments = response.data.appointments;
                        this.rest = response.data.rest;
                        this.notes = response.data.recipient.notes;
                        this.labels = response.data.recipient.labels_list;

                        this.updateResponses(this.recipient);

                        this.setLoading(false);
                    })
                    .catch((response) => {
                        this.setLoading(false);
                        this.$toastr.error("Couldn't fetch responses.");
                    });
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
                        console.log(response);
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
                        console.log(response);
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
                        console.log(response);
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
                        console.log(response);
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
                        console.log(response);
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
                        console.log(response);
                        this.$toastr.error('Failed to send email.');
                    });
            },
            addLabel: function (label) {
                let selectedLabel = this.selectedLabel.value;
                if (label) {
                    selectedLabel = label;
                }

                axios.post(generateRoute(window.addLabelUrl, {'recipientId': this.recipientId}),
                    {
                        label: selectedLabel
                    })
                    .then((response) => {
                        this.$toastr.success('Label added.');
                    })
                    .catch((response) => {
                        console.log(response);
                        this.$toastr.error('Failed to add label.');
                    });
            },
            removeLabel: function (label) {
                let selectedLabel = this.selectedLabel.value;
                if (label) {
                    selectedLabel = label;
                }

                axios.post(generateRoute(window.removeLabelUrl, {'recipientId': this.recipientId}),
                    {
                        label: selectedLabel
                    })
                    .then((response) => {
                        this.$toastr.success('Label removed.');
                    })
                    .catch((response) => {
                        console.log(response);
                        this.$toastr.error('Failed to remove label.');
                    });
            },
            updateResponses: function (recipient) {

                if (recipient) {

                    window['app'].pusher('private-campaign.' + this.campaign.id, 'response.' + recipient.id + '.updated', (data) => {

                        if (data) {
                            this.setLoading(true);

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

                                        // Update labels in main recipients list
                                        this.$set(window['app'].recipients[this.recipientKey], 'labels_list_text',
                                            response.data.recipient.labels_list_text);
                                    }
                                    if (response.data.recipient.labels_list) {
                                        this.labels = response.data.recipient.labels_list;
                                    }
                                    if (response.data.recipient.labels_list) {
                                        this.labels = response.data.recipient.labels_list;
                                    }

                                    this.setLoading(false);
                                })
                                .catch((response) => {
                                    console.log(response);
                                    this.$toastr.error('Failed to update responses.');
                                    this.setLoading(false);
                                });
                        }
                    });
                }
            },
        }
    }
</script>
