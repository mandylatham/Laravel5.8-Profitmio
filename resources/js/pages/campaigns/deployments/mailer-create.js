import Vue from 'vue';
import './../../../common';
import Form from './../../../common/form';
import axios from 'axios';
import VueFormWizard from 'vue-form-wizard';
Vue.use(VueFormWizard);
import DatePicker from 'vue2-datepicker';
import moment from 'moment';


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
            send_at: null,
            image: ''
        }),
        fileTypes: ['jpeg', 'jpg', 'png', 'bmp', 'gif', 'svg'],
        sendAtDateTransformers: {
            value2date: (value) =>{
                return moment(value, 'YYYY-MM-DD').toDate();
            },
            date2value: (date) =>{
                return moment(date).format('YYYY-MM-DD');
            }
        }
    },
    mounted() {
    },
    methods: {
        clearError: function (field) {
            this.dropForm.errors.clear(field);
        },
        onImageSelected(file) {
            this.clearError('image');
            this.dropForm.image = file.file.file;
        },
        saveDrop() {
            let valid = true;
            this.dropForm.errors.clear();
            if (!this.dropForm.image) {
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
            this.dropForm
                .post(window.saveMailerDropUrl)
                .then(() => {
                    this.loading = false;
                    this.$swal({
                        title: 'Mailer Created!',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.replace(window.dropsIndexUrl);
                    });
                }, e => {
                    window.PmEvent.fire('errors.api', "Unable to process your request");
                    this.loading = false;
                });
        }
    }
});
