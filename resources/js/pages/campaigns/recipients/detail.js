import Vue from 'vue';
import './../../../common';
import Form from "../../../common/form";
import axios from "axios";
import {generateRoute} from '../../../common/helpers';

window['app'] = new Vue({
    el: '#recipients-detail',
    computed: {
        pagination: function () {
            return {
                page: this.searchForm.page,
                per_page: this.searchForm.per_page,
                total: this.total
            };
        }
    },
    components: {
        'pm-pagination': require('./../../../components/pm-pagination/pm-pagination').default,
        'spinner-icon': require('./../../../components/spinner-icon/spinner-icon').default
    },
    data: {
        checkAll: false,
        recipientsSelected: [],
        recipients: [],
        searchForm: new Form({
            q: null,
            page: 1,
            per_page: 50
        }),
        loading: false,
        total: 0
    },
    methods: {
        selectAllRecipients() {
            if (this.checkAll) {
                this.recipients.forEach(rec => {
                    if (!rec.dropped_at) {
                        rec.checked = true;
                    }
                })
            } else {
                this.recipients.forEach(rec => {
                    rec.checked = false;
                });
            }
        },
        deleteRecipients() {
            const recipientsToDelete = [];
            this.recipients.forEach(rec => {
                if (rec.checked) {
                    recipientsToDelete.push(rec.id);
                }
            });
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
                    return axios
                        .delete(window.deleteRecipientsUrl, {
                            params: {'recipient_ids': recipientsToDelete}
                        });
                }
            }).then(result => {
                if (result.value) {
                    this.searchForm.page = 1;
                    this.fetchData();
                    this.$swal({
                        title: 'Recipients Deleted!',
                        type: 'success'
                    });
                }
            }, error => {
                window.PmEvent.fire('errors.api', 'Unable to process your request');
            });
        },
        fetchData() {
            this.loading = true;
            this.searchForm
                .get(window.searchRecipientsUrl)
                .then(response => {
                    this.recipients = response.data;
                    this.searchForm.page = response.meta.current_page;
                    this.searchForm.per_page = response.meta.per_page;
                    this.total = response.meta.total;
                    this.loading = false;
                })
                .catch(error => {
                    window.PmEvent.fire('errors.api', "Unable to get recipient lists");
                });
        },
        generateRoute,
        onPageChanged(event) {
            this.searchForm.page = event.page;
            return this.fetchData();
        },
    },
    mounted() {
        this.fetchData();
    }
});
