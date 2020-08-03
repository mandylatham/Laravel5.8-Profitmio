import Vue from 'vue';
import './../../../common';
import Form from './../../../common/form';
import axios from 'axios';
import VueFormWizard from 'vue-form-wizard';
Vue.use(VueFormWizard);
import DatePicker from 'vue2-datepicker';
import moment from 'moment';
import {generateRoute} from "../../../common/helpers";


window['app'] = new Vue({
    el: '#mailer-create',
    components: {
        'resumable': require('./../../../components/resumable/resumable').default,
        'spinner-icon': require('./../../../components/spinner-icon/spinner-icon').default,
        'input-errors': require('./../../../components/input-errors/input-errors').default,
        'date-pick': require('./../../../components/date-pick/date-pick').default,
        DatePicker
    },
    data: {
        loading: false,
        dropForm: new Form({
            send_at: null
        }),
        fileTypes: ['jpeg', 'jpg', 'png', 'bmp', 'gif', 'svg'],
        image: null,
        uploadImageUrl: window.saveMailerDropUrl
    },
    methods: {
        clearError: function (field) {
            this.dropForm.errors.clear(field);
        },
        onFileError() {
            this.loading = false;
            window.PmEvent.fire('errors.api', "Unable to process your request");
        },
        onFileSuccess() {
            this.$swal({
                title: 'Mailer Created!',
                type: 'success',
                allowOutsideClick: false
            }).then(() => {
                window.location.replace(window.dropsIndexUrl);
            });
        },
        onImageSelected(image) {
            this.clearError('image');
            this.image = image;
        },
        saveDrop() {
            let valid = true;
            this.dropForm.errors.clear();
            if (!this.image) {
                valid = false;
                this.dropForm.errors.add('image', 'This field is required.');
            }
            if (!this.dropForm.send_at) {
                valid = false;
                this.dropForm.errors.add('send_at', 'This field is required.');
            }
            if (!valid) {
                return;
            }
            this.loading = true;
            const parseData = moment(this.dropForm.send_at, 'MM/DD/YYYY').format('YYYY-MM-DD');
            this.$refs.resumable.addData('send_at', parseData);
            this.$refs.resumable.startUpload();
        }
    }
});
