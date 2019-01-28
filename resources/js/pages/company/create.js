import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import {generateRoute} from './../../common/helpers'
// Wizard
import VueFormWizard from 'vue-form-wizard';
Vue.use(VueFormWizard);
// Validation
import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);
// Custom Validation
import { helpers, required, minLength, url } from 'vuelidate/lib/validators';
import { isNorthAmericanPhoneNumber, isCanadianPostalCode, isUnitedStatesPostalCode, looseAddressMatch } from './../../common/validators';

window['app'] = new Vue({
    el: '#app',
    components: {
        'input-errors': require('./../../components/input-errors/input-errors'),
    },
    data: {
        companyIndex: '',
        createFormUrl: null,
        createForm: new Form({
            name: '',
            type: '',
            country: '',
            phone: '',
            address: '',
            address2: '',
            city: '',
            state: '',
            zip: '',
            url: '',
            facebook: '',
            twitter: '',
            image: '',
        }),
    },
    mounted() {
        this.createFormUrl = window.createUrl;
        this.companyIndex = window.indexUrl;
    },
    methods: {
        addImageFile() {
            this.createForm.image = this.$refs.image.files[0];
            console.log(this.$refs.image.files[0]);
        },
        validateBasicTab() {
            let valid = true;
            ['name','type'].forEach(field => {
                this.$v.createForm[field].$touch();
                if (this.$v.createForm[field].$error) {
                    valid = false;
                }
            });
            return valid;
        },
        validateContactTab() {
            let valid = true;
            ['country','phone', 'address', 'address2', 'city', 'state', 'zip'].forEach(field => {
                this.$v.createForm[field].$touch();
                if (this.$v.createForm[field].$error) {
                    console.log(field, this.$v.createForm[field]);
                    valid = false;
                }
            });
            return valid;
        },
        validateSocialTab() {
            return true;
        },
        saveCompany() {
            this.isLoading = true;
            this.createForm.post(this.createFormUrl)
                .then(response => {
                    this.isLoading = false;
                    this.$toastr.success("Company Added");
                    // window.location.replace(this.companyIndex);
                })
                .catch(error => {
                    this.createForm.errors = error.errors;
                    this.$toastr.error("Unable to create company");
                });
        },
    },
    validations() {
        return {
            createForm: {
                name: { required, minLength: minLength(2) },
                type: { required },
                country: { required },
                phone: { required, isNorthAmericanPhoneNumber },
                address: { required, looseAddressMatch },
                address2: {},
                city: { required },
                state: { required, },
                zip: this.createForm.country == 'us' ? { required, isUnitedStatesPostalCode } : { required, isCanadianPostalCode },
                url: { url },
                facebook: { url },
                twitter: { url },
            }
        }
    }
});
