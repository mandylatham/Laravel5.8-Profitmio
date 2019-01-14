import Vue from 'vue';
import './../../common';
import { SearchIcon} from 'vue-feather-icons';

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
        'pm-responsive-table': require('./../../components/pm-responsive-table/pm-responsive-table')
    },
    data: {
        campaign: {},
        searchText: '',
        currentRecipientId: null,
        currentUser: {},
        rows: [],
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
        }
    },
    mounted: function () {
        this.recipients = window.recipients;
        this.campaign = window.campaign;
        this.currentUser = window.user;
    }
});
