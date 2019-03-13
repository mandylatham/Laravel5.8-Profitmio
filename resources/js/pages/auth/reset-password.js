import Vue from 'vue';
import Form from './../../common/form';
import VueFormWizard from 'vue-form-wizard';
Vue.use(VueFormWizard);
// Sweet Alert
import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);

window['app'] = new Vue({
    el: '#reset-password',
    components: {
        'spinner-icon':  require('./../../components/spinner-icon/spinner-icon').default,
    },
    data: {
        errors: [],
        errorMessage: '',
        loading: false,
        userForm: new Form({
            email: '',
            token: window.token,
            password: '',
            password_confirmation: '',
        }),
    },
    methods: {
        clearError(field) {
            this.userForm.errors.clear(field);
        },
        reset() {
            var valid = true;
            this.userForm.errors.clear();
            if (!this.userForm.email) {
                this.userForm.errors.add('email', 'This field is required.');
                valid = false;
            }
            if (!this.userForm.password) {
                this.userForm.errors.add('password', 'This field is required.');
                valid = false;
            }
            if (!this.userForm.password_confirmation) {
                this.userForm.errors.add('password_confirmation', 'This field is required.');
                valid = false;
            }
            if (this.userForm.password_confirmation !== this.userForm.password) {
                this.userForm.errors.add('password_confirmation', 'Passwords doesn\'t match.');
                valid = false;
            }
            if (!valid) {
                return;
            }
            this.loading = true;
            this.userForm
                .post(window.updatePasswordUrl)
                .then(response => {
                    this.$swal({
                        title: 'Password Updated!',
                        text: response.status,
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = window.loginUrl;
                    });
                }, error => {
                    if (error && error.response && error.response.data && error.response.data.errors) {
                        let errs = [];
                        for (const key of Object.keys(error.response.data.errors)) {
                            error.response.data.errors[key].forEach(msg => {
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
