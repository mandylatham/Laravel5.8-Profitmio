import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import {generateRoute} from './../../common/helpers'
import VueFormWizard from 'vue-form-wizard';
import InputTag from 'vue-input-tag'
Vue.use(VueFormWizard);

window['app'] = new Vue({
    el: '#app',
    components: {
        'input-errors': require('./../../components/input-errors/input-errors'),
        InputTag
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
        }),
    },
    mounted() {
        this.createFormUrl = window.createUrl;
        this.companyIndex = window.indexUrl;
    },
    methods: {
        generateRoute,
        validateBasicTab() {

        },
        validateContactTab() {

        },
        validateSocialTab() {

        },
        saveCompany() {
            this.isLoading = true;
            this.createForm.post(this.createFormUrl)
                .then(response => {
                    this.isLoading = false;
                    this.$toastr.success("Company Added");
                    setTimeout(function () { window.location = this.companyIndex; }, 400);
                })
                .catch(error => {
                    this.createForm.errors = error.errors;
                    this.$toastr.error("Unable to create company");
                });
        },
        onSubmit() {
            this.isLoading = true;
            this.createForm.post(this.createFormUrl)
                .then(response => {
                    this.isLoading = false;
                    this.$toastr.success("Company Added");
                    setTimeout(function () { window.location = this.companyIndex; }, 400);
                })
                .catch(error => {
                    this.createForm.errors = error.errors;
                    this.$toastr.error("Unable to create company");
                });
        },
    }
});
