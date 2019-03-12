import Vue from 'vue';
import './../../common';
import Form from './../../common/form';

window['app'] = new Vue({
    el: '#profile',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
    },
    computed: {
    },
    data: {
        userForm: new Form({...window.user}),
        passwordForm: new Form({
            current_password: '',
            new_password: '',
            new_password_confirmation: '',
        }),
        loadingUserForm: false,
        loadingPasswordForm: false,
    },
    methods: {
        updateProfile() {
            this.loadingUserForm = true;
            this.userForm
                .post(window.updateUserUrl)
                .then(response => {
                    this.loadingUserForm = false;
                    this.$toastr.success("User updated");
                })
                .catch(error => {
                    this.loadingUserForm = false;
                    window.PmEvent.fire('errors.api', "Unable to update");
                });
        },
        resetPassword() {
            this.loadingPasswordForm = true;
            this.passwordForm
                .post(window.updatePasswordUrl)
                .then(response => {
                    this.loadingPasswordForm = false;
                    this.passwordForm = new Form({
                        current_password: '',
                        new_password: '',
                        new_password_confirmation: '',
                    });
                    this.$toastr.success("Password updated");
                })
                .catch(error => {
                    this.loadingPasswordForm = false;
                    window.PmEvent.fire('errors.api', "Unable to update");
                });
        }
    }
});
