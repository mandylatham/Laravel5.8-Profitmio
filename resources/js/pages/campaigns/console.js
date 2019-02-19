import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import moment from 'moment';
import {SearchIcon} from 'vue-feather-icons';
import VueSlideoutPanel from 'vue2-slideout-panel';
import {each} from 'lodash';
import PusherService from "../../common/pusher-service";
import './../../filters/m-utc-parse.filter';
import './../../filters/m-format-localized.filter';
import './../../filters/m-duration-for-humans.filter';

toastr.options.positionClass = "toast-bottom-left"; 
toastr.options.newestOnTop = true;
toastr.options.progressBar = true;

Vue.use(VueSlideoutPanel);

let pusherService = null;

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
            per_page: 30,
            filter: null,
            label: null,
            media: null
        }),
        total: null,
        pusherKey: '',
        pusherCluster: '',
        pusherAuthEndpoint: '',
    },
    filters: {
        shortDate: function(value) {
            if (value == "") { return; }
            return moment(String(value)).format('MM/DD/YYYY hh:mm A')
        }
    },
    methods: {
        fetchRecipients() {
            this.loading = true;
            this.searchForm
                .get(window.getRecipientsUrl)
                .then(response => {
                    this.recipients = response.data;
                    this.searchForm.page = response.current_page;
                    this.searchForm.per_page = response.per_page;
                    this.total = response.total;
                    this.loading = false;
                })
                .catch(error => {
                    this.loading = false;
                    this.$toastr.error('Unable to get recipient');
                });
        },
        showPanel: function (recipient, key) {
            this.currentRecipientId = recipient.id;
            this.recipientKey = key;

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
        onPageChanged: function ({page}) {
            this.searchForm.page = page;
            return this.fetchRecipients();
        },
        registerGlobalEventListeners() {
            // Events
            window.Event.listen('removed.recipient.label', (data) => {
                this.recipients.forEach((recipient, index) => {
                    if (recipient.id === data.recipientId) {
                        this.$delete(this.recipients[index].labels, data.label);
                    }
                });
            });

            window.Event.listen('added.recipient.label', (data) => {
                this.recipients.forEach((recipient, index) => {
                    if (recipient.id === data.recipientId) {
                        this.$set(this.recipients[index].labels, data.label, data.labelText);
                    }
                });
            });

            window.Event.listen('filters.filter-changed', (data) => {
                if (data.filter === 'media') {
                    this.searchForm.media = data.value;
                } else if (data.filter === 'filter') {
                    this.searchForm.filter = data.value;
                } else if (data.filter === 'label') {
                    this.searchForm.label = data.value;
                }
                this.fetchRecipients();
            });

        }
    },
    mounted() {
        this.campaign = window.campaign;
        this.currentUser = window.user;
        this.pusherKey = window.pusherKey;
        this.pusherCluster = window.pusherCluster;
        this.pusherAuthEndpoint = window.pusherAuthEndpoint;

        // this.fetchRecipients();
        
        this.registerGlobalEventListeners();
    }
});

// Sidebar
window['sidebar'] = new Vue({
    el: '#sidebar-nav-content',
    data: {
        activeFilterSection: 'all',
        counters: {},
        campaign: {},
        baseUrl: window.baseUrl
    },
    mounted: function () {
        each(window.counters, (value, key) => {
            Vue.set(this.counters, key, value);
        });
        this.campaign = window.campaign;

        pusherService = new PusherService();

        if (window.filterApplied) {
            this.changeFilter('filter', window.filterApplied);
        } else {
            this.changeFilter('filter', this.activeFilterSection);
        }

        this.registerPusherListeners();
    },
    methods: {
        changeFilter: function (filter, value) {
            let newUrl = window.baseUrl;
            newUrl += value;
            if (newUrl !== window.location.href) {
                window.history.pushState("", "", newUrl);
            }
            this.activeFilterSection = value;

            window.Event.fire('filters.filter-changed', {
                filter: filter,
                value: value
            });
        },
        registerPusherListeners: function () {
            pusherService
                .subscribe('private-campaign.' + this.campaign.id)
                .bind('counts.updated', (data) => {
                    each(data, (value, key) => {
                        Vue.set(this.counters, key, value);
                    });
                });
        },
    }
});

Vue.component('communication-side-panel', require('./../../page-components/campaign/communication-side-panel/communication-side-panel.component'));
