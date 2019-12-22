import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
import axios from 'axios';
import {filter} from 'lodash';

// Toastr Library
import VueToastr2 from 'vue-toastr-2'
window.toastr = require('toastr');
Vue.use(VueToastr2);

// Bootstrap-Vue
import Button from 'bootstrap-vue';
import InputGroup from 'bootstrap-vue';
import FormInput from 'bootstrap-vue';
Vue.use(Button);
Vue.use(InputGroup);
Vue.use(FormInput);

// Ave Editor
import Vue2AceEditor from 'vue2-ace-editor';
Vue.use(Vue2AceEditor);

window['app'] = new Vue({
    el: '#template-details',
    components: {
        'editor': require('vue2-ace-editor').default,
    },
    computed: {
    },
    data: {
        template: {},
        toggleInputs: false,
        toggleNameInput: false,
        updateUrl: '',
        deleteUrl: '',
        showNameControls: false,
        showTextMessageControls: false,
        showEmailSubjectControls: false,
        showEmailTextControls: false,
        showEmailHtmlControls: false,

        templateEdit: '',
        templateDelete: '',
        nameInput: false,
        textMessageInput: false,
        emailSubjectInput: false,
        emailTextInput: false,
        emailHtmlInput: false,
        oldTemplate: '',
        renderedTemplate: '',
        updateForm: new Form({
            name: '',
            text_message: '',
            email_subject: '',
            email_text: '',
            email_html: '',
        }),
    },
    mounted() {
        this.updateUrl = window.updateUrl;
        this.deleteUrl = window.deleteUrl;
        this.template = window.template;

        this.oldTemplate = JSON.parse(JSON.stringify(template));
        this.renderedTemplate = JSON.parse(JSON.stringify(this.oldTemplate));

        this.renderedTemplate.text_message = this.htmlify(this.oldTemplate.text_message);
        this.renderedTemplate.email_subject = this.htmlify(this.oldTemplate.email_subject);
        this.renderedTemplate.email_text = this.htmlify(this.oldTemplate.email_text);
        this.renderedTemplate.email_html = this.htmlify(this.oldTemplate.email_html);

        this.updateForm.name = this.oldTemplate.name;
        this.updateForm.text_message = this.oldTemplate.text_message;
        this.updateForm.email_subject = this.oldTemplate.email_subject;
        this.updateForm.email_text = this.oldTemplate.email_text;
        this.updateForm.email_html = this.oldTemplate.email_html;
    },
    methods: {
        updateFields: function () {
            // update the form
            this.updateForm.name = this.oldTemplate.name;
            this.updateForm.text_message = this.oldTemplate.text_message;
            this.updateForm.email_subject = this.oldTemplate.email_subject;
            this.updateForm.email_text = this.oldTemplate.email_text;
            this.updateForm.email_html = this.oldTemplate.email_html;
        },
        htmlifyFields: function () {
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
            this.update(fieldName);
        },
        cancelField: function (fieldName) {
            var controlName = 'show' + this.toRoyalCase(fieldName) + 'Controls';
            this[controlName] = false;
        },
        update: function (fieldName) {
            this.updateFields();
            console.log(updateUrl);
            this.updateForm.patch(updateUrl)
                .then(response => {
                    this.oldTemplate = JSON.parse(JSON.stringify(this.template));
                    var controlName = 'show' + this.toRoyalCase(fieldName) + 'Controls';
                    this[controlName] = false;
                    this.$toastr.success("Update successful");
                    this.htmlifyFields();
                })
                .catch(error => {
                    window.PmEvent.fire('errors.api', "Unable to update");
                });
        }
    }
});
