import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import './../../filters/user-role.filter';
import axios from 'axios';

window['app'] = new Vue({
    el: '#user-create',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon'),
    },
    data: {
        companies: [],
        companySelected: null,
        isAdmin: false,
        loading: false,
        roles: [],
        userForm: new Form({
            role: null,
            first_name: '',
            last_name: '',
            email: '',
            company: null
        })
    },
    mounted() {
        this.isAdmin = window.isAdmin;
        if (this.isAdmin) {
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
                        per_page: 100
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
                        title: 'User Added',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.replace(window.userIndexUrl);
                    });
                })
                .catch(e => {
                    this.$toastr.error("Unable to process your request");
                    this.loading = false;
                });
        }
    }
});
