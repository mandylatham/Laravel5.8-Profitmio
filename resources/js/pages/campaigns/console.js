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
import {generateRoute} from './../../common/helpers';
import Modal from 'bootstrap-vue';
Vue.use(Modal);
import { BFormCheckbox } from 'bootstrap-vue';
Vue.component('checkbox', BFormCheckbox);

toastr.options.positionClass = "toast-bottom-left";
toastr.options.newestOnTop = true;
toastr.options.progressBar = true;

Vue.use(VueSlideoutPanel);

let pusherService = null;

// Main vue
window.app = new Vue({
    el: '#console',
    components: {
        SearchIcon,
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination').default,
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
        'countdown': require('./../../components/countdown/countdown').default,
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
        closeLeadForm: new Form({
            outcome: null,
            tags: [],
        }),
        currentRecipientId: null,
        currentUser: {},
        recipientKey: null,
        leadTags: window.leadTags,
        loading: false,
        mediaOption: null,
        mediaOptions: [
            {
                label: 'Email',
                value: 'email'
            },
            {
                label: 'SMS',
                value: 'text'
            },
            {
                label: 'Call',
                value: 'phone'
            }
        ],
        panel1Form: {
            openOn: 'right'
        },
        positiveOptions: window.positiveTags,
        negativeOptions: window.negativeTags,
        featureTags: window.featureTags,
        recipients: [],
        rowsTest: [],
        searchForm: new Form({
            search: null,
            page: 1,
            per_page: 30,
            status: null,
            labels: null,
            media: null
        }),
        searchTags: [],
        total: null,
        pusherKey: '',
        pusherCluster: '',
        pusherAuthEndpoint: '',
        leadClosePositiveDetails: false,
        leadCloseNegativeDetails: false,
        closingLead: null,
        closed_details: [],
        trueValue: true,
        textToValueRequestedTag: window.textToValueRequestedTag,
        checkedInTextToValueTag: window.checkedInTextToValueTag,
    },
    filters: {
        shortDate: function(value) {
            if (value == "") { return; }
            return moment(String(value)).format('MM/DD/YYYY hh:mm A');
        }
    },
    methods: {
        test() {
            console.log(this.mediaOption);
        },
        fetchRecipients(resetPagination = false) {
            this.loading = true;
            this.searchForm.labels = this.searchTags.map(t => t.name);
            if (resetPagination) {
                this.searchForm.page = 1;
            }
            this.searchForm
                .get(window.getRecipientsUrl)
                .then((response) => {
                    this.recipients = response.data;
                    this.searchForm.page = response.meta.current_page;
                    this.searchForm.per_page = response.meta.per_page;
                    this.total = response.meta.total;
                    this.loading = false;
                })
                .catch((error) => {
                    this.loading = false;
                    window.PmEvent.fire('errors.api', 'Unable to get recipient');
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
        closeLead: function (lead) {
            this.closingLead = lead;
            this.$refs.closeLeadModalRef.show();
        },
        cancelCloseLead: function (lead) {
            this.closingLead = null;
            this.$refs.closeLeadModalRef.hide();
            this.closeLeadForm.tags = [];
            this.closeLeadForm.outcome = null;
        },
        sendCloseForm: function () {
            this.closeLeadForm.post(generateRoute(window.closeLeadUrl, {leadId: this.closingLead.id}))
                .then((response) => {
                    this.recipients.forEach((recipient, index) => {
                        if (recipient.id === response.data.id) {
                            if (this.searchForm.status == 'new' || this.searchForm.status == 'open') {
                                this.recipients.splice(index, 1);
                            } else {
                                this.$set(this.recipients[index], 'status', response.data.status);
                            }
                        }
                    });
                    window.PmEvent.fire('recipient.closed', response.data);
                    this.$toastr.success("Lead has been closed");
                    this.cancelCloseLead();

                })
                .catch((err) => {
                    // @TODO other stuff
                    this.$toastr.error(err);
                });
        },
        selectPositiveOutcome: function () {
            this.closeLeadForm.tags = [];
            if (this.closingLead.text_to_value_requested) {
                this.closeLeadForm.tags.push(window.textToValueRequestedTag.name);
            }
            if (this.closingLead.checked_in) {
                this.closeLeadForm.tags.push(window.checkedInTextToValueTag.name);
            }
            this.closeLeadForm.outcome = 'positive';
        },
        selectNegativeOutcome: function () {
            this.closeLeadForm.tags = [];
            if (this.closingLead.text_to_value_requested) {
                this.closeLeadForm.tags.push(window.textToValueRequestedTag.name);
            }
            if (this.closingLead.checked_in) {
                this.closeLeadForm.tags.push(window.checkedInTextToValueTag.name);
            }
            this.closeLeadForm.outcome = 'negative';
        },
        registerGlobalEventListeners() {
            // Events
            window.PmEvent.listen('removed.recipient.label', (data) => {
                this.recipients.forEach((recipient, index) => {
                    if (recipient.id === data.recipientId) {
                        this.$delete(this.recipients[index].labels, data.label);
                    }
                });
            });

            window.PmEvent.listen('added.recipient.label', (data) => {
                this.recipients.forEach((recipient, index) => {
                    if (recipient.id === data.recipientId) {
                        this.$set(this.recipients[index].labels, data.label, data.labelText);
                    }
                });
            });

            window.PmEvent.listen('changed.recipient.status', (data) => {
                this.recipients.forEach((recipient, index) => {
                    if (recipient.id === data.id) {
                        this.$set(this.recipients[index], 'status', data.status);
                    }
                });
            });

            window.PmEvent.listen('filters.filter-changed', (data) => {
                if (data.filter === 'media') {
                    this.searchForm.media = data.value;
                } else if (data.filter === 'status') {
                    this.searchForm.status = data.value;
                    window.displayFilter = data.value;
                } else if (data.filter === 'label') {
                    this.searchForm.label = data.value;
                } else if (data.filter === 'reset') {
                    this.searchForm.status = '';
                    this.searchForm.media = '';
                    this.searchForm.label = '';
                }
                this.fetchRecipients(1);
            });

            window.PmEvent.listen('lead.close-request', (data) => {
                this.closeLead(data);
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

        this.registerGlobalEventListeners();
    }
});

// Sidebar
window.sidebar = new Vue({
    el: '#sidebar-nav-content',
    data: {
        activeFilterSection: 'all',
        baseUrl: window.baseUrl,
        counters: {},
        campaign: {},
        loggedUser: {}
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

        this.loggedUser = window['app'].currentUser;

        this.registerPusherListeners();
    },
    methods: {
        changeFilter: function (filter, value) {
            this.activeFilterSection = value;

            window.PmEvent.fire('filters.filter-changed', {
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

Vue.component('communication-side-panel', require('./../../page-components/campaign/communication-side-panel/communication-side-panel.component').default);
