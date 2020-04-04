import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import './../../filters/user-role.filter';
import axios from 'axios';

window['app'] = new Vue({
    el: '#user-create',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
    },
    data: {
        companies: [],
        companySelected: null,
        isAdmin: false,
        loading: false,
        roles: [],
        userForm: new Form({
            role: null,
            email: '',
            company: window.companySelectedId ? window.companySelectedId : null
        })
    },
    mounted() {
        this.isAdmin = window.isAdmin;
        if (this.isAdmin && window.companySelectedId) {
            this.roles = ['admin', 'user'];
            this.fetchCompanies();
        } else if (this.isAdmin && !window.companySelectedId) {
            this.roles = ['admin', 'user', 'site_admin'];
            this.fetchCompanies();
        } else {
            this.roles = ['user', 'site_admin'];
        }
    },
    methods: {
        fetchCompanies: function() {
            axios
                .get(window.getCompaniesUrl, {
                    params: {
                        per_page: 1000
                    }
                })
                .then(response => {
                    this.companies = response.data.data;
                });
        },
        saveCompany: function () {
            this.loading = true;
            if (this.companySelected) {
                this.userForm.company = this.companySelected.id;
            }
            this.userForm
                .post(window.addUserUrl)
                .then(() => {
                    this.loading = false;
                    this.$swal({
                        title: 'Invitation Sent',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.replace(window.userIndexUrl);
                    });
                })
                .catch(e => {
                    this.loading = false;
                    if (e.error !== undefined){
                        window.PmEvent.fire('errors.api', "Unable to process invitation: " + e.error);
                        return;
                    }

                    window.PmEvent.fire('errors.api', "Unable to process the invitation");
                });
        }
    }
});
