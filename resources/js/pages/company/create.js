import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import {generateRoute} from './../../common/helpers';
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
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon'),
        'resumable': require('./../../components/resumable/resumable'),
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
        imagePreviewUrl: null,
        isLoading: false,
    },
    mounted() {
        this.createFormUrl = window.createUrl;
        this.companyIndex = window.indexUrl;
    },
    methods: {
        onFileAdded({file: resumableFile}) {
            const fileReader = new FileReader();
            // Generate preview
            this.createForm.image = resumableFile.file;
            fileReader.readAsDataURL(resumableFile.file);
            fileReader.onload = event => {
                this.imagePreviewUrl = event.target.result;
            };
        },
        removeSelectedImage() {
            this.createForm.image = null;
            this.imagePreviewUrl = null;
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
                .then(() => {
                    this.isLoading = false;
                    this.$swal({
                        title: 'Company Added!',
                        type: 'success'
                    }).then(() => {
                        window.location.replace(this.companyIndex);
                    });
                }, error => {
                    this.isLoading = false;
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
