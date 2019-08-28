import Vue from 'vue';
import './../../../common';
import Form from './../../../common/form';
import VueFormWizard from 'vue-form-wizard';
Vue.use(VueFormWizard);
import DatePicker from 'vue2-datepicker';
// import moment from 'moment';
import moment from 'moment-timezone';
import * as Resumable from 'resumablejs/resumable.js';


window['app'] = new Vue({
    el: '#deployments-edit',
    components: {
        'resumable': require('./../../../components/resumable/resumable').default,
        'input-errors': require('./../../../components/input-errors/input-errors').default,
        'spinner-icon': require('./../../../components/spinner-icon/spinner-icon').default,
        DatePicker,
        'editor': require('vue2-ace-editor'),
    },
    directives: {
        'droppable': require('./../../../directives/droppable').default
    },
    data: {
        editImage: false,
        loading: false,
        droppableConfig: {
            browseSelector: 'button',
            targetUrl: window.updateMailerImageUrl
        },
        dropForm: new Form({
            id: null,
            send_at_date: null,
            send_at_time: null,
            send_at: null,
            image_url: null,
            type: null,
            text_message: null,
            email_subject: null,
			email_text: null,
			email_html: null,
        }),
        fileTypes: ['jpeg', 'jpg', 'png', 'bmp', 'gif', 'svg'],
        uploadingImage: false
    },
    computed: {
        image_url: function () {
            return 'backgroundImage: url("'+window.drop.image_url+'")';
        },
    },
    mounted() {
        var utc_tz = moment.tz(window.drop.send_at, 'UTC');
        var local_tz = utc_tz.clone().tz(window.timezone);
        this.dropForm.id = window.drop.id;
        this.dropForm.send_at = window.drop.send_at;
        this.dropForm.status = window.drop.status;
        this.dropForm.type = window.drop.type;
        this.dropForm.text_message = window.drop.text_message;
        this.dropForm.email_subject = window.drop.email_subject;
        this.dropForm.email_text = window.drop.email_text;
        this.dropForm.email_html = (window.drop.email_html ? window.drop.email_html : '');
        this.dropForm.image_url = window.drop.image_url;
        if (window.drop.send_at) {
            Vue.set(this.dropForm, 'send_at_date', local_tz.format('YYYY-MM-DD HH:mm:ss'));
            Vue.set(this.dropForm, 'send_at_time', local_tz.format('YYYY-MM-DD HH:mm:ss'));
        }
    },
    methods: {
        initEditor: function (editor) {
            require('brace/mode/html').default;
            require('brace/theme/chrome').default;
        },
        onFileSelected(event) {
            const data = event.detail;
            this.uploadingImage = true;
            data.resumable.upload();
        },
        onFileSuccess(event) {
            const data = event.detail;
            this.dropForm.image_url = data.message.image_url;
            this.uploadingImage = false;
            this.$toastr.success("Image updated successfully");
        },
        save() {
            let valid = true;
            this.dropForm.errors.clear();
            if (!this.dropForm.send_at_date) {
                valid = false;
                this.dropForm.errors.add('send_at_date', 'This field is required.');
            }
            if (this.dropForm.type !== 'mailer' && !this.dropForm.send_at_time) {
                valid = false;
                this.dropForm.errors.add('send_at_time', 'This field is required.');
            }
            if (!valid) {
                return;
            }
            this.loading = true;
            if (this.dropForm.send_at_time) {
                this.dropForm.send_at_time = moment(this.dropForm.send_at_time).format('HH:mm:ss');
            }
            if (this.dropForm.send_at_date) {
                this.dropForm.send_at_date = moment(this.dropForm.send_at_date).format('YYYY-MM-DD');
            }
            this.dropForm
                .post(window.updateDropUrl)
                .then(() => {
                    this.$swal({
                        title: 'Drop Update!',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.replace(window.dropsIndexUrl);
                    });
                }, e => {
                    window.PmEvent.fire('errors.api', "Unable to process your request");
                    this.loading = false;
                });
            // axios
            //     .post(window.updateDropUrl, data)
            //     .then(response => {
            //         this.loading = false;
            //         this.$swal({
            //             title: 'Drop Update!',
            //             type: 'success',
            //             allowOutsideClick: false
            //         }).then(() => {
            //             window.location.replace(window.dropsIndexUrl);
            //         });
            //     }, error => {
            //         this.loading = false;
            //     });
        }
    }
});

