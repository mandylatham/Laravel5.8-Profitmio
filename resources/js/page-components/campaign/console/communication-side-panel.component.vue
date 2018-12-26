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

                <div class="mail-content">
                    <div class="form-group">
                        <textarea class="form-control" placeholder="Notes..." name="notes"
                                  v-model="notes">{{ this.recipient.notes }}</textarea>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-primary" @click="addNotes(recipientId)">Save</button>
                    </div>
                </div>

                <div class="mail-attachments" v-if="this.threads.length > 0">
                    <h4>Calls</h4>
                    <ul class="list-group">

                        <li class="list-group-item" v-for="call in this.threads.phone">
                            <i class="icon fa-phone"></i>
                            Called at {{ call.created_at }}

                            <div v-if="currentUser.is_admin === 1">
                                <div class="audio-player" v-if="call.recording_url.length > 0">
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

            </div>
        </div>
    </div>
</template>

<script>
    require("./communication-side-panel.scss");
    export default {
        mounted() {
            this.getResponses(this.campaign.id, this.recipientId);
            console.log(this.currentUser);
        },
        data() {
            return {
                recipient: [],
                threads: [],
                loading: false,
                notes: ''
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
            getResponses: function (campaignId, recipientId) {
                const vm = this;
                vm.setLoading(true);

                axios.get('/campaign/' + campaignId + '/response/' + recipientId)
                    .then(function (response) {
                        vm.recipient = response.data.recipient;
                        vm.threads = response.data.threads;
                        console.log(vm.recipient);
                        console.log(vm.threads);
                        vm.setLoading(false);
                    })
                    .catch(function (response) {
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
                        console.log(response);
                    })
                    .catch(function (response) {
                        console.log(response);
                    });
            }
        }
    }
</script>
