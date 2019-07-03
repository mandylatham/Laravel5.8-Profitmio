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
import Modal from 'bootstrap-vue';
Vue.use(Modal);
import axios from 'axios';

window['app'] = new Vue({
    el: '#company-details',
    components: {
        'campaign': require('./../../components/campaign/campaign').default,
        'user-status': require('./../../components/user-status/user-status').default,
        'user-role': require('./../../components/user-role/user-role').default,
        'resumable': require('./../../components/resumable/resumable').default,
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination').default,
    },
    computed: {
        campaignAccessUsersPagination: function () {
            return {
                page: this.searchCampaignAccessUser.page,
                per_page: this.searchCampaignAccessUser.per_page,
                total: this.totalCampaignAccessUser
            };
        },
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
        campaignSelected: null,
        campaigns: [],
        company: {},
        companyIndex: '',
        editImage: false,
        loadingUsers: false,
        loadingCampaignAccessUsers: false,
        modifiedCompany: {},
        loadingInvitation: false,
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
        searchCampaignAccessUser: new Form({
            q: localStorage.getItem('companyUserQ') || '',
            page: 1,
            per_page: 15,
            company: window.company.id,
            campaign: null
        }),
        updateForm: new Form({
            name: '',
            country: null,
            address: '',
            address2: '',
            city: '',
            state: '',
            zip: '',
            phone: '',
            url: '',
            facebook: '',
            twitter: ''
        }),
        updateUrl: '',
        users: [],
        usersForCampaignAccess: [],
        companyFormFields: ['name', 'country', 'address', 'address2', 'city', 'state', 'zip', 'phone', 'url', 'facebook', 'twitter'],
        showCompanyFormControls: false,
        loadingCampaigns: false,
        targetUrl: window.updateCompanyImageUrl,
        total: 0,
        totalUsers: 0,
        totalCampaignAccessUser: 0,
        userEditUrl: '',
        userImpersonateUrl: '',
    },
    mounted() {
        this.companyIndex = window.indexUrl;
        this.updateUrl = window.updateUrl;
        this.company = window.company;
        this.userEditUrl = window.userEditUrl;
        this.userImpersonateUrl = window.userImpersonateUrl;

        this.modifiedCompany = JSON.parse(JSON.stringify(this.company));
        this.updateFields();
        this.fetchCampaigns();
        this.fetchUsers();
    },
    methods: {
        openCampaignAccessModal: function (campaign) {
            this.usersForCampaignAccess = [];
            this.campaignSelected = campaign;
            this.fetchUsersForCampaignAccess(campaign);
            this.$refs.configureAccessModal.show();
        },
        closeModal: function () {
            this.$refs.configureAccessModal.hide();
        },
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
                    window.PmEvent.fire('errors.api', "Unable to get campaigns");
                });
        },
        fetchUsers() {
            localStorage.setItem('companyUserQ', this.searchUserForm.q);
            this.loadingUsers = true;
            this.searchUserForm
                .get(window.searchUserFormUrl)
                .then(response => {
                    this.users = response.data;
                    this.searchUserForm.page = response.meta.current_page;
                    this.searchUserForm.per_page = response.meta.per_page;
                    this.totalUsers = response.meta.total;
                    this.loadingUsers = false;
                })
                .catch(error => {
                    window.PmEvent.fire('errors.api', "Unable to get users");
                });
        },
        fetchUsersForCampaignAccess(campaign) {
            localStorage.setItem('companyCampaignAccessUsersQ', this.searchCampaignAccessUser.q);
            this.loadingCampaignAccessUsers = true;
            this.searchCampaignAccessUser.campaign = campaign.id;
            this.searchCampaignAccessUser
                .get(generateRoute(window.searchCampaignAccessUserUrl, {'campaign': campaign.id}))
                .then(response => {
                    this.usersForCampaignAccess = response.data;
                    this.searchCampaignAccessUser.page = response.meta.current_page;
                    this.searchCampaignAccessUser.per_page = response.meta.per_page;
                    this.totalCampaignAccessUser = response.meta.total;
                    this.loadingCampaignAccessUsers = false;
                })
                .catch(error => {
                    window.PmEvent.fire('errors.api', "Unable to get users");
                });
        },
        resendInvitation(user) {
            this.loadingInvitation = true;
            axios
                .get(window.resendInvitationUrl, {
                    params: {
                        user: user.id,
                        company: this.company.id
                    },
                })
                .then(response => {
                    this.loadingInvitation = false;
                    this.$toastr.success('Invitation Sent!');
                }, () => {
                    this.loadingInvitation = false;
                    window.PmEvent.fire('errors.api', 'Unable to process your request.');
                })
        },
        updateFields: function () {
            // update the form
            this.companyFormFields.forEach((field) => {
                if (this.modifiedCompany[field]) {
                    this.updateForm[field] = JSON.parse(JSON.stringify(this.modifiedCompany[field]));
                }
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
        toggleCampaignAccess: function (user) {
            axios.post(generateRoute(window.toggleCampaignAccessUserUrl, {
                userId: user.id,
                campaignId: this.campaignSelected.id
            })).then(() => {
                this.$toastr.success("Access updated.");
            }, () => {
                window.PmEvent.fire('errors.api', "Unable to process your request");
                user.has_access = !user.has_access;
            })
        },
        onCampaignPageChanged(event) {
            this.searchCampaignForm.page = event.page;
            return this.fetchCampaigns();
        },
        onCampaignAccessUserPageChanged(event) {
            this.searchCampaignAccessUser.page = event.page;
            return this.fetchUsersForCampaignAccess(this.campaignSelected);
        },
        onFileAdded() {
            this.$refs.resumable.startUpload();
        },
        onFileSuccess(event) {
            const response = JSON.parse(event.message);
            this.company.image = response.location;
            this.editImage = false;
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
            console.log('this.updateForm', this.updateForm);
            this.updateForm.put(updateUrl)
                .then(response => {
                    this.showCompanyFormControls = false;
                    this.company = JSON.parse(JSON.stringify(this.modifiedCompany));
                    this.$toastr.success("Update successful");
                })
                .catch(error => {
                    window.PmEvent.fire('errors.api', "Unable to update");
                });
        },
        generateRoute
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
