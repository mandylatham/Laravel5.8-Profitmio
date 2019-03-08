import Vue from 'vue';
import Form from './../../common/form';
import VueFormWizard from 'vue-form-wizard';
Vue.use(VueFormWizard);
// Sweet Alert
import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);

window['app'] = new Vue({
    el: '#registration',
    components: {
        'spinner-icon':  require('./../../components/spinner-icon/spinner-icon').default,
    },
    data: {
        errors: [],
        errorMessage: '',
        loading: false,
        userForm: new Form({
            first_name: '',
            last_name: '',
            email: '',
            password: '',
            timezone: '',
            password_confirmation: '',
        }),
    },
    methods: {
        clearError: function (field) {
            this.userForm.errors.clear(field);
        },
        validateUserDetails: function () {
            this.userForm.errors.clear();
            if (!this.userForm.first_name) {
                this.userForm.errors.add('first_name', 'This field is required.');
            }
            if (!this.userForm.last_name) {
                this.userForm.errors.add('last_name', 'This field is required.');
            }
            return !!(this.userForm.first_name && this.userForm.last_name);
        },
        validateContactTab: function () {
            this.userForm.errors.clear();
            if (!window.userIsAdmin && !this.userForm.timezone) {
                this.userForm.errors.add('timezone', 'This field is required.');
            }
            return !!(window.userIsAdmin || (!window.userIsAdmin && this.userForm.timezone));
        },
        validateAuthTab: function () {
            this.userForm.errors.clear();
            let valid = true;
            if (!this.userForm.password) {
                this.userForm.errors.add('password', 'This field is required.');
                valid = false;
            }
            if (!this.userForm.password_confirmation) {
                this.userForm.errors.add('password_confirmation', 'This field is required.');
                valid = false;
            }
            if (this.userForm.password !== this.userForm.password_confirmation) {
                this.userForm.errors.add('password_confirmation', 'Passwords doesn\'t match.');
                valid = false;
            }
            return valid;
        },
        signup() {
            this.loading = true;
            this.userForm
                .post(window.signupUrl)
                .then(response => {
                    this.$swal({
                        title: 'Account Completed!',
                        text: 'You will now be redirected to login page.',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.replace(response.redirect_url);
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
