import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
import axios from 'axios';
// Toastr Library
import VueToastr2 from 'vue-toastr-2'
// Chart Library
import VueChartkick from 'vue-chartkick'
import Chart from 'chart.js'
import {filter} from 'lodash';

Vue.use(VueChartkick, {adapter: Chart});

window['app'] = new Vue({
    el: '#campaign-index',
    components: {
        'campaign': require('./../../components/campaign/campaign').default,
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination').default,
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
    },
    computed: {
        countActiveCampaigns: function () {
            return filter(this.campaigns, {
                status: 'Active'
            }).length;
        },
        countInactiveCampaigns: function () {
            return filter(this.campaigns, item => {
                return item.status !== 'Active';
            }).length;
        },
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
            company: localStorage.getItem('campaignsIndexCompany') ? JSON.parse(localStorage.getItem('campaignsIndexCompany')) : undefined,
            q: localStorage.getItem('campaignsIndexQ'),
            page: 1,
            per_page: 15,
        }),
        isLoading: true,
        total: null,
        campaigns: [],
        companies: [],
        searchTerm: '',
        companySelected: null,
        tableOptions: {
            mobile: 'lg'
        },
        formUrl: ''
    },
    mounted() {
        this.searchFormUrl = window.searchFormUrl;
        this.companySelected = localStorage.getItem('campaignsIndexCompany') ? JSON.parse(localStorage.getItem('campaignsIndexCompany')) : undefined;

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
        onCompanySelected() {
            this.searchForm.page = 1;
            return this.fetchData();
        },
        fetchData() {
            if (this.companySelected) {
                localStorage.setItem('campaignsIndexCompany', JSON.stringify(this.companySelected));
                this.searchForm.company = this.companySelected.id;
            } else {
                this.searchForm.company = null;
                localStorage.removeItem('campaignsIndexCompany');
            }
            if (this.searchForm.q) {
                localStorage.setItem('campaignsIndexQ', this.searchForm.q);
            } else {
                localStorage.removeItem('campaignsIndexQ');
            }
            this.isLoading = true;
            this.searchForm.get(this.searchFormUrl)
                .then(response => {
                    this.campaigns = response.data;
                    this.searchForm.page = response.current_page;
                    this.searchForm.per_page = response.per_page;
                    this.total = response.total;
                    this.isLoading = false;
                })
                .catch(error => {
                    window.PmEvent.fire('errors.api', "Unable to get campaigns");
                });
        },
        onPageChanged(event) {
            this.searchForm.page = event.page;
            return this.fetchData();
        }
    }
});
