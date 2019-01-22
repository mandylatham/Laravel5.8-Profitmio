import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import {SearchIcon} from 'vue-feather-icons';
import VueSlideoutPanel from 'vue2-slideout-panel';
// TODO: how to include VueToastr2?
// import 'vue-toastr-2/dist/vue-toastr-2.min.css'
// import VueToastr2 from 'vue-toastr-2';
// window.toastr = require('toastr');
// Vue.use(VueToastr2);

Vue.use(VueSlideoutPanel);

// Main vue
window['app'] = new Vue({
    el: '#console',
    components: {
        SearchIcon,
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination'),
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon')
    },
    computed: {
        pagination: function () {
            return {
                page: this.searchForm.page,
                per_page: this.searchForm.per_page,
                total: this.total
            };
        }
    },
    data: {
        campaign: {},
        currentRecipientId: null,
        currentUser: {},
        loading: false,
        panel1Form: {
            openOn: 'right'
        },
        recipients: [],
        rowsTest: [],
        searchForm: new Form({
            search: null,
            page: 1,
            per_page: 15,
            filter: null,
            label: null,
            mediaType: null
        }),
        total: null,
        pusherKey: '',
        pusherCluster: '',
        pusherAuthEndpoint: '',
    },
    methods: {
        fetchRecipients: function () {
            this.loading = true;
            this.searchForm.get(window.getRecipientsUrl)
                .then(response => {
                    this.recipients = response.recipients.data;
                    this.searchForm.page = response.recipients.current_page;
                    this.searchForm.per_page = response.recipients.per_page;
                    this.total = response.recipients.total;

                    // this.updateRecipients(this.recipients);

                    this.loading = false;
                })
                .catch(error => {
                    this.$toastr.error('Unable to get recipient');
                });
        },
        showPanel: function (recipient) {
            this.currentRecipientId = recipient.id;
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
        clearSearch: function () {
            this.searchForm.search = '';
            this.fetchRecipients();
        },
        pusher: function (channelName, eventName, callback) {
            // TODO: remove me when done
            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = true;

            let pusher = new Pusher(this.pusherKey, {
                cluster: this.pusherCluster,
                forceTLS: true,
                authEndpoint: this.pusherAuthEndpoint,
                auth: {
                    headers: {
                        'X-CSRF-Token': window.csrfToken
                    }
                }
            });

            let channel = pusher.subscribe(channelName);
            channel.bind(eventName, function (data) {
                callback(data);
            });
        },
        updateRecipients: function () {
            // TODO: check this out
            const vm = this;
            this.pusher('private-campaign.' + this.campaign.id, 'recipients.updated', function (data) {
                console.log(data);

                vm.recipients = data.recipients.data;
                vm.searchForm.page = data.recipients.current_page;
                vm.searchForm.per_page = data.recipients.per_page;
                vm.total = data.recipients.total;
            });
        }
    },
    mounted: function () {
        const vm = this;
        this.campaign = window.campaign;
        this.currentUser = window.user;
        this.pusherKey = window.pusherKey;
        this.pusherCluster = window.pusherCluster;
        this.pusherAuthEndpoint = window.pusherAuthEndpoint;

        this.fetchRecipients();
        this.updateRecipients();

        // Events
        window.Event.listen('filters.filter-changed', function (data) {
            vm.searchForm.filter = data.filter;
            vm.searchForm.label = data.label;

            vm.fetchRecipients();
        });
    }
});


// Sidebar
window['sidebar'] = new Vue({
    el: '#sidebar-nav-content',
    data: {
        activeFilterSection: 'all',
        activeLabelSection: '',
        filter: '',
        label: '',
        counters: [],
        labelCounts: [],
        campaign: {},
    },
    mounted: function () {
        this.filter = window.filter;
        this.label = window.label;
        this.counters = window.counters;
        this.labelCounts = window.counters.labelCounts;
        this.campaign = window.campaign;
        this.updateCounters();
    },
    methods: {
        changeFilter: function (filter, label) {
            this.activeFilterSection = filter;

            if (label) {
                this.activeLabelSection = label;
            }

            window.Event.fire('filters.filter-changed', {
                filter: filter,
                label: label ? label : ''
            });
        },
        updateCounters: function () {
            const vm = this;
            window['app'].pusher('private-campaign.' + this.campaign.id, 'counts.updated', function (data) {
                vm.labelCounts = data.labelCounts
            });
        },
    }
});

Vue.component('communication-side-panel', require('./../../page-components/campaign/communication-side-panel/communication-side-panel.component'));