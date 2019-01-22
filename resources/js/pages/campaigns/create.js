import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import VueFormWizard from 'vue-form-wizard';
import Modal from 'bootstrap-vue';
Vue.use(Modal);
import InputTag from 'vue-input-tag'
Vue.use(VueFormWizard);

window['app'] = new Vue({
    el: '#campaign-create',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon'),
        'input-errors': require('./../../components/input-errors/input-errors'),
        'date-pick': require('./../../components/date-pick/date-pick'),
        InputTag
    },
    data: {
        adfCrmExportEmail: '',
        agencySelected: null,
        availablePhoneNumbers: [],
        dealershipSelected: null,
        agencies: [],
        datePickInputClasses: {
            class: 'form-control'
        },
        dealerships: [],
        loading: false,
        campaignForm: new Form({
            agency: null,
            adf_crm_export: false,
            adf_crm_export_email: [],
            client_passthrough: false,
            client_passthrough_email: [],
            dealership: null,
            end: null,
            expires: null,
            lead_alerts: false,
            lead_alert_emails: [],
            name: '',
            order: null,
            service_dept: false,
            service_dept_email: [],
            sms_on_callback: false,
            sms_on_callback_number: [],
            start: null,
            status: 'Active',
        }),
        loadingPhoneModal: false,
        loadingPurchaseNumber: false,
        phoneNumbers: [],
        purchasePhoneNumberForm: new Form({
            phone_number: null,
            forward: '',
            call_source: ''
        }),
        searchPhoneNumberForm: new Form({
            country: 'US',
            postal_code: '',
            contains: '',
            area_code: '',
        }),
        showAvailablePhoneNumbers: false,
        validation: [{
            classes: 'asdf',
            rule: /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/
        }]
    },
    mounted() {
        this.agencies = window.agencies;
        this.dealerships = window.dealerships;
    },
    methods: {
        clearError: function (form, field) {
            form.errors.clear(field);
        },
        closeModal: function (modalRef) {
            this.$refs[modalRef].hide();
        },
        purchasePhoneNumber: function () {
            let invalid = false;
            this.purchasePhoneNumberForm.errors.clear();
            ['phone_number', 'forward', 'call_source'].forEach(field => {
                if (!this.purchasePhoneNumberForm[field]) {
                    this.purchasePhoneNumberForm.errors.add(field, 'This field is required.');
                    invalid = true;
                }
            });
            if (invalid) return;
            this.loadingPurchaseNumber = true;
            this.purchasePhoneNumberForm
                .post(window.provisionPhoneUrl)
                .then(() => {
                    this.loadingPurchaseNumber = false;
                    this.phoneNumbers.push(this.purchasePhoneNumberForm.data());
                    this.closeModal('addPhoneModalRef');
                }, () => {
                    this.loadingPurchaseNumber = false;
                    this.$toastr.error('Unable to process your request.');
                })
        },
        saveCampaign: function () {
            this.loading = true;
            this.campaignForm.agency = this.agencySelected.id;
            this.campaignForm.dealership = this.dealershipSelected.id;
            this.campaignForm
                .post(window.saveCampaignUrl)
                .then(() => {
                    this.loading = false;
                    this.$swal({
                        title: 'Campaign Created!',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.replace(window.campaignIndexUrl);
                    });
                })
                .catch(e => {
                    this.$toastr.error("Unable to process your request");
                    this.loading = false;
                });
        },
        searchPhones() {
            let invalid = false;
            this.searchPhoneNumberForm.errors.clear();
            ['area_code', 'postal_code', 'contains'].forEach(field => {
                if (!this.searchPhoneNumberForm[field]) {
                    this.searchPhoneNumberForm.errors.add(field, 'This field is required.');
                    invalid = true;
                }
            });
            if (invalid) return;
            this.loadingPhoneModal = true;
            this.showAvailablePhoneNumbers = false;
            this.searchPhoneNumberForm
                .post(window.searchPhoneUrl)
                .then(response => {
                    this.availablePhoneNumbers = response.numbers;
                    this.loadingPhoneModal = false;
                }, () => {
                    this.$toastr.error('Unable to get phone numbers.');
                    this.showAvailablePhoneNumbers = true;
                    this.loadingPhoneModal = false;
                });
        },
        validateAccountsTab: function () {
            let valid = true;
            this.campaignForm.errors.clear();
            if (!this.agencySelected) {
                valid = false;
                this.campaignForm.errors.add('agency', 'This field is required.');
            }
            if (!this.dealershipSelected) {
                valid = false;
                this.campaignForm.errors.add('dealership', 'This field is required.');
            }
            return valid;
        },
        validateBasicTab: function () {
            let valid = true;
            this.campaignForm.errors.clear();
            ['name', 'order', 'status', 'start', 'end', 'expires'].forEach(field => {
                if (!this.campaignForm[field]) {
                    this.campaignForm.errors.add(field, 'This field is required.');
                    valid = false;
                }
            });
            return valid;
        },
    }
});
