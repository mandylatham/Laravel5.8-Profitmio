import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import {generateRoute} from './../../common/helpers'
import axios from "axios";

window['app'] = new Vue({
    el: '#company-user-index',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination').default,
        'user-role': require('./../../components/user-role/user-role').default,
        'user-status': require('./../../components/user-status/user-status').default
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
        this.searchForm.q = window.q;
        this.userEditUrl = window.userEditUrl;
        this.userImpersonateUrl = window.userImpersonateUrl;
        this.userActivateUrl = window.userActivateUrl;
        this.userDeactivateUrl = window.userDeactivateUrl;
        this.fetchData();
    },
    methods: {
        generateRoute,
        deactivateUser: function (user) {
            this.$swal({
                title: "Are you sure?",
                text: "You want to deactivate this user?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                allowOutsideClick: false,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return axios.get(generateRoute(window.userDeactivateUrl, {'userId': user.id}));
                }
            }).then(result => {
                if (result.value) {
                    this.$set(user, 'is_active', false);
                    this.$swal({
                        title: 'User Deactivated',
                        type: 'success',
                        allowOutsideClick: true
                    }).then(() => {
                    });
                }
            }, error => {
                window.PmEvent.fire('errors.api', 'Unable to process your request');
            });
        },
        activateUser: function (user) {
            this.$swal({
                title: "Are you sure?",
                text: "You want to activate this user?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#38c172",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                allowOutsideClick: false,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return axios.get(generateRoute(window.userActivateUrl, {'userId': user.id}));
                }
            }).then(result => {
                if (result.value) {
                    this.$set(user, 'is_active', true);
                    this.$swal({
                        title: 'User Activated',
                        type: 'success',
                        allowOutsideClick: true
                    }).then(() => {
                    });
                }
            }, error => {
                window.PmEvent.fire('errors.api', 'Unable to process your request');
            });
        },
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
                    window.PmEvent.fire('errors.api', "Unable to get users");
                });
        },
        onPageChanged(event) {
            this.searchForm.page = event.page;
            return this.fetchData();
        }
    }
});
