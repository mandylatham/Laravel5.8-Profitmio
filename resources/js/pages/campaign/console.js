import Vue from 'vue';
import './../../common';

// SIDEBAR
window['console'] = new Vue({
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
        'campaign-console-responses': require('./../../page-components/campaign/console-responses/campaign-console-responses.component'),
    },
});
