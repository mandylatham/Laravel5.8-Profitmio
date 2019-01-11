import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import {generateRoute} from './../../common/helpers'


window['app'] = new Vue({
    el: '#company-user-index',
    components: {
        'pm-responsive-table': require('./../../components/pm-responsive-table/pm-responsive-table'),
        'user-role': require('./../../components/user-role/user-role'),
        'status': require('./../../components/status/status')
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
        columnData: [
            {
                slot: 'id',
                is_manager: true,
            }, {
                slot: 'type'
            }, {
                slot: 'mail'
            }, {
                slot: 'phone_number'
            }, {
                slot: 'status'
            }, {
                slot: 'options',
                is_manager_footer: true
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
        this.searchForm.q = window.q;
        this.userEditUrl = window.userEditUrl;
        this.userImpersonateUrl = window.userImpersonateUrl;
        this.userActivateUrl = window.userActivateUrl;
        this.userDeactivateUrl = window.userDeactivateUrl;
        this.fetchData();
    },
    methods: {
        generateRoute,
        fetchData() {
            this.isLoading = true;
            this.searchForm
                .get(this.searchFormUrl)
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
