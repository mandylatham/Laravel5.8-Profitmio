import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import VueFormWizard from 'vue-form-wizard';
import {filter} from 'lodash';
import moment from 'moment';
import Modal from 'bootstrap-vue';
import { PackageIcon } from 'vue-feather-icons';
import {generateRoute} from '../../common/helpers';
Vue.use(Modal);
Vue.use(VueFormWizard);

window['app'] = new Vue({
    el: '#campaigns-edit',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
        'input-errors': require('./../../components/input-errors/input-errors').default,
        'date-pick': require('./../../components/date-pick/date-pick').default
    },
    data: {
        addCrmExportEmail: '',
        adfCrmExportEmail: '',
        agencies: [],
        agencySelected: window.agencySelected,
        availableCallSources: [],
        availableEditCallSources: [],
        availablePhoneNumbers: [],
        clientPassThroughEmail: '',
        datePickInputClasses: {
            class: 'form-control'
        },
        dealerships: [],
        dealershipSelected: window.dealershipSelected,
        callSources: [
            {name: 'email', label: 'Email'},
            {name: 'mailer', label: 'Mailer'},
            {name: 'sms', label: 'SMS'},
            {name: 'text_in', label: 'Text-In'},
        ],
        campaign: window.campaign,
        campaignForm: new Form({
            agency: null,
            adf_crm_export: window.campaign.adf_crm_export,
            adf_crm_export_email: window.campaign.adf_crm_export_email || [],
            client_passthrough: window.campaign.client_passthrough,
            client_passthrough_email: window.campaign.client_passthrough_email || [],
            dealership: null,
            end: moment.utc(window.campaign.ends_at, 'YYYY-MM-DD HH:mm:ss').local().format('YYYY-MM-DD'),
            expires: window.campaign.expires_at? moment.utc(window.campaign.expires_at, 'YYYY-MM-DD HH:mm:ss').local().format('YYYY-MM-DD') : undefined,
            lead_alerts: window.campaign.lead_alerts,
            lead_alert_emails: window.campaign.lead_alert_email || [],
            name: window.campaign.name,
            order: window.campaign.order_id,
            service_dept: window.campaign.service_dept,
            service_dept_email: window.campaign.service_dept_email || [],
            sms_on_callback: window.campaign.sms_on_callback,
            sms_on_callback_number: window.campaign.sms_on_callback_number || [],
            start: moment.utc(window.campaign.starts_at, 'YYYY-MM-DD HH:mm:ss').local().format('YYYY-MM-DD'),
            status: window.campaign.status
        }),
        campaignPhones: [],
        editPhoneNumberForm: {},
        getCampaignPhonesForm: new Form(),
        getCampaignPhonesUrl: window.getCampaignPhonesUrl,
        leadAlertEmail: '',
        loading: false,
        loadingPhoneModal: false,
        loadingPurchaseNumber: false,
        phoneNumbers: [],
        provisionPhoneUrl: window.provisionPhoneUrl,
        purchasePhoneNumberForm: new Form({
            call_source_name: '',
            campaign_id: window.campaign.id,
            forward: '',
            phone_number: null,
        }),
        searchPhoneNumberForm: new Form({
            country: 'US',
            inPostalCode: '',
            contains: '',
            areaCode: '',
        }),
        serviceDeptEmail: '',
        showAvailablePhoneNumbers: false,
        showPhoneNumberForm: {},
        smsOnCallbackNumber: '',
        validation: [{
            classes: 'asdf',
            rule: /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/
        }]
    },
    mounted() {
        this.agencies = window.agencies;
        this.dealerships = window.dealerships;
        this.getCampaignPhones();
    },
    methods: {
        availableCallSourcesWithCurrent: function (call_source_name) {
            if (call_source_name === undefined) return;
            let phoneSource = _.filter(this.callSources, {name: call_source_name});
            if (phoneSource.length > 0) {
                phoneSource = phoneSource[0];
            }
            let newSources = [];
            for (var i=0; i < this.availableCallSources.length; i++) {
                newSources.push(this.availableCallSources[i]);
            }
            newSources.push(phoneSource);
            console.log(newSources);
            return newSources;
        },
        enablePhoneNumberForm: function (phone) {
            this.$set(this.showPhoneNumberForm, phone.id, true);
        },
        editPhoneNumber: function (phone) {
            this.editPhoneNumberForm[phone.id].reset();
            console.log(this.showPhoneNumberForm[phone.id]);
            Vue.set(this.showPhoneNumberForm, phone.id, true);
            console.log(this.showPhoneNumberForm[phone.id]);
        },
        savePhoneNumber: function (phone) {
            this.editPhoneNumberForm[phone.id].patch(generateRoute(window.savePhoneNumberUrl, {'phone_number_id': phone.id}))
                .then((response) => {
                    Vue.set(this.showPhoneNumberForm, phone.id, false);
                    this.getCampaignPhones();
                    this.$toastr.success("Phone Updated");
                })
                .catch((error) => {
                    this.$toastr.error("Unable to update phone number");
                });
        },
        cancelPhoneNumber: function (phone) {
            Vue.set(this.editPhoneNumberForm, phone.id, new Form({
                forward: phone.forward,
                call_source_name: phone.call_source_name,
            }));
            Vue.set(this.showPhoneNumberForm, phone.id, false);
        },
        setupPhoneNumberForms: function () {
            if (this.campaignPhones.length > 0) {
                for (var i=0; i < this.campaignPhones.length; i++) {
                    let phone = this.campaignPhones[i];
                    Vue.set(this.showPhoneNumberForm, phone.id, false);
                    Vue.set(this.editPhoneNumberForm, phone.id, new Form({
                        forward: phone.forward,
                        call_source_name: phone.call_source_name
                    }));
                }
            }
        },
        getCallSourceName: function (value) {
            let displayValue = _.filter(this.callSources, {name: value});
            if (displayValue.length == 1) {
                return displayValue[0].label;
            }
            return '';
        },
        updateCallSources: function () {
            this.availableCallSources = [];
            let campaign_sources = _.map(this.campaignPhones, 'call_source_name');
            if (campaign_sources.length == 0) {
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
        getCampaignPhones: function () {
            this.getCampaignPhonesForm.get(window.getCampaignPhonesUrl)
                .then((response) => {
                    this.campaignPhones = response;
                    this.updateCallSources();
                    this.setupPhoneNumberForms();
                })
                .catch((error) => {
                    this.$toastr.error("Unable to fetch campaign phones: " + error);
                });
        },
        purchasePhoneNumber: function () {
            let invalid = false;
            this.purchasePhoneNumberForm.errors.clear();
            this.loadingPurchaseNumber = true;
            this.purchasePhoneNumberForm
                .post(window.provisionPhoneUrl)
                .then((request) => {
                    this.loadingPurchaseNumber = false;
                    delete this.availableCallSources[this.purchasePhoneNumberForm.call_source_name];
                    this.purchasePhoneNumberForm.reset();
                    this.showAvailablePhoneNumbers = false;
                    this.getCampaignPhones();
                    this.closeModal('addPhoneModalRef');
                }, (error) => {
                    this.loadingPurchaseNumber = false;
                    this.$toastr.error('Unable to process your request: ' + error);
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
                        // window.location.replace(window.campaignStatsUrl);
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
            if (!this.searchPhoneNumberForm.areaCode && !this.searchPhoneNumberForm.inPostalCode && !this.searchPhoneNumberForm.contains) {
                this.searchPhoneNumberForm.errors.add('area_code', 'This field is required.');
                this.searchPhoneNumberForm.errors.add('postal_code', 'This field is required.');
                this.searchPhoneNumberForm.errors.add('contains', 'This field is required.');
                invalid = true;
            }
            if (invalid) return;
            this.loadingPhoneModal = true;
            this.showAvailablePhoneNumbers = false;
            this.searchPhoneNumberForm
                .post(window.searchPhoneUrl)
                .then(response => {
                    let modifiedNumbers = this.convertPhoneNumbersToOptions(response.numbers);
                    this.availablePhoneNumbers = modifiedNumbers;
                    this.showAvailablePhoneNumbers = true;
                    this.loadingPhoneModal = false;
                }, (error) => {
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
