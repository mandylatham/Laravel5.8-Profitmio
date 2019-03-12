import Vue from 'vue';
import './../../../common';
import axios from 'axios';
import {generateRoute} from "../../../common/helpers";
import moment from 'moment';


window['app'] = new Vue({
    el: '#deployments-details',
    components: {
        'spinner-icon': require('./../../../components/spinner-icon/spinner-icon').default,
    },
    computed: {
        pendingSmsCounter: function () {
            return this.recipients.reduce((acc, recipient) => {
                return acc + (!recipient.pivot.sent_at && !recipient.pivot.failed_at ? 1 : 0);
            }, 0);
        },
        sentSmsCounter: function () {
            return this.recipients.reduce((acc, recipient) => {
                return acc + (recipient.pivot.sent_at || recipient.pivot.failed_at ? 1 : 0);
            }, 0);
        },
        currentRecipient: function () {
            let recipientToReturn = null;
            this.recipients.forEach(recipient => {
                if (!recipientToReturn && !recipient.pivot.sent_at && !recipient.pivot.failed_at) {
                    recipientToReturn = recipient;
                    return false;
                }
            });
            return recipientToReturn;
        }
    },
    data: {
        loading: false,
        drop: {},
        campaign: {},
        recipients: []
    },
    mounted() {
        this.recipients = window.recipients;
        this.drop = window.drop;
        this.campaign = window.campaign;
    },
    methods: {
        sendMessage() {
            this.loading = true;
            axios
                .post(generateRoute(window.sendSms, {recipientId: this.currentRecipient.id}), {})
                .then(response => {
                    this.$toastr.success('This recipient has been sent a customized copy of the sms message');
                    this.loading = false;
                    this.currentRecipient.pivot.sent_at = moment().format('YYYY-MM-DD HH:mm:ss');
                }, error => {
                    window.PmEvent.fire('errors.api', 'Error sending message to ' + this.currentRecipient.first_name + ' '  + this.currentRecipient.last_name);
                    this.currentRecipient.pivot.failed_at = moment().format('YYYY-MM-DD HH:mm:ss');
                    this.loading = false;
                });
        }
    }
});
