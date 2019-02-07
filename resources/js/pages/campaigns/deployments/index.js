import Vue from 'vue';
import './../../../common';
import Form from "../../../common/form";
import axios from "axios";
import {generateRoute} from '../../../common/helpers';

window['app'] = new Vue({
    el: '#drops-index',
    computed: {
        pagination: function () {
            return {
                page: this.searchDropForm.page,
                per_page: this.searchDropForm.per_page,
                total: this.total
            };
        }
    },
    components: {
        'pm-pagination': require('./../../../components/pm-pagination/pm-pagination'),
        'spinner-icon': require('./../../../components/spinner-icon/spinner-icon'),
        'drop-status': require('./../../../components/drop-status/drop-status'),
    },
    data: {
        drops: [],
        dropEditUrl: '',
        dropRunSmsUrl: '',
        loading: false,
        searchDropForm: new Form({
            q: null,
            page: 1,
            per_page: 15
        }),
        total: 0
    },
    methods: {
        deleteDrop(drop) {
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
                    return axios.delete(generateRoute(window.deleteDropUrl, {'dropId': drop.id}));
                }
            }).then(result => {
                if (result.value) {
                    this.$swal({
                        title: 'Drop Deleted',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.replace(window.dropIndexUrl);
                    });
                }
            }, error => {
                this.$toastr.error('Unable to process your request');
            });
        },
        fetchData() {
            this.loading = true;
            this.searchDropForm
                .get(window.searchDropsUrl)
                .then(response => {
                    this.drops = response.data;
                    this.searchDropForm.page = response.current_page;
                    this.searchDropForm.per_page = response.per_page;
                    this.total = response.total;
                    this.loading = false;
                })
                .catch(error => {
                    this.$toastr.error("Unable to get drops");
                });
        },
        generateRoute,
        onPageChanged(event) {
            this.searchDropForm.page = event.page;
            return this.fetchData();
        }
    },
    mounted() {
        this.fetchData();
        this.dropRunSmsUrl = window.dropRunSmsUrl;
        this.dropEditUrl = window.dropEditUrl;
    }
});
