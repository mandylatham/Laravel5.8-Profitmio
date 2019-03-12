import Vue from 'vue';
import './../../common';
import axios from 'axios';
import Form from './../../common/form';
import {generateRoute} from './../../common/helpers'

window['app'] = new Vue({
    el: '#company-index',
    components: {
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination').default,
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
        'company-type': require('./../../components/company-type/company-type').default,
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
        companyEdit: '',
        companyDelete: '',
        isLoading: true,
        total: null,
        companies: [],
        searchTerm: '',
        companySelected: null,
        columnData: [
            {
                slot: 'id',
                is_manager: true,
            }, {
                field: 'type'
            }, {
                field: 'url'
            }, {
                field: 'phone'
            }, {
                slot: 'options',
                is_manager_footer: true
            }
        ],
        tableOptions: {
            mobile: 'lg'
        },
        formUrl: ''
    },
    mounted() {
        this.searchFormUrl = window.searchFormUrl;
        this.searchForm.q = window.q;
        this.companyEdit = window.companyEdit;
        this.companyDelete = window.companyDelete;

        this.fetchData();
    },
    methods: {
        generateRoute,
        onCompanySelected() {
            this.searchForm.page = 1;
            return this.fetchData();
        },
        fetchData() {
            this.isLoading = true;
            this.searchForm.get(this.searchFormUrl)
                .then(response => {
                    this.companies = response.data;
                    this.searchForm.page = response.meta.current_page;
                    this.searchForm.per_page = response.meta.per_page;
                    this.total= response.meta.total;
                    this.isLoading = false;
                })
                .catch(error => {
                    console.log(error);
                    window.PmEvent.fire('errors.api', "Unable to get companies");
                });
        },
        deleteCompany: function (id, index) {
            var route = generateRoute(this.companyDelete, {companyId: id});
            this.$swal({
                title: "Are you sure?",
                text: "You will not be able to undo this operation!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                allowOutsideClick: false,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    console.log(route);
                    return axios.delete(route);
                }
            }).then(result => {
                if (result.value) {
                    this.$toastr.success("Company deleted");
                    this.companies.splice(index, 1);
                }
            }, error => {
                window.PmEvent.fire('errors.api', "Unable to delete company");
            });
        },
        onPageChanged(event) {
            this.searchForm.page = event.page;
            return this.fetchData();
        }
    }
});
