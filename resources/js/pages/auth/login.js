import Vue from 'vue';
import Form from './../../common/form';
import forEach from 'lodash';

window['app'] = new Vue({
    el: '#login',
    components: {
        'spinner-icon':  require('./../../components/spinner-icon/spinner-icon').default,
    },
    data: {
        errors: [],
        errorMessage: null,
        userForm: new Form({
            email: null,
            password: null,
        }),
        loading: false
    },
    methods: {
        login() {
            this.loading = true;
            this.errors = [];
            this.errorMessage = null;
            this.userForm
                .post(window.authUrl)
                .then(response => {
                    window.location.replace(response.redirect_url);
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
        },
    }
});
