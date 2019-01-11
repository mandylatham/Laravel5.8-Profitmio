import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import axios from 'axios';
// Chart Library
import VueChartkick from 'vue-chartkick'
import Chart from 'chart.js'
import {filter} from 'lodash';
import './../../filters/user-role.filter';
import {generateRoute} from './../../common/helpers';

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
        companiesPagination: function () {
            return {
                page: this.searchCompanyForm.page,
                per_page: this.searchCompanyForm.per_page,
                total: this.totalCompanies
            };
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
        totalForCompanies: null,
        campaigns: [],
        companies: [],
        companiesForList: [],
        roles: ['site_admin', 'user'],
        searchTerm: '',
        campaignCompanySelected: null,
        tableOptions: {
            mobile: 'lg'
        },
        timezones: [],
        formUrl: ''
    },
    mounted() {
        this.timezones = window.timezones;
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
        companyDataUpdated(company) {
            axios
                .post(generateRoute(window.updateCompanyDataUrl, {'userId': window.user.id}), {
                    company: company.id,
                    role: company.role,
                    timezone: company.timezone
                })
                .then(response => {
                }, () => {
                    this.$toastr.error('Unable to process your request');
                });
        },
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
                    this.companiesForList = response.data;
                    this.searchCompanyForm.page = response.meta.current_page;
                    this.searchCompanyForm.per_page = response.meta.per_page;
                    this.totalCompanies = response.meta.total;
                    this.loadingCompanies = false;
                })
                .catch(error => {
                    this.$toastr.error("Unable to get campaigns");
                });
        },
        onCampaignPageChanged(event) {
            this.searchCampaignForm.page = event.page;
            return this.fetchCampaigns();
        },
        onCompanyPageChanged(event) {
            this.searchCompanyForm.page = event.page;
            return this.fetchCompanies();
        },
    }
});

window['sidebar'] = new Vue({
    el: '#sidebar',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon'),
    },
    data: {
        loading: false,
        enableInputs: false,
        editUserForm: new Form(window.user),
        user: {}
    },
    methods: {
        saveUser: function () {
            this.loading = true;
            this.editUserForm
                .post(generateRoute(window.updateUserUrl, {userId: window.user.id}))
                .then(() => {
                    this.enableInputs = false;
                    this.$toastr.success('User updated!');
                    this.loading = false;
                    this.user = this.editUserForm.data();
                })
                .catch(e => {
                    this.$toastr.error("Unable to process your request");
                    this.loading = false;
                });
        },
        deleteUser: function () {
            this.$swal({
                title: "Are you sure?",
                text: "You will not be able to undo this operation!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                allowOutsideClick: false,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return axios.delete(window.deleteUserUrl);
                }
            }).then(result => {
                if (result.value) {
                    this.$swal({
                        title: 'User Deleted',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.replace(window.userIndexUrl);
                    });
                }
            }, error => {
                this.$toastr.error('Unable to process your request');
            });
        }
    },
    mounted: function () {
        this.user = window.user;
    }
});
