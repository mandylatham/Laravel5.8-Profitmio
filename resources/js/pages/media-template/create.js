import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
import axios from 'axios';

// Toastr Library
import VueToastr2 from 'vue-toastr-2';
window.toastr = require('toastr');
Vue.use(VueToastr2);

// Bootstrap Vue
import {filter} from 'lodash';
import Button from 'bootstrap-vue';
import InputGroup from 'bootstrap-vue';
import FormInput from 'bootstrap-vue';
Vue.use(Button);
Vue.use(InputGroup);
Vue.use(FormInput);

// Ace Editor
import Vue2AceEditor from 'vue2-ace-editor';
Vue.use(Vue2AceEditor);

window['app'] = new Vue({
    el: '#template-create',
    components: {
        editor: require('vue2-ace-editor'),
    },
    computed: {
        readyToSubmitForm: function () {
            return this.oldTemplate.name && this.oldTemplate.name.length > 0 &&
            (
                (
                    this.oldTemplate.type == 'email' &&
                    this.oldTemplate.email_subject.length > 0 &&
                    this.oldTemplate.email_text.length > 0 &&
                    this.oldTemplate.email_html.length > 0
                ) || (
                    this.oldTemplate.type == 'sms' &&
                    this.oldTemplate.text_message.length > 0
                )
            );
        }
    },
    data: {
        createUrl: '',
        indexUrl: '',
        template: {
            name: '',
            type: '',
            text_message: '',
            email_subject: '',
            email_text: '',
            email_html: window.emailTemplate || ''
        },
        oldTemplate: {},
        renderedTemplate: '',

        showNameControls: true,
        showTextMessageControls: true,
        showEmailSubjectControls: true,
        showEmailTextControls: true,
        showEmailHtmlControls: !!window.emailTemplate,

        createForm: new Form({
            name: '',
            type: '',
            text_message: '',
            email_subject: '',
            email_text: '',
            email_html: '',
        }),
    },
    mounted() {
        this.createUrl = window.createUrl;
        this.indexUrl = window.indexUrl;
        this.oldTemplate = JSON.parse(JSON.stringify(this.template));
        this.renderedTemplate = JSON.parse(JSON.stringify(this.oldTemplate));
        this.renderedTemplate.text_message = this.htmlify(this.oldTemplate.text_message);
        this.renderedTemplate.email_subject = this.htmlify(this.oldTemplate.email_subject);
        this.renderedTemplate.email_text = this.htmlify(this.oldTemplate.email_text);
        this.renderedTemplate.email_html = this.htmlify(this.oldTemplate.email_html);

        this.createForm.name = this.oldTemplate.name;
        this.createForm.text_message = this.oldTemplate.text_message;
        this.createForm.email_subject = this.oldTemplate.email_subject;
        this.createForm.email_text = this.oldTemplate.email_text;
        this.createForm.email_html = this.oldTemplate.email_html;

        if (window.templateType) {
            this.selectType(window.templateType);
        }
    },
    methods: {
        selectType: function (value) {
            this.template.type = value;
            this.oldTemplate.type = value;
            this.createForm.type = value;
        },
        updateFields: function () {
            // update the form
            this.createForm.name = this.oldTemplate.name;
            this.createForm.text_message = this.oldTemplate.text_message;
            this.createForm.email_subject = this.oldTemplate.email_subject;
            this.createForm.email_text = this.oldTemplate.email_text;
            this.createForm.email_html = this.oldTemplate.email_html;

            // style the previews
            this.renderedTemplate.text_message = this.htmlify(this.oldTemplate.text_message);
            this.renderedTemplate.email_subject = this.htmlify(this.oldTemplate.email_subject);
            this.renderedTemplate.email_text = this.htmlify(this.oldTemplate.email_text);
            this.renderedTemplate.email_html = this.htmlify(this.oldTemplate.email_html);
        },
        initEditor: function (editor) {
            require('brace/mode/html').default;
            require('brace/theme/chrome').default;
        },
        htmlify: function (value) {
            if (value === undefined || value === null || value.length === 0) return;

            let replacableValues = {
                first_name: 'John',
                last_name: 'Doe',
                email: 'test@example.com',
                phone: '555-555-5555',
                year: '2017',
                make: 'Toyota',
                model: 'Prius',
            };

            for (var key in replacableValues) {
                var reggie = new RegExp('\\{\\{\\s*' + key + '\\s*\\}\\}', 'g');
                var tag = '<button class="btn btn-sm btn-primary variable-button">' + replacableValues[key] + '</button>';
                value = value.replace(reggie, tag);
            }

            return value;
        },
        toRoyalCase: function (data) {
            var royal = '';
            var parts = data.split('_');
            for (var key in parts) {
                royal += parts[key].charAt(0).toUpperCase() + parts[key].slice(1);
            }
            return royal;
        },
        saveField: function (fieldName) {
            this.oldTemplate[fieldName] = this.template[fieldName];
            this.updateFields();
            var controlName = 'show' + this.toRoyalCase(fieldName) + 'Controls';
            this[controlName] = false;
        },
        cancelField: function (fieldName) {
            var controlName = 'show' + this.toRoyalCase(fieldName) + 'Controls';
            this[controlName] = false;
        },
        onSubmit: function (redirect=false) {
            this.createForm.post(createUrl)
                .then(response => {
                    this.template = {
                        name: '',
                        type: '',
                        text_message: '',
                        email_subject: '',
                        email_text: '',
                        email_html: ''
                    };
                    this.oldTemplate = JSON.parse(JSON.stringify(this.template));
                    this.renderedTemplate = JSON.parse(JSON.stringify(this.oldTemplate));
                    if (redirect === true) {
                        this.$toastr.success("Template created, you will be redirected shortly");
                        setTimeout(function() { window.location.href = indexUrl; }, 800);
                    } else {
                        this.$toastr.success("Template created");
                    }
                })
                .catch(error => {
                    console.log('error', error);
                    window.PmEvent.fire('errors.api', "Unable to save");
                });
        }
    }
});
