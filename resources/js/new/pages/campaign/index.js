import Vue from 'vue';
import VueToastr2 from 'vue-toastr-2'
import './../../common';
import Form from './../../common/form';
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
import axios from 'axios';
import {merge} from 'lodash';
window.toastr = require('toastr');
Vue.use(VueToastr2);

Vue.component('pm-responsive-table', require('./../../components/pm-responsive-table/pm-responsive-table'));
Vue.component('spinner-icon', require('./../../components/spinner-icon/spinner-icon'));

window.app = new Vue({
    el: '#campaign-index',
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
        searchFormUrl: null,
        searchForm: new Form({
            company: null,
            q: null,
            page: 1,
            per_page: 15,
        }),
        isLoading: true,
        total: null,
        campaigns: [],
        companies: [],
        searchTerm: '',
        companySelected: null,
        columnData: [
            {
                slot: 'name',
                is_manager: true,
                classes: ['name-col'],
                widths: {
                    'lg': '100px'
                }
            }, {
                field: 'dealership.name',
                classes: ['dealership-col'],
                widths: {
                    'lg': '100px'
                }
            }, {
                field: 'agency.name',
                classes: ['agency-col'],
                widths: {
                    'lg': '100px'
                }
            }, {
                slot: 'recipients_count',
                classes: ['recipients-col'],
                widths: {
                    'lg': '100px'
                }
            }, {
                slot: 'phone_responses_count',
                classes: ['phone-responses-col'],
                widths: {
                    'lg': '100px'
                }
            }, {
                slot: 'email_responses_count',
                classes: ['email-responses-col'],
                widths: {
                    'lg': '100px'
                }
            }, {
                slot: 'text_responses_count',
                classes: ['text-responses-col'],
                widths: {
                    'lg': '100px'
                }
            }, {
                slot: 'options',
                is_manager_footer: true,
                classes: ['options-col'],
                widths: {
                    'lg': '100px'
                }
            }
        ],
        tableOptions: {
            mobile: 'lg'
        },
        formUrl: ''
    },
    mounted() {
        this.searchFormUrl = window.searchFormUrl;
        this.searchForm.company = window.companySelected;
        this.searchForm.q = window.q;

        axios
            .get(window.getCompanyUrl, {
                headers: {
                    'Content-Type': 'application/json'
                },
                params: {
                    per_page: 100
                },
                data: null
            })
            .then(response => {
                this.companies = response.data.data;
            });

        this.fetchData();
    },
    methods: {
        fetchData() {
            this.isLoading = true;
            this.searchForm.get(this.searchFormUrl)
                .then(response => {
                    this.campaigns = response.data;
                    this.searchForm.page = response.current_page;
                    this.searchForm.per_page = response.per_page;
                    this.total= response.total;
                    this.isLoading = false;
                })
                .catch(error => {
                    this.$toastr.error("Unable to get campaigns");
                });
        },
        onPageChanged(event) {
            if (event) {
                this.searchForm.page = event.page;
            }
            return this.fetchData();
        }
    }
});
