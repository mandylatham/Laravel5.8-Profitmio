import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import {generateRoute} from './../../common/helpers'
// Wizard
import VueFormWizard from 'vue-form-wizard';
Vue.use(VueFormWizard);
// Chart Library
import VueChartkick from 'vue-chartkick'
import Chart from 'chart.js'
Vue.use(VueChartkick, {adapter: Chart})
import {filter} from 'lodash';
// filters
import './../../filters/user-role.filter';
import vue2Dropzone from 'vue2-dropzone';
// Validation
import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);
// Custom Validation
import { helpers, required, minLength, url } from 'vuelidate/lib/validators';
import { isNorthAmericanPhoneNumber, isCanadianPostalCode, isUnitedStatesPostalCode, looseAddressMatch } from './../../common/validators';
import './../../components/campaign/campaign';

window['app'] = new Vue({
    el: '#app',
    components: {
        'campaign': require('./../../components/campaign/campaign'),
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon'),
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination'),
    },
    computed: {
        usersPagination: function () {
            return {
                page: this.searchUserForm.page,
                per_page: this.searchUserForm.per_page,
                total: this.totalUsers
            };
        },
        campaignsPagination: function () {
            return {
                page: this.searchCampaignForm.page,
                per_page: this.searchCampaignForm.per_page,
                total: this.total
            };
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
        }
    },
    data: {
        campaigns: [],
        company: {},
        companyIndex: '',
        loadingUsers: false,
        modifiedCompany: {},
        searchCampaignForm: new Form({
            q: localStorage.getItem('companyCampaignQ') || '',
            page: 1,
            per_page: 15,
            company: window.company.id
        }),
        searchUserForm: new Form({
            q: localStorage.getItem('companyUserQ') || '',
            page: 1,
            per_page: 15,
            company: window.company.id
        }),
        updateForm: new Form({
            name: '',
            address: '',
            address2: '',
            city: '',
            state: '',
            zip: '',
        }),
        updateUrl: '',
        users: [],
        companyFormFields: ['name', 'address', 'address2', 'city', 'state', 'zip'],
        showCompanyFormControls: false,
        loadingCampaigns: false,
        total: 0,
        totalUsers: 0
    },
    mounted() {
        this.companyIndex = window.indexUrl;
        this.updateUrl = window.updateUrl;
        this.company = window.company;
        this.modifiedCompany = JSON.parse(JSON.stringify(this.company));
        this.updateFields();
        this.fetchCampaigns();
        this.fetchUsers();
    },
    methods: {
        fetchCampaigns() {
            localStorage.setItem('companyCampaignQ', this.searchCampaignForm.q);
            this.loadingCampaigns = true;
            this.searchCampaignForm
                .get(window.searchCampaignFormUrl)
                .then(response => {
                    this.campaigns = response.data;
                    this.searchCampaignForm.page = response.current_page;
                    this.searchCampaignForm.per_page = response.per_page;
                    this.total = response.total;
                    this.loadingCampaigns = false;
                })
                .catch(error => {
                    this.$toastr.error("Unable to get campaigns");
                });
        },
        fetchUsers() {
            localStorage.setItem('companyUserQ', this.searchUserForm.q);
            this.loadingUsers = true;
            this.searchUserForm
                .get(window.searchUserFormUrl)
                .then(response => {
                    this.users = response.data;
                    this.searchUserForm.page = response.current_page;
                    this.searchUserForm.per_page = response.per_page;
                    this.totalUsers = response.total;
                    this.loadingUsers = false;
                })
                .catch(error => {
                    this.$toastr.error("Unable to get users");
                });
        },
        updateFields: function () {
            // update the form
            this.companyFormFields.forEach((field) => {
                this.updateForm[field] = JSON.parse(JSON.stringify(this.modifiedCompany[field]));
            });
        },
        resetFields: function () {
            this.companyFormFields.forEach((field) => {
                this.updateForm[field] = JSON.parse(JSON.stringify(this.company[field]));
                this.modifiedCompany[field] = JSON.parse(JSON.stringify(this.company[field]));
            });
        },
        toRoyalCase: function (data) {
            var royal = '';
            var parts = data.split('_');
            for (var key in parts) {
                royal += parts[key].charAt(0).toUpperCase() + parts[key].slice(1);
            }
            return royal;
        },
        onCampaignPageChanged(event) {
            this.searchCampaignForm.page = event.page;
            return this.fetchCampaigns();
        },
        onUserPageChanged(event) {
            this.searchUserForm.page = event.page;
            return this.fetchUsers();
        },
        saveCompanyForm: function () {
            this.updateFields();
            this.update();
        },
        cancelCompanyForm: function () {
            this.resetFields();
            this.showCompanyFormControls = false;
        },
        update: function () {
            this.updateForm.put(updateUrl)
                .then(response => {
                    this.showCompanyFormControls = false;
                    this.company = JSON.parse(JSON.stringify(this.modifiedCompany));
                    this.$toastr.success("Update successful");
                })
                .catch(error => {
                    this.$toastr.error("Unable to update");
                });
        }
    },
    validations() {
        return {
            template: {
                name: { required, minLength: minLength(2) },
                address: { required, looseAddressMatch },
                address2: {},
                city: { required },
                state: { required, },
                zip: this.createForm.country == 'us' ? { required, isUnitedStatesPostalCode } : { required, isCanadianPostalCode },
            }
        }
    }
});
