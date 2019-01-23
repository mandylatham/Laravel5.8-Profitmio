import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import VueFormWizard from 'vue-form-wizard';
import moment from 'moment';
import Modal from 'bootstrap-vue';
Vue.use(Modal);
import InputTag from 'vue-input-tag'
Vue.use(VueFormWizard);

window['app'] = new Vue({
    el: '#campaigns-edit',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon'),
        'input-errors': require('./../../components/input-errors/input-errors'),
        'date-pick': require('./../../components/date-pick/date-pick'),
        InputTag
    },
    data: {
        addCrmExportEmail: '',
        adfCrmExportEmail: '',
        agencySelected: window.agencySelected,
        availablePhoneNumbers: [],
        clientPassThroughEmail: '',
        dealershipSelected: window.dealershipSelected,
        agencies: [],
        datePickInputClasses: {
            class: 'form-control'
        },
        dealerships: [],
        loading: false,
        campaignForm: new Form({
            agency: null,
            adf_crm_export: window.campaign.adf_crm_export,
            adf_crm_export_email: (window.campaign.adf_crm_export_email || '').split(','),
            client_passthrough: window.campaign.client_passthrough,
            client_passthrough_email: (window.campaign.client_passthrough_email || '').split(','),
            dealership: null,
            end: moment.utc(window.campaign.ends_at, 'YYYY-MM-DD HH:mm:ss').local().format('YYYY-MM-DD'),
            expires: moment.utc(window.campaign.expires_at, 'YYYY-MM-DD HH:mm:ss').local().format('YYYY-MM-DD'),
            lead_alerts: window.campaign.lead_alerts,
            lead_alert_emails: (window.campaign.lead_alert_email || '').split(','),
            name: window.campaign.name,
            order: window.campaign.order_id,
            service_dept: window.campaign.service_dept,
            service_dept_email: (window.campaign.service_dept_email || '').split(','),
            sms_on_callback: window.campaign.sms_on_callback,
            sms_on_callback_number: (window.campaign.sms_on_callback_number || '').split(','),
            start: moment.utc(window.campaign.starts_at, 'YYYY-MM-DD HH:mm:ss').local().format('YYYY-MM-DD'),
            status: window.campaign.status
        }),
        leadAlertEmail: '',
        loadingPhoneModal: false,
        loadingPurchaseNumber: false,
        phoneNumbers: [],
        purchasePhoneNumberForm: new Form({
            phone_number: null,
            forward: '',
            call_source: ''
        }),
        serviceDeptEmail: '',
        searchPhoneNumberForm: new Form({
            country: 'US',
            postal_code: '',
            contains: '',
            area_code: '',
        }),
        showAvailablePhoneNumbers: false,
        smsOnCallbackNumber: '',
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
        addFieldToAdditionalFeature: function (field, list) {
            if (!this[field]) return;
            list.push(this[field]);
            this[field] = null;
        },
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
        removeAdditionalFeature: function (index, list) {
          if (list[index]) {
              list.splice(index, 1);
          }
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
                        title: 'Campaign Updated!',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.replace(window.campaignStatsUrl);
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
            ['name', 'order', 'status', 'start', 'end'].forEach(field => {
                if (!this.campaignForm[field]) {
                    this.campaignForm.errors.add(field, 'This field is required.');
                    valid = false;
                }
            });
            return valid;
        },
    }
});
