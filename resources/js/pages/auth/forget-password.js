import Vue from 'vue';
import Form from './../../common/form';
import VueFormWizard from 'vue-form-wizard';
Vue.use(VueFormWizard);
// Sweet Alert
import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);

window['app'] = new Vue({
    el: '#forget-password',
    components: {
        'spinner-icon':  require('./../../components/spinner-icon/spinner-icon').default,
    },
    data: {
        errors: [],
        errorMessage: '',
        loading: false,
        userForm: new Form({
            email: ''
        }),
    },
    methods: {
        clearError(field) {
            this.userForm.errors.clear(field);
        },
        reset() {
            this.userForm.errors.clear();
            if (!this.userForm.email) {
                this.userForm.errors.add('email', 'This field is required.');
                return;
            }
            this.loading = true;
            this.userForm
                .post(window.resetPasswordUrl)
                .then(response => {
                    this.$swal({
                        title: 'Link Sent!',
                        text: response.status,
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = window.loginUrl;
                    });
                }, error => {
                    if (error && error.errors) {
                        let errs = [];
                        for (const key of Object.keys(error.errors)) {
                            error.errors[key].forEach(msg => {
                                errs.push(msg);
                            });
                        }
                        this.errors = errs;
                    } else if (error && error.response && error.response.data && error.response.data.message) {
                        this.errorMessage = error.response.data.message;
                    }
                    this.loading = false;
                });
        }
    }
});
