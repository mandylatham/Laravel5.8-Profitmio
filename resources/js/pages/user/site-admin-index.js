import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
import axios from 'axios';
// Toastr Library
import VueToastr2 from 'vue-toastr-2'
window.toastr = require('toastr');
Vue.use(VueToastr2);
import {generateRoute} from './../../common/helpers'

window['app'] = new Vue({
    el: '#user-index',
    components: {
        'pm-responsive-table': require('./../../components/pm-responsive-table/pm-responsive-table'),
        'user-role': require('./../../components/user-role/user-role')
    },
    filters: {
        'userRole': require('./../../filters/user-role.filter')
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
        columnData: [
            {
                slot: 'id',
                is_manager: true,
                widths: {

                }
            }, {
                slot: 'companies',
                widths: {
                    'lg': '240px'
                }
            }, {
                field: 'email',
                widths: {
                    'lg': '200px'
                }
            }, {
                slot: 'phone_number',
                classes: ['text-center'],
                widths: {
                    'lg': '160px'
                }
            }, {
                slot: 'options',
                is_manager_footer: true,
                classes: ['text-center'],
                widths: {
                    'lg': '140px'
                }
            }
        ],
        searchTerm: '',
        companySelected: null,
        tableOptions: {
            mobile: 'lg'
        },
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
                    this.$toastr.error("Unable to get users");
                });
        },
        onPageChanged(event) {
            this.searchForm.page = event.page;
            return this.fetchData();
        }
    }
});
