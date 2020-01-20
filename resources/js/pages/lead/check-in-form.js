import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import './../../filters/user-role.filter';
import {getRequestError} from '../../common/helpers';

window['app'] = new Vue({
    el: '#check-in',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
    },
    data: {
        lead: {},
        loading: false,
        leadForm: new Form({
            first_name: window.lead.first_name,
            last_name: window.lead.last_name,
            phone: window.lead.phone,
            email: window.lead.email,
            make: window.lead.make,
            year: window.lead.year,
            model: window.lead.model
        })
    },
    mounted() {
        this.lead = window.lead;
    },
    methods: {
        saveForm: function () {
            this.loading = true;
            this.leadForm
                .post(window.saveCheckInFormUrl)
                .then(() => {
                    this.loading = false;
                    this.$swal({
                        title: 'Lead updated!',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.replace('/');
                    });
                })
                .catch(e => {
                    this.loading = false;
                    window.PmEvent.fire('errors.api', getRequestError(e));
                });
        }
    }
});
