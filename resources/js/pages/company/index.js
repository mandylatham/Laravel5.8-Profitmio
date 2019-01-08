import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
// Toastr Library
import VueToastr2 from 'vue-toastr-2'
window.toastr = require('toastr');
Vue.use(VueToastr2);
import {generateRoute} from './../../common/helpers'

window['app'] = new Vue({
    el: '#company-index',
    components: {
        'pm-responsive-table': require('./../../components/pm-responsive-table/pm-responsive-table'),
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
        this.companyEditUrl = window.companyEditUrl;

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
                    this.searchForm.page = response.current_page;
                    this.searchForm.per_page = response.per_page;
                    this.total= response.total;
                    this.isLoading = false;
                })
                .catch(error => {
                    this.$toastr.error("Unable to get companies");
                });
        },
        onPageChanged(event) {
            this.searchForm.page = event.page;
            return this.fetchData();
        }
    }
});
