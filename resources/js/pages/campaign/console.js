import Vue from 'vue';
import './../../common';
import axios from 'axios';
import Form from './../../common/form';
import { SearchIcon} from 'vue-feather-icons';
import VueSlideoutPanel from 'vue2-slideout-panel';
Vue.use(VueSlideoutPanel);

// SIDEBAR
window['sidebar'] = new Vue({
    el: '#sidebar-nav-content',
    data: {
        // TODO: PUT EVERYTHING THAT NEEDS TO BE HERE
        activeFilterSection: 'all',
        activeMediaTypeSection: '',
        activeLabelSection: '',
        filter: '',
        label: '',
        counters: []
    },
    mounted: function () {
        this.filter = window.filter;
        this.label = window.label;
        this.counters = window.counters;
    },
    methods: {
        changeFilter: function (item) {
            this.activeFilterSection = item;
            this.asideOpen = false;
        },
        changeMediaType: function (item) {
            this.activeMediaTypeSection = item;
            this.asideOpen = false;
        },
        changeLabel: function (item) {
            this.activeLabelSection = item;
            this.asideOpen = false;
        }
    }
});

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
        searchText: '',
        searchForm: new Form({
            q: null,
            page: 1,
            per_page: 15,
        }),
        total: null
    },
    methods: {
        fetchRecipients: function () {
            this.loading = true;
            this.searchForm.get(window.getRecipientsUrl)
                .then(response => {
                    this.recipients = response.data;
                    this.searchForm.page = response.current_page;
                    this.searchForm.per_page = response.per_page;
                    this.total = response.total;
                    this.loading = false;
                })
                .catch(error => {
                    this.$toastr.error('Unable to get recipient');
                });
        },
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
        }
    },
    mounted: function () {
        this.campaign = window.campaign;
        this.currentUser = window.user;
        this.fetchRecipients();
    }
});
