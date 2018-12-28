import Vue from 'vue';
import './../../common';
import axios from 'axios';
import {merge} from 'lodash';

Vue.component('pm-responsive-table', require('./../../components/pm-responsive-table/pm-responsive-table'));

window.app = new Vue({
    el: '#campaign-index',
    data: {
        // form: new Form({
        //     company: '',
        //     keywords: '',
        // }),
        pagination: {
            page: 2,
            per_page: 15,
            total: null
        },
        pageVariables: {
            searchTerm: '',
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
            axios
                .get(this.pageVariables.formUrl, {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: null,
                    params: params
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
        onSubmit() {
            this.fetchData(merge({current_page: this.pagination.current_page + 1}, this.pagination));
            // this.form.post(this.pageVariables.formUrl);
        }
    }
});
