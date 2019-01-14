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
                            <i class="icon fa-car"></i>
                            {{ this.recipient.vehicle }}
                        </div>
                        <div class="email" v-if="this.recipient.email">
                            <i class="icon fa-envelope"></i>
                            {{ this.recipient.email }}
                        </div>
                        <div class="phone" v-if="this.recipient.phone">
                            <i class="icon fa-phone"></i>
                            {{ this.recipient.phone }}
                        </div>
                    </div>
                </div>

                <div class="row align-items-end no-gutters mt-4 mb-3">
                    <div class="col-12">

                        <label for="labels" class="form-check-label">Add Label</label>
                        <select name="label" class="form-control" id="labels" v-model="selectedLabel"
                                @change="addLabel(recipientId)">
                            <option value="interested">Interested</option>
                            <option value="service">Service Dept</option>
                            <option value="not_interested">Not Interested</option>
                            <option value="wrong_number">Wrong Number</option>
                            <option value="car_sold">Car Sold</option>
                            <option value="heat">Heat Case</option>
                        </select>

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
                                <i class="icon fa-phone"></i>
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
                                <i class="icon fa-calendar"></i>
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
                            <i class="icon fa-phone"></i>
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
                                            @click="sendText(campaign.id, recipientId)">
                                        <i class="icon md-mail-send" aria-hidden="true"></i> Send
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

                            <div class="message original-message">
                                Original Message
                                <iframe class="email-original" :src="threads.emailDrop.email_html"></iframe>
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
                                            @click="sendEmail(campaign.id, recipientId)">
                                        <i class="icon md-mail-send" aria-hidden="true"></i> Send
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
    require("./communication-side-panel.scss");
    export default {
        mounted() {
            this.resetVars();
            this.getResponses(this.campaign.id, this.recipientId);
            console.log(this.recipientId);
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
                selectedLabel: ''
            }
        },
        props: ['campaign', 'recipientId', 'currentUser'],
        computed: {
            //
        },
        watch: {
            //
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
                this.notes = '';
                this.calledCheckbox = false;
                this.appointmentSelectedDate = '';
                this.appointmentSelectedTime = '';
                this.textMessage = '';
                this.emailMessage = '';
                this.selectedLabel = '';
            },
            getResponses: function (campaignId, recipientId) {
                const vm = this;
                vm.setLoading(true);

                axios.get('/campaign/' + campaignId + '/response/' + recipientId)
                    .then(function (response) {
                        vm.recipient = response.data.recipient;
                        vm.threads = response.data.threads;
                        vm.appointments = response.data.appointments;
                        vm.rest = response.data.rest;
                        vm.notes = response.data.recipient.notes;

                        // TODO: remove me
                        console.log(vm.recipient);
                        console.log(vm.threads);

                        vm.setLoading(false);
                    })
                    .catch(function (response) {
                        // TODO: error
                        console.log(response);
                    });
            },
            setLoading: function (bool) {
                this.loading = bool;
            },
            addNotes: function (recipientId) {
                const vm = this;

                axios.post('/recipient/' + recipientId + '/update-notes',
                    {
                        notes: vm.notes
                    })
                    .then(function (response) {
                        // TODO: success
                        console.log(response);
                    })
                    .catch(function (response) {
                        // TODO: error
                        console.log(response);
                    });
            },
            appointmentCalledBackToggle: function (event, appointmentId) {
                axios.post('/appointment/' + appointmentId + '/update-called-status',
                    {
                        called_back: event.target.checked
                    })
                    .then(function (response) {
                        // TODO: success
                        console.log(response);
                    })
                    .catch(function (response) {
                        // TODO: error
                        console.log(response);
                    });
            },
            addAppointment: function (campaignId, recipientId) {
                const vm = this;

                axios.post('/campaign/' + campaignId + '/responses/' + recipientId + '/add-appointment',
                    {
                        appointment_date: vm.appointmentSelectedDate,
                        appointment_time: vm.appointmentSelectedTime
                    })
                    .then(function (response) {
                        // TODO: success
                        console.log(response);
                    })
                    .catch(function (response) {
                        // TODO: error
                        console.log(response);
                    });
            },
            messageUpdateReadStatus: function (event, textId) {
                axios.post('/response/' + textId + '/update-read-status',
                    {
                        read: event.target.checked
                    })
                    .then(function (response) {
                        // TODO: success
                        console.log(response);
                    })
                    .catch(function (response) {
                        // TODO: error
                        console.log(response);
                    });
            },
            sendText: function (campaignId, recipientId) {
                const vm = this;

                axios.post('/campaign/' + campaignId + '/text-response/' + recipientId,
                    {
                        message: vm.textMessage
                    })
                    .then(function (response) {
                        // TODO: success
                        console.log(response);
                    })
                    .catch(function (response) {
                        // TODO: error
                        console.log(response);
                    });
            },
            sendEmail: function (campaignId, recipientId) {
                const vm = this;

                axios.post('/campaign/' + campaignId + '/email-response/' + recipientId,
                    {
                        message: vm.emailMessage
                    })
                    .then(function (response) {
                        // TODO: success
                        console.log(response);
                    })
                    .catch(function (response) {
                        // TODO: error
                        console.log(response);
                    });
            },
            addLabel: function (recipientId) {
                const vm = this;

                axios.post('/recipient/' + recipientId + '/add-label',
                    {
                        label: vm.selectedLabel
                    })
                    .then(function (response) {
                        // TODO: success
                        console.log(response);
                    })
                    .catch(function (response) {
                        // TODO: error
                        console.log(response);
                    });
            },
            removeLabel: function (recipientId) {
                // TODO: click on displayed label should remove it
                const vm = this;

                axios.post('/recipient/' + recipientId + '/remove-label',
                    {
                        label: vm.selectedLabel
                    })
                    .then(function (response) {
                        // TODO: success
                        console.log(response);
                    })
                    .catch(function (response) {
                        // TODO: error
                        console.log(response);
                    });
            },
        }
    }
</script>
