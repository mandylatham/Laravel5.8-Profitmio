import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import moment from 'moment';
import {SearchIcon} from 'vue-feather-icons';
import VueSlideoutPanel from 'vue2-slideout-panel';

toastr.options.positionClass = "toast-bottom-left"; 
toastr.options.newestOnTop = true;
toastr.options.progressBar = true;

Vue.use(VueSlideoutPanel);

// Main vue
window['app'] = new Vue({
    el: '#console',
    components: {
        SearchIcon,
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination'),
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon'),
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
        recipientKey: null,
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
    filters: {
        shortDate: function(value) {
            return moment(String(value)).format('MM/DD/YYYY hh:mm A')
        }
    },
    methods: {
        fetchRecipients() {
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
        showPanel: function (recipient, key) {
            this.currentRecipientId = recipient.id;
            this.recipientKey = key;

            console.log('window.innerWidth', window.innerWidth);

            const panel = this.$showPanel({
                component: 'communication-side-panel',
                cssClass: 'communication-side-panel',
                width: window.innerWidth >= 768 ? '50%' : '100%',
                disableBgClick: false,
                props: {
                    campaign: this.campaign,
                    recipientId: this.currentRecipientId,
                    currentUser: this.currentUser,
                    recipientKey: this.recipientKey
                }
            });
        },
        clearSearch: function () {
            this.searchForm.search = '';
            this.fetchRecipients();
        },
        // toggleSidebar: function () {
        //     let app = document.getElementById('app');
        //
        //     if (app.classList.contains('side-menu-open')) {
        //         app.classList.add('navbar-side-menu-fix');
        //         app.classList.remove('side-menu-open');
        //         app.classList.remove('navbar-side-menu-fix');
        //     } else {
        //         app.classList.add('side-menu-open');
        //     }
        // },
        pusherInit: function () {
            // TODO: Enable pusher logging - don't include this in production
            Pusher.logToConsole = true;

            return new Pusher(this.pusherKey, {
                cluster: this.pusherCluster,
                forceTLS: true,
                authEndpoint: this.pusherAuthEndpoint,
                auth: {
                    headers: {
                        'X-CSRF-Token': window.csrfToken
                    }
                }
            });
        },
        pusher: function (channelName, eventName, callback) {
            let pusher = this.pusherInit();

            let channel = pusher.subscribe(channelName);
            channel.bind(eventName, function (data) {
                callback(data);
            });
        },
        pusherUnbindEvent: function (channelName, eventName) {
            let pusher = this.pusherInit();

            let channel = pusher.subscribe(channelName);
            channel.unbind(eventName);
        },
        updateRecipients() {
            this.pusher('private-campaign.' + this.campaign.id, 'recipients.updated', (data) => {
                this.recipients = data.recipients.data;
                this.searchForm.page = data.recipients.current_page;
                this.searchForm.per_page = data.recipients.per_page;
                this.total = data.recipients.total;
            });
        }
    },
    mounted() {
        this.campaign = window.campaign;
        this.currentUser = window.user;
        this.pusherKey = window.pusherKey;
        this.pusherCluster = window.pusherCluster;
        this.pusherAuthEndpoint = window.pusherAuthEndpoint;

        this.fetchRecipients();
        this.updateRecipients();

        // Events
        window.Event.listen('filters.filter-changed', (data) => {
            this.searchForm.filter = data.filter;
            this.searchForm.label = data.label;

            this.fetchRecipients();
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
        labelCounts: {},
        campaign: {},
    },
    mounted: function () {
        console.log('window.counters', window.counters);
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
            window['app'].pusher('private-campaign.' + this.campaign.id, 'counts.updated', (data) => {
                this.labelCounts = data.labelCounts
            });
        },
    }
});

Vue.component('communication-side-panel', require('./../../page-components/campaign/communication-side-panel/communication-side-panel.component'));
