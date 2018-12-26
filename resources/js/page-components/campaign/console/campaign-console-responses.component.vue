<template>
    <div class="container-fluid list-campaign-container">

        <div class="row align-items-end no-gutters">
            <div class="col-6">
                <button class="btn btn-round btn-sm">
                    <chevron-left-icon class="chevron-left-icon"></chevron-left-icon>
                    Home
                </button>
            </div>
            <div class="col-6">
                <div class="input-group mb-2 float-right">
                    <div class="input-group-prepend">
                        <search-icon class="search-icon"></search-icon>
                    </div>
                    <input type="text" class="form-control" v-model="searchText">
                </div>
            </div>
        </div>

        <div class="row align-items-end no-gutters mt-4 mb-3">
            <div class="col-12">
                <a class="icon" href="javascript:;"><img src="../../../../img/icons/folder.png" alt="folder"></a>
                <a class="icon" href="javascript:;"><img src="../../../../img/icons/tag.png" alt="tag"></a>
            </div>
        </div>

        <div class="row align-items-end no-gutters">
            <div class="col-12">
                <!-- TODO: pass current `recipientId` to `showPanel` method -->
                <pm-responsive-table :rows="rows" :columns="columns" :disable-folding="true"
                                     v-on:row-clicked="showPanel">
                </pm-responsive-table>
            </div>
        </div>

        <div class="row align-items-end no-gutters">
            <div class="col-12">

            </div>
        </div>

        <slideout-panel></slideout-panel>
    </div>
</template>
<script>
    require("./campaign-console-responses.scss");
    import {ChevronLeftIcon} from 'vue-feather-icons';
    import {SearchIcon} from 'vue-feather-icons';

    export default {
        mounted() {
            this.getCurrentUser();
        },
        data() {
            return {
                searchText: '',
                currentRecipientId: null,
                currentUser: [],
                rows: this.recipients.data,
                columns: [
                    {
                        field: 'name',
                        classes: ['console-response-name']
                    },
                    {
                        field: 'email',
                        classes: ['console-response-email']
                    },
                    {
                        field: 'last_seen_ago',
                        classes: ['console-response-date']
                    }
                ],
                rowsTest: [],
                panel1Form: {
                    openOn: 'right'
                }
            };
        },
        props: ['campaign', 'recipients'],
        components: {
            ChevronLeftIcon,
            SearchIcon,
        },
        methods: {
            showPanel: function (event) {
                this.currentRecipientId = event.row.id;
                const panel = this.$showPanel({
                    component: 'communication-side-panel',
                    cssClass: 'communication-side-panel',
                    width: '50%',
                    props: {
                        campaign: this.campaign,
                        recipientId: this.currentRecipientId,
                        currentUser: this.currentUser,
                    }
                });
            },
            getCurrentUser: function () {
                const vm = this;

                axios.get('/current-user')
                    .then(function (response) {
                        vm.currentUser = response.data;
                    })
                    .catch(function (response) {
                        console.log(response);
                    });
            },
        },
        computed: {
            // filterTable() {
            //     // let searchString = this.searchText;
            //     return this.rows.filter((row) => {
            //         row.name.indexOf(this.searchText) !== -1;
            //     })
            // }
        }
    }
</script>
