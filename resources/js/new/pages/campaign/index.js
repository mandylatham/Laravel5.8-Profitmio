import Vue from 'vue';
import VueToastr2 from 'vue-toastr-2'
import './../../common';
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
import axios from 'axios';
import {merge} from 'lodash';
window.toastr = require('toastr');
Vue.use(VueToastr2);

Vue.component('pm-responsive-table', require('./../../components/pm-responsive-table/pm-responsive-table'));
Vue.component('spinner-icon', require('./../../components/spinner-icon/spinner-icon'));

window.app = new Vue({
    el: '#campaign-index',
    data: {
        isLoading: true,
        pagination: {
            page: 1,
            per_page: 15,
            total: null
        },
        campaigns: [],
        companies: [],
        columnData: [
            {
                field: 'name',
                is_manager: true,
                classes: ['name-col']
            }, {
                field: 'dealership.name',
                classes: ['dealership-col']
            }, {
                field: 'agency.name',
                classes: ['agency-col']
            }, {
                field: 'recipients_count',
                classes: ['recipients-col']
            }, {
                field: 'phone_responses_count',
                classes: ['phone-responses-col']
            }, {
                field: 'email_responses_count',
                classes: ['email-responses-col']
            }, {
                field: 'text_responses_count',
                classes: ['text-responses-col']
            }, {
                field: 'options',
                is_manager_footer: true,
                classes: ['options-col']
            }
        ],
        formUrl: '',
        filters: {
            searchTerm: '',
            companySelected: null
        }
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        setFormUrl(formUrl) {
            this.formUrl = formUrl;
        },

        fetchData(params = {}) {
            const p = {
                page: this.pagination.page,
                per_page: this.pagination.per_page,
                ...params
            };
            if (this.filters.searchTerm) {
                p.q = this.filters.searchTerm;
            }
            if (this.filters.companySelected) {
                p.company = this.filters.companySelected;
            }
            this.isLoading = true;
            axios
                .get(this.formUrl, {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: null,
                    params: p
                })
                .then(response => {
                    this.campaigns = response.data.data;
                    this.pagination = {
                        page: response.data.current_page,
                        per_page: response.data.per_page,
                        total: response.data.total
                    };
                    this.isLoading = false;
                })
                .catch(error => {
                    console.log(error);
                    this.$toastr.error("Unable to get campaigns");
                });
        },

        onPageChanged({page}) {
            return this.fetchData(merge({page}));
        }
    }
});
