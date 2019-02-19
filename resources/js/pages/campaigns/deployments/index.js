import Vue from 'vue';
import './../../../common';
import Form from "../../../common/form";
import axios from "axios";
import {generateRoute} from '../../../common/helpers';
import moment from "moment-timezone";

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
        canStartDrop(drop) {
            if (drop.send_at) {
                const current = moment.utc().tz(window.timezone);
                const sendAt = moment.utc(drop.send_at, 'YYYY-MM-DD HH:mm:ss').tz(window.timezone);
                return current.isSameOrAfter(sendAt);
            }
            return false
        },
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
                    this.$toastr.error('Unable to process your request');
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
