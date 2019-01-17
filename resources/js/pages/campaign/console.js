import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import {SearchIcon} from 'vue-feather-icons';
import VueSlideoutPanel from 'vue2-slideout-panel';

Vue.use(VueSlideoutPanel);

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
        pusherKey: '',
        campaign: {},
    },
    mounted: function () {
        this.filter = window.filter;
        this.label = window.label;
        this.counters = window.counters;
        this.labelCounts = window.counters.labelCounts;
        this.pusherKey = window.pusherKey;
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
        pusher: function (channelName, eventName, callback) {
            // TODO: remove me when done
            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = true;

            let pusher = new Pusher(this.pusherKey, {
                cluster: 'eu',
                forceTLS: true
            });

            let channel = pusher.subscribe(channelName);
            channel.bind(eventName, function (data) {
                callback(data);
            });
        },
        updateCounters: function () {
            const vm = this;
            this.pusher('response-console', 'counters.update.' + this.campaign.id, function (data) {
                vm.labelCounts = data.labelCounts
            });
        },
    }
});

Vue.component('communication-side-panel', require('./../../page-components/campaign/communication-side-panel/communication-side-panel.component'));

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
        total: null
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
        }
    },
    mounted: function () {
        const vm = this;
        this.campaign = window.campaign;
        this.currentUser = window.user;
        this.fetchRecipients();

        // Events
        window.Event.listen('filters.filter-changed', function (data) {
            vm.searchForm.filter = data.filter;
            vm.searchForm.label = data.label;

            vm.fetchRecipients();
        });
    }
});
