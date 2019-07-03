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
        'pm-pagination': require('./../../../components/pm-pagination/pm-pagination').default,
        'spinner-icon': require('./../../../components/spinner-icon/spinner-icon').default,
        'drop-status': require('./../../../components/drop-status/drop-status').default,
    },
    data: {
        drops: [],
        dropEditUrl: '',
        dropRunSmsUrl: '',
        loading: false,
        searchDropForm: new Form({
            q: null,
            page: 1,
            per_page: 15,
            type: null
        }),
        total: 0,
        types: [{
            label: 'Sms',
            value: 'sms'
        }, {
            label: 'Email',
            value: 'email'
        }],
        typeSelected: null
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
                window.PmEvent.fire('errors.api', 'Unable to process your request');
            });
        },
        fetchData() {
            this.loading = true;
            if (this.typeSelected) {
                this.searchDropForm.type = this.typeSelected.value;
            } else {
                this.searchDropForm.type = null;
            }
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
                    window.PmEvent.fire('errors.api', "Unable to get drops");
                });
        },
        generateRoute,
        onPageChanged(event) {
            this.searchDropForm.page = event.page;
            return this.fetchData();
        },
        startDrop(drop) {
            if (drop.sms_phones === 0) {
                this.$swal({
                    title: "Cannot send SMS",
                    text: "This campaign does not have a phone number from which to send SMS messages!",
                    footer: '<a href="'+window.campaignEditUrl+'">Click here to add one</a>',
                    type: "error",
                    showCancelButton: false,
                    confirmButtonText: "Ok",
                    allowOutsideClick: false,
                });
            } else {
                this.$swal({
                    title: "Are you sure?",
                    text: "Do you want to start this drop?",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#38c172",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return axios.get(generateRoute(window.startDropUrl, {'dropId': drop.id}));
                    }
                }).then(result => {
                    if (result.value) {
                        drop.status = 'Processing';
                        window.location.href = generateRoute(this.dropRunSmsUrl, {'dropId': drop.id});
                    }
                }, error => {
                    window.PmEvent.fire('errors.api', 'Unable to process your request');
                });
            }
        }
    },
    mounted() {
        this.fetchData();
        this.dropRunSmsUrl = window.dropRunSmsUrl;
        this.dropEditUrl = window.dropEditUrl;
    }
});
