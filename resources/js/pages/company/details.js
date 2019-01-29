import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import {generateRoute} from './../../common/helpers'
// Wizard
import VueFormWizard from 'vue-form-wizard';
Vue.use(VueFormWizard);
// Chart Library
import VueChartkick from 'vue-chartkick';
import Chart from 'chart.js';
import {filter} from 'lodash';
import './../../filters/user-role.filter';
import vue2Dropzone from 'vue2-dropzone';
// Validation
import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);
// Custom Validation
import { helpers, required, minLength, url } from 'vuelidate/lib/validators';
import { isNorthAmericanPhoneNumber, isCanadianPostalCode, isUnitedStatesPostalCode, looseAddressMatch } from './../../common/validators';

window['app'] = new Vue({
    el: '#app',
    data: {
        company: {},
        companyIndex: '',
        modifiedCompany: {},
        updateForm: new Form({
            name: '',
            address: '',
            address2: '',
            city: '',
            state: '',
            zip: '',
        }),
        updateUrl: '',
        companyFormFields: ['name', 'address', 'address2', 'city', 'state', 'zip'],
        showCompanyFormControls: false,
        loadingCampaigns: false
    },
    mounted() {
        this.companyIndex = window.indexUrl;
        this.updateUrl = window.updateUrl;
        this.company = window.company;
        this.modifiedCompany = JSON.parse(JSON.stringify(this.company));
        this.updateFields();
    },
    methods: {
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
