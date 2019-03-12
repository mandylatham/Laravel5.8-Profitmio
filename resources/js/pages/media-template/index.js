import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import axios from 'axios';
import {generateRoute} from './../../common/helpers'

// Bootstrap Vue
import Modal from 'bootstrap-vue'
Vue.use(Modal);

window['app'] = new Vue({
    el: '#template-index',
    components: {
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination').default,
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
        'media-type': require('./../../components/media-type/media-type').default,
    },
    computed: {
        pagination: function () {
            return {
                page: this.searchForm.page,
                per_page: this.searchForm.per_page,
                total: this.total
            };
        },
        template_text: function () {
            if (this.media_template.type == 'sms') {
                return this.media_template.text_message;
            }
            if (this.media_template.type == 'email') {
                return this.media_template.email_text;
            }

            return;
        }
    },
    data: {
        searchFormUrl: null,
        searchForm: new Form({
            type: null,
            q: localStorage.getItem('templatesIndexQ'),
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
        },
        mediaTemplateClosed: true,
        templateEdit: '',
        templateDelete: ''
    },
    mounted() {
        this.templateEdit = window.templateEdit;
        this.templateDelete = window.templateDelete;
        this.searchFormUrl = window.searchFormUrl;

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
        onCompanySelected: function () {
            this.searchForm.page = 1;
            return this.fetchData();
        },
        fetchData: function () {
            if (this.searchForm.q) {
                localStorage.setItem('templatesIndexQ', this.searchForm.q);
            } else {
                localStorage.removeItem('templatesIndexQ');
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
                    window.PmEvent.fire('errors.api', "Unable to get templates");
                });
        },
        deleteTemplate: function (id, index) {
            var route = generateRoute(this.templateDelete, {templateId: id});
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
                    return axios.delete(route);
                }
            }).then(result => {
                if (result.value) {
                    this.$toastr.success("User deleted");
                    this.templates.splice(index, 1);
                }
            }, error => {
                window.PmEvent.fire('errors.api', "Unable to delete user");
            });
        },
        onPageChanged: function (event) {
            this.searchForm.page = event.page;
            return this.fetchData();
        },
        generateRoute
    }
});
