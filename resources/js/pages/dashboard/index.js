import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
import axios from 'axios';
// Toastr Library
import VueToastr2 from 'vue-toastr-2'
window.toastr = require('toastr');
Vue.use(VueToastr2);
// Chart Library
import {filter} from 'lodash';
import Modal from 'bootstrap-vue'
Vue.use(Modal);

window['app'] = new Vue({
    el: '#template-index',
    components: {
        'media-template': require('./../../components/media-template/media-template'),
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination'),
        'pm-responsive-table': require('./../../components/media-template/media-template'),
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon'),
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
            type: null,
            q: null,
            page: 1,
            per_page: 15,
        }),
        isLoading: true,
        total: null,
        templates: [],
        companies: [],
        searchTerm: '',
        companySelected: null,
        tableOptions: {
            mobile: 'lg'
        }
    },
    mounted() {
        this.searchFormUrl = window.searchFormUrl;
        this.companySelected = window.companySelected;
        this.searchForm.q = window.q;

        axios
            .get(window.searchFormUrl, {
                headers: {
                    'Content-Type': 'application/json'
                },
                params: {
                    per_page: 100
                },
                data: null
            })
            .then(response => {
                this.templates = response.data.data;
            });

        this.fetchData();
    },
    methods: {
        onCompanySelected() {
            this.searchForm.page = 1;
            return this.fetchData();
        },
        fetchData() {
            if (this.companySelected) {
                this.searchForm.companySelected = this.companySelected.id;
            } else {
                this.searchForm.companySelected = null;
            }
            this.isLoading = true;
            this.searchForm.get(this.searchFormUrl)
                .then(response => {
                    this.templates = response.data;
                    this.searchForm.page = response.current_page;
                    this.searchForm.per_page = response.per_page;
                    this.total= response.total;
                    this.isLoading = false;
                })
                .catch(error => {
                    this.$toastr.error("Unable to get templates");
                });
        },
        onPageChanged(event) {
            this.searchForm.page = event.page;
            return this.fetchData();
        }
    }
});
