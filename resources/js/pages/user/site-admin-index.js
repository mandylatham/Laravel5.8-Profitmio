import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import axios from 'axios';
import {generateRoute} from './../../common/helpers'

window['app'] = new Vue({
    el: '#user-index',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination').default,
        'user-role': require('./../../components/user-role/user-role').default
    },
    filters: {
        'userRole': require('./../../filters/user-role.filter').default
    },
    computed: {
        pagination: function () {
            return {
                page: this.searchForm.page,
                per_page: this.searchForm.per_page,
                total: this.total
            };
        }
    },
    data: {
        searchFormUrl: null,
        searchForm: new Form({
            company: null,
            q: null,
            page: 1,
            per_page: 15,
        }),
        isAdmin: undefined,
        isLoading: true,
        total: null,
        users: [],
        companies: [],
        searchTerm: '',
        companySelected: null,
        formUrl: '',
        userEditUrl: '',
        userImpersonateUrl: '',
        userActivateUrl: '',
        userDeactivateUrl: ''
    },
    mounted() {
        this.searchFormUrl = window.searchFormUrl;
        this.companySelected = window.companySelected;
        this.searchForm.q = window.q;
        this.userEditUrl = window.userEditUrl;
        this.userImpersonateUrl = window.userImpersonateUrl;
        this.userActivateUrl = window.userActivateUrl;
        this.userDeactivateUrl = window.userDeactivateUrl;

        axios
            .get(window.getCompanyUrl, {
                headers: {
                    'Content-Type': 'application/json'
                },
                params: {
                    per_page: 100
                },
                data: null
            })
            .then(response => {
                this.companies = response.data.data;
            });

        this.fetchData();
    },
    methods: {
        generateRoute,
        isActiveInCompany: function (user, companyId) {
            let isActive = false;
            user.companies.forEach(company => {
                if (company.id === companyId) {
                    isActive = company.is_active;
                }
            });
            return isActive;
        },
        onCompanySelected() {
            this.searchForm.page = 1;
            return this.fetchData();
        },
        fetchData() {
            if (this.companySelected) {
                this.searchForm.company = this.companySelected.id;
            } else {
                this.searchForm.company = null;
            }
            this.isLoading = true;
            this.searchForm.get(this.searchFormUrl)
                .then(response => {
                    this.users = response.data;
                    this.searchForm.page = response.meta.current_page;
                    this.searchForm.per_page = response.meta.per_page;
                    this.total = response.meta.total;
                    this.isLoading = false;
                })
                .catch(error => {
                    window.PmEvent.fire('errors.api', "Unable to get users");
                });
        },
        onPageChanged(event) {
            this.searchForm.page = event.page;
            return this.fetchData();
        }
    }
});
