import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
import axios from 'axios';
// Toastr Library
import VueToastr2 from 'vue-toastr-2'
window.toastr = require('toastr');
Vue.use(VueToastr2);
// Chart Library
import VueChartkick from 'vue-chartkick'
import Chart from 'chart.js'
import {filter} from 'lodash';

Vue.use(VueChartkick, {adapter: Chart});

window['app'] = new Vue({
    el: '#user-view',
    components: {
        'campaign': require('./../../components/campaign/campaign'),
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination'),
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon'),
    },
    computed: {
        countCompanies: function () {
            console.log('this', this);
            return this.companiesForList.length;
        },
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
        campaignsPagination: function () {
            return {
                page: this.searchCampaignForm.page,
                per_page: this.searchCampaignForm.per_page,
                total: this.total
            };
        }
    },
    data: {
        searchCampaignFormUrl: null,
        searchCompanyForm: new Form({
            q: null,
            page: 1,
            per_page: 15,
            user: null
        }),
        searchCampaignForm: new Form({
            company: null,
            q: null,
            page: 1,
            per_page: 15,
            user: null
        }),
        loadingCompanies: true,
        loadingCampaigns: true,
        total: null,
        campaigns: [],
        companies: [],
        companiesForList: [],
        searchTerm: '',
        campaignCompanySelected: null,
        tableOptions: {
            mobile: 'lg'
        },
        formUrl: ''
    },
    mounted() {
        this.campaignCompanySelected = window.campaignCompanySelected;
        this.searchCampaignForm.q = window.q;

        axios
            .get(window.getCompanyUrl, {
                headers: {
                    'Content-Type': 'application/json'
                },
                params: {
                    per_page: 100,
                    user: window.user.id
                },
                data: null
            })
            .then(response => {
                this.companies = response.data.data;
            });

        this.fetchCampaigns();
        this.fetchCompanies();
    },
    methods: {
        onCampaignCompanySelected() {
            this.searchCampaignForm.page = 1;
            return this.fetchCampaigns();
        },
        fetchCampaigns() {
            if (this.campaignCompanySelected) {
                this.searchCampaignForm.company = this.campaignCompanySelected.id;
            } else {
                this.searchCampaignForm.company = null;
            }
            this.searchCampaignForm.user = window.user.id;
            this.loadingCampaigns = true;
            this.searchCampaignForm
                .get(window.searchCampaignFormUrl)
                .then(response => {
                    this.campaigns = response.data;
                    this.searchCampaignForm.page = response.current_page;
                    this.searchCampaignForm.per_page = response.per_page;
                    this.total= response.total;
                    this.loadingCampaigns = false;
                })
                .catch(error => {
                    this.$toastr.error("Unable to get campaigns");
                });
        },
        fetchCompanies() {
            this.searchCompanyForm.user = window.user.id;
            this.loadingCompanies = true;
            this.searchCompanyForm
                .get(window.searchCompaniesFormUrl)
                .then(response => {
                    console.log('response', response);
                    console.log('this', this);
                    this.companiesForList = response.data;
                    this.searchCompanyForm.page = response.current_page;
                    this.searchCompanyForm.per_page = response.per_page;
                    this.totalCompanies = response.total;
                    this.loadingCompanies = false;
                })
                .catch(error => {
                    this.$toastr.error("Unable to get campaigns");
                });
        },
        onCampaignPageChanged(event) {
            this.searchCampaignForm.page = event.page;
            return this.fetchCampaigns();
        }
    }
});

window['sidebar'] = new Vue({
    el: '#sidebar',
    data: {
        enableInputs: false
    }
})
