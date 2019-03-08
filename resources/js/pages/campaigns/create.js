import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import VueFormWizard from 'vue-form-wizard';
import Modal from 'bootstrap-vue';
import {filter} from 'lodash';
Vue.use(Modal);
Vue.use(VueFormWizard);

window['app'] = new Vue({
    el: '#campaign-create',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
        'input-errors': require('./../../components/input-errors/input-errors').default,
        'date-pick': require('./../../components/date-pick/date-pick').default
    },
    data: {
        addCrmExportEmail: '',
        adfCrmExportEmail: '',
        agencySelected: null,
        availablePhoneNumbers: [],
        availableCallSources: [],
        clientPassThroughEmail: '',
        dealershipSelected: null,
        agencies: [],
        callSources: [
            {name: 'email', label: 'Email'},
            {name: 'mailer', label: 'Mailer'},
            {name: 'sms', label: 'SMS'},
            {name: 'text_in', label: 'Text-In'},
        ],
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
            phone_number_ids: [],
            service_dept: false,
            service_dept_email: [],
            sms_on_callback: false,
            sms_on_callback_number: [],
            start: null,
            status: 'Active',
        }),
        campaignPhones: [],
        leadAlertEmail: '',
        loadingPhoneModal: false,
        loadingPurchaseNumber: false,
        phoneNumbers: [],
        purchasePhoneNumberForm: new Form({
            phone_number: null,
            forward: '',
            call_source_name: ''
        }),
        serviceDeptEmail: '',
        searchPhoneNumberForm: new Form({
            country: 'US',
            inPostalCode: '',
            contains: '',
            areaCode: '',
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
        this.updateCallSources();
    },
    methods: {
        updateCallSources: function () {
            this.availableCallSources = [];

            let campaign_sources = _.map(this.phoneNumbers, 'call_source_name');
            if (this.phoneNumbers.length == 0 || campaign_sources.length == 0) {
                this.availableCallSources = this.callSources;
                return;
            }

            _.map(this.callSources, (source, index) => {
                if (campaign_sources.indexOf(source.name) < 0) {
                    this.availableCallSources.push(source);
                }
            });
        },
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
            ['phone_number', 'call_source_name'].forEach(field => {
                if (!this.purchasePhoneNumberForm[field]) {
                    this.purchasePhoneNumberForm.errors.add(field, 'This field is required.');
                    invalid = true;
                }
            });
            if (invalid) return;
            this.loadingPurchaseNumber = true;
            this.purchasePhoneNumberForm
                .post(window.provisionPhoneUrl)
                .then((response) => {
                    this.loadingPurchaseNumber = false;
                    this.phoneNumbers.push(response);
                    this.campaignForm.phone_number_ids.push(response.id);
                    this.updateCallSources();
                    this.purchasePhoneNumberForm.reset();
                    this.showAvailablePhoneNumbers = false;
                    this.closeModal('addPhoneModalRef');
                })
                .catch((error) => {
                    this.loadingPurchaseNumber = false;
                    this.$toastr.error('Unable to process your request.');
                });
        },
        removeAdditionalFeature: function (index, list) {
          if (list[index]) {
              list.splice(index, 1);
          }
        },
        getCallSourceName: function (value) {
            let displayValue = _.filter(this.callSources, {name: value});
            if (displayValue.length == 1) {
                return displayValue[0].label;
            }
            return '';
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
            this.loadingPhoneModal = true;
            this.showAvailablePhoneNumbers = false;
            this.searchPhoneNumberForm
                .post(window.searchPhoneUrl)
                .then(response => {
                    let modifiedNumbers = this.convertPhoneNumbersToOptions(response.numbers);
                    this.availablePhoneNumbers = modifiedNumbers;
                    this.showAvailablePhoneNumbers = true;
                    this.loadingPhoneModal = false;
                }, () => {
                    this.$toastr.error('Unable to get phone numbers.');
                    this.showAvailablePhoneNumbers = true;
                    this.loadingPhoneModal = false;
                });
        },
        convertPhoneNumbersToOptions: function (numbers) {
            numbers.forEach((number) => {
                number.label = number.phone + ' - ' + number.location;
                number.value = number.phoneNumber;
                delete number.location;
                delete number.phone;
                delete number.phoneNumber;
                delete number.zip;
            });
            return numbers;
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
