import Vue from 'vue';
import Form from './../../common/form';

window['app'] = new Vue({
    el: '#selector',
    components: {
        'spinner-icon':  require('./../../components/spinner-icon/spinner-icon').default,
    },
    data: {
        errors: [],
        errorMessage: '',
        loading: false,
        companyForm: new Form({
            company: window.activeCompanyId
        }),
    },
    methods: {
        selectCompany() {
            this.loading = true;
            this.companyForm
                .post(window.selectCompanyUrl, {
                    header: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    window.location.replace(response.redirect_url);
                }, error => {
                    if (error && error.errors) {
                        let errs = [];
                        for (const key of Object.keys(error.errors)) {
                            error.errors[key].forEach(msg => {
                                errs.push(msg);
                            });
                        }
                        this.errors = errs;
                    } else if (error.message) {
                        this.errorMessage = error.message;
                    }
                    this.loading = false;
                });
        }
    }
});
