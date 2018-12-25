<template>
    <div>
        <div class="container-fluid list-campaign-container">
            <div class="row align-items-end no-gutters mt-4 mb-3">
                <div class="col-12">

                    <button class="btn btn-primary float-right" v-on:click.prevent="closePanel">Close Panel</button>
                </div>
            </div>

            <div class="row align-items-end no-gutters mt-4 mb-3">
                <div class="col-12">

                    <dl>
                        <dt>Name</dt>
                        <dd>{{ this.recipient.name }}</dd>
                        <dt>Vehicle</dt>
                        <dd>{{ this.recipient.vehicle }}</dd>
                        <dt>Email</dt>
                        <dd>{{ this.recipient.email }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    require("./communication-side-panel.scss");
    export default {
        mounted() {
            this.getResponse(this.campaign.id, this.recipientId);
        },
        data() {
            return {
                recipient: [],
            }
        },
        props: ['campaign', 'recipientId'],
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
            getResponse: function (campaignId, recipientId) {
                const vm = this;

                axios.get('/campaign/' + campaignId + '/response/' + recipientId)
                    .then(function (response) {
                        vm.recipient = response.data.recipient;
                    })
                    .catch(function (response) {
                        console.log(response);
                    });
            },

        }
    }
</script>
