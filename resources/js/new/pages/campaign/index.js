import Vue from 'vue';
import './../../common';
import axios from 'axios';
import {merge} from 'lodash';

Vue.component('pm-responsive-table', require('./../../components/pm-responsive-table/pm-responsive-table'));

window.app = new Vue({
    el: '#campaign-index',
    data: {
        pagination: {
            page: 1,
            per_page: 15,
            total: null
        },
        pageVariables: {
            campaigns: [],
            columns: [
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
            formUrl: ''
        },
        filters: {
            searchTerm: '',
            companySelected: null
        }
    },
    mounted() {
        this.fetchData(this.pagination);
    },
    methods: {
        addPageVariables(name, value) {
            this.pageVariables = Object.assign({}, this.pageVariables, {[name]: value})
        },
        getPageVariable(name) {
            return this.pageVariables[name];
        },
        fetchData(params) {
            const p = {...params};
            if (this.filters.searchTerm) {
                p.q = this.filters.searchTerm;
            }
            if (this.filters.companySelected) {
                p.company = this.filters.companySelected;
            }
            axios
                .get(this.pageVariables.formUrl, {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: null,
                    params: p
                })
                .then(response => {
                    this.addPageVariables('campaigns', response.data.data);
                    this.pagination = {
                        page: response.data.current_page,
                        per_page: response.data.per_page,
                        total: response.data.total
                    };
                })
                .catch(error => this.$toastr.e(error.response.data));
        },
        fetchMoreData() {
            return this.fetchData(merge({page: this.pagination.page + 1}, this.pagination));
        }
    }
});
