import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import Str from './../../common/str';
import VueFormWizard from 'vue-form-wizard';
import moment from 'moment';
import Alert from 'bootstrap-vue';
import Modal from 'bootstrap-vue';
import Progress from 'bootstrap-vue';
import {generateRoute} from '../../common/helpers';
import filter from 'lodash/filter';
import map from 'lodash/map';
// custom validation
import Vuelidate from 'vuelidate';
import { required, between } from 'vuelidate/lib/validators';
import { isNorthAmericanPhoneNumber } from './../../common/validators';
Vue.use(Alert);
Vue.use(Modal);
Vue.use(Progress);
Vue.use(VueFormWizard);
Vue.use(Vuelidate);

window['app'] = new Vue({
    el: '#campaigns-edit',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
        'input-errors': require('./../../components/input-errors/input-errors').default,
        'date-pick': require('./../../components/date-pick/date-pick').default
    },
    computed: {
    },
    data: {
        addCampaignTagForm: new Form({
            name: null,
            text: null,
            indication: null,
        }),
        removeTagForm: new Form(),
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
            enable_text_to_value: window.campaign.enable_text_to_value || false,
            adf_crm_export: window.campaign.adf_crm_export,
            adf_crm_export_email: window.campaign.adf_crm_export_email || [],
            client_passthrough: window.campaign.client_passthrough,
            client_passthrough_email: window.campaign.client_passthrough_email || [],
            dealership: null,
            tags: window.campaign.tags || [],
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
        getTagsForm: new Form(),
        leadAlertEmail: '',
        tags: window.campaign.tags,
        leadTag: '',
        leadTagHumanText: '',
        leadTagIndication: '',
        loading: false,
        loadingPhoneModal: false,
        loadingPurchaseNumber: false,
        phoneNumbers: [],
        phoneVerificationCode: '',
        phoneVerificationForm: new Form({
            phone: '',
            code: '',
        }),
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
        }],
        verificationStarted: false,
        verificationStartedMessage: '',
        verificationStartedVariant: '',

        dismissSeconds: 10,
        dismissCountDown: 0,
        showDismissibleAlert: false
    },
    mounted() {
        this.agencies = window.agencies;
        this.dealerships = window.dealerships;
        this.getCampaignPhones();
    },
    methods: {
        campaignHasMailerPhone() {
            let hasMailerPhone = false;
            this.phoneNumbers.forEach(phone => {
                if (phone.call_source_name === 'mailer') {
                    hasMailerPhone = true;
                }
            });
            return hasMailerPhone;
        },
        countDownChanged: function(dismissCountDown) {
            this.dismissCountDown = dismissCountDown;
        },
        tagIndicationClass: function (tag) {
            if (tag.indication == 'positive') return 'fa-thumbs-up';
            if (tag.indication == 'negative') return 'fa-thumbs-down';
            if (tag.indication == 'neutral') return 'fa-minus';
            return 'fa-warning-triangle';
        },
        availableCallSourcesWithCurrent: function (call_source_name) {
            if (call_source_name === undefined) return;
            let phoneSource = filter(this.callSources, {name: call_source_name});
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
        getTags: function () {
            this.getTagsForm.get(window.getTagsUrl)
                .then((response) => {
                    this.tags = response.data;
                })
                .catch((error) => {
                    console.error(error);
                    this.$toastr.error("Unable to fetch campaign lead tags");
                });
        },
        savePhoneNumber: function (phone) {
            this.editPhoneNumberForm[phone.id].patch(generateRoute(window.savePhoneNumberUrl, {'phone_number_id': phone.id}))
                .then((response) => {
                    Vue.set(this.showPhoneNumberForm, phone.id, false);
                    this.getCampaignPhones();
                    this.$toastr.success("Phone Updated");
                })
                .catch((error) => {
                    window.PmEvent.fire('errors.api', "Unable to update phone number");
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
            let displayValue = filter(this.callSources, {name: value});
            if (displayValue.length == 1) {
                return displayValue[0].label;
            }
            return '';
        },
        updateCallSources: function () {
            this.availableCallSources = [];
            let campaign_sources = map(this.campaignPhones, 'call_source_name');
            if (campaign_sources.length == 0) {
                this.availableCallSources = this.callSources;
                return;
            }

            map(this.callSources, (source, index) => {
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
        addNewTag: function() {
            this.addCampaignTagForm.name = Str.snake(this.addCampaignTagForm.name);
            this.addCampaignTagForm.post(window.addNewTagUrl)
                .then((response) => {
                    this.getTags();
                    this.addCampaignTagForm.reset();
                    this.$toastr.success("Tag added");
                })
                .catch((error) => {
                    console.error(error.response.data.message);
                    this.$toastr.error("Unable to add tag: " + error.response.data.message);
                });
        },
        removeTag: function (tag) {
            this.removeTagForm.delete(generateRoute(window.deleteTagUrl, {tagName: tag}))
                .then((response) => {
                    var index = this.tags.findIndex(t => t.name == tag.name);
                    console.log(tag, index);
                    this.tags.splice(index, 1);
                    console.log(this.tags);
                })
                .catch((error) => {
                    console.error(error);
                    this.$toastr.error("Unable to delete the tag");
                });
        },
        showVerificationStartedAlert: function(variant, message) {
            this.verificationStartedVariant = variant;
            this.verificationStartedMessage = message;
            this.dismissCountDown = this.dismissSeconds;
        },
        startPhoneNumberVerification: function (number) {
            this.$set(this.phoneVerificationForm, 'phone', this.smsOnCallbackNumber);

            let valid = true;
            this.$v.phoneVerificationForm.phone.$touch();
            if (this.$v.phoneVerificationForm.phone.$error) {
                this.$set(this.phoneVerificationForm, 'phone', '');
                return;
            } else {
                this.$v.$reset();
            }

            this.phoneVerificationForm.post(window.sendPhoneVerificationUrl)
                .then((response) => {
                    if (response.status === 'started') {
                        this.$toastr.success("Verification Code Sent!");
                        this.verificationStarted = true;
                        this.showVerificationStartedAlert("success", response.message);
                    } else if (response.status === 'already-started') {
                        this.verificationStarted = true;
                        this.showVerificationStartedAlert("success", response.message);
                    } else if (response.status === 'already-verified') {
                        this.campaignForm.sms_on_callback_number.push(this.smsOnCallbackNumber);
                        this.smsOnCallbackNumber = '';
                        this.verificationStarted = false;
                        this.phoneVerificationForm.reset();
                        this.$toastr.success("Phone Number was already verified in our system!");
                    } else {
                        window.PmEvent.fire('errors.api', "Unable to obtain verification code");
                    }
                })
                .catch((error) => {
                    window.PmEvent.fire('errors.api', "Unable to send verification code. Check the number and try again.");
                });
        },
        finishPhoneNumberVerification: function (number, code) {
            this.$set(this.phoneVerificationForm, 'code', this.phoneVerificationCode);
            this.phoneVerificationForm.post(window.phoneVerificationUrl)
                .then((response) => {
                    if (response.status === 'verified') {
                        this.campaignForm.sms_on_callback_number.push(this.smsOnCallbackNumber);
                        this.smsOnCallbackNumber = '';
                        this.verificationStarted = false;
                        this.phoneVerificationForm.reset();
                        this.$toastr.success("Phone Number successfully verified!");
                        this.showVerificationStartedAlert("success", "Phone number successfully verified!");
                    } else if (response.status === 'failed') {
                        this.phoneVerificationCode = '';
                        this.$toastr.warning("Incorrect Verification Code");
                        this.showVerificationStartedAlert("warning", "Incorrect code entered");
                    } else if (response.status === 'delayed') {
                        this.phoneVerificationCode = '';
                        this.$toastr.error("Verification locked for this number");
                        this.showVerificationStartedAlert("danger", response.message);
                    } else if (response.status === 'blocked') {
                        this.phoneVerificationCode = '';
                        this.$toastr.error("Verification permanently blocked for this number");
                        this.showVerificationStartedAlert("danger", response.message);
                    }
                })
                .catch((error) => {
                    window.PmEvent.fire('errors.api', "Unable to confirm verification code: " + error);
                });
        },
        clearError: function (form, field) {
            form.errors.clear(field);
        },
        closeModal: function (modalRef) {
            this.$refs[modalRef].hide();
        },
        formatDate: function (date) {
            return date.toISOString();
        },
        getCampaignPhones: function () {
            this.getCampaignPhonesForm.get(window.getCampaignPhonesUrl)
                .then((response) => {
                    this.campaignPhones = response;
                    this.updateCallSources();
                    this.setupPhoneNumberForms();
                })
                .catch((error) => {
                    window.PmEvent.fire('errors.api', "Unable to fetch campaign phones: " + error);
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

                    // Reset form data with a new form instance
                    this.purchasePhoneNumberForm = new Form({
                        call_source_name: '',
                        campaign_id: window.campaign.id,
                        forward: '',
                        phone_number: null,
                    });

                    this.showAvailablePhoneNumbers = false;
                    this.getCampaignPhones();
                    this.closeModal('addPhoneModalRef');
                }, (error) => {
                    this.loadingPurchaseNumber = false;
                    window.PmEvent.fire('errors.api', 'Unable to process your request: ' + error);
                });
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
                    console.log('enable_text_to_value', this.campaignForm);
                    this.campaign.enable_text_to_value = this.campaignForm.enable_text_to_value;
                    this.$swal({
                        title: 'Campaign Updated!',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        // window.location.replace(window.campaignStatsUrl);
                    });
                })
                .catch(e => {
                    window.PmEvent.fire('errors.api', "Unable to process your request");
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
                    if (error.response.status !== 422) {
                        window.PmEvent.fire('errors.api', 'Unable to get phone numbers.');
                    }

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
    },
    validations() {
        return {
            phoneVerificationForm: {
                phone: { required, isNorthAmericanPhoneNumber },
                code: { between: between(100000,999999) }
            }
        };
    }
});
