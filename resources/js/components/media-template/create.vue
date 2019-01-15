<template>
<div>
    <div class="card">
        <div class="card-body p-5">
            <div class="row">
                <div class="col-12 mb-4">
                    <h1>Create new template</h1>
                </div>
            </div>
            <div class="row mb-4" v-if="template.type != 'email' && template.type != 'sms'">
                <div class="col-12">
                    <div class="type-buttons-container">
                        <button class="btn btn-outline-primary type-buttons" @click="selectType('sms')">
                            <i class="fa fa-comment"></i>
                            SMS
                        </button>
                        <button class="btn btn-outline-primary type-buttons" @click="selectType('email')">
                            <i class="fa fa-envelope"></i>
                            Email
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mb-4" v-if="template.type == 'email' || template.type == 'sms'">
                <div class="col-12">
                    <div>
                        <div class="form-group mb-1">
                            <h1>
                                <input name="name" class="form-control" v-model="template.name" 
                                       aria-label="Template Name" aria-describedby="save-name-button"
                                       placeholder="Template Name" @keyup="showNameControls = true">
                            </h1>
                        </div>
                        <div class="form-group" v-if="showNameControls">
                            <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveField('name')">
                                Preview
                            </button>
                            <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelField('name')">
                                Undo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4" v-if="template.type == 'sms'">
                <div class="col-12">
                    <strong>Text Message</strong>
                </div>
                <div class="col-12 col-md-6">
                    <div class="box">
                        <div>
                            <div class="form-group mb-1">
                                <p><textarea name="text_message" class="form-control" v-model="template.text_message" @keyup="showTextMessageControls = true"></textarea></p>
                            </div>
                            <div class="form-group" v-if="showTextMessageControls">
                                <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveField('text_message')">
                                    Preview
                                </button>
                                <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelField('text_message')">
                                    Undo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card card-primary preview-window">
                        <div class="card-header"><p>Text Message Preview</p></div>
                        <div class="card-body" v-html="renderedTemplate.text_message"></div>
                    </div>
                </div>
            </div>
            <div class="row mb-4" v-if="template.type == 'email'">
                <div class="col-12">
                    <strong>Email Subject</strong>
                </div>
                <div class="col-12 col-md-6">
                    <div class="box">
                        <div>
                            <div class="form-group mb-1">
                                <p><textarea name="email_subject" class="form-control" v-model="template.email_subject" @keyup="showEmailSubjectControls = true"></textarea></p>
                            </div>
                            <div class="form-group" v-if="showEmailSubjectControls">
                                <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveField('email_subject')">
                                    Preview
                                </button>
                                <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelField('email_subject')">
                                    Undo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card card-primary preview-window">
                        <div class="card-header"><p>Email Subject Preview</p></div>
                        <div class="card-body" v-html="renderedTemplate.email_subject"></div>
                    </div>
                </div>
            </div>
            <div class="row mb-4" v-if="template.type == 'email'">
                <div class="col-12 pb-3">
                    <strong>Email Short-Text</strong>
                </div>
                <div class="col-12 col-md-6">
                    <div class="box">
                        <div>
                            <div class="form-group mb-1">
                                <p><textarea name="email_text" class="form-control" v-model="template.email_text" @keyup="showEmailTextControls = true"></textarea></p>
                            </div>
                            <div class="form-group" v-if="showEmailTextControls">
                                <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveField('email_text')">
                                    Preview
                                </button>
                                <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelField('email_text')">
                                    Undo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card card-primary preview-window">
                        <div class="card-header"><p>Email Short-Text Preview</p></div>
                        <div class="card-body" v-html="renderedTemplate.email_text"></div>
                    </div>
                </div>
            </div>
            <div class="row mb-4" v-if="template.type == 'email'">
                <div class="col-12 mb-4">
                    <strong>Email HTML</strong>
                </div>
                <div class="col-12 col-md-6">
                    <button class="btn btn-outline-info" v-if="!showEmailHtmlControls" @click="showEmailHtmlControls = !showEmailHtmlControls">Edit HTML</button>
                    <div v-if="showEmailHtmlControls">
                        <div class="form-group mb-1">
                            <editor v-model="template.email_html" lang="html" height="500" @init="initEditor"></editor>
                        </div>
                        <div class="form-group">
                            <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveField('email_html')">
                                Preview
                            </button>
                            <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelField('email_html')">
                                Undo
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card card-primary preview-window email-preview">
                        <div class="card-header"><p>Email HTML Preview</p></div>
                        <div class="card-body" v-html="renderedTemplate.email_html"></div>
                    </div>
                </div>
            </div>
            <div class="row mb-4" v-if="template.type == 'email' || template.type == 'sms'">
                <button class="btn btn-primary mr-2" :disabled="this.readyToSubmitForm == false" @click="onSubmit(true)">Create</button>
                <button class="btn btn-secondary" :disabled="this.readyToSubmitForm == false" @click="onSubmit()">Create and add another</button>
            </div>
        </div>
    </div>
</div>
</template>
<script>
    import moment from 'moment';
    import {generateRoute} from './../../common/helpers';
    import Vue2AceEditor from 'vue2-ace-editor';
    import Form from './../../common/form';

    export default {
        components: {
            editor: require('vue2-ace-editor'),
        },
        props: {
            createUrl: {
                type: String,
                required: true,
                default: function () {
                    return '';
                }
            },
            indexUrl: {
                type: String,
                required: true,
                default: function () {
                    return '';
                }
            }
        },
        data() {
            return {
                template: {
                    name: '',
                    type: '',
                    text_message: '',
                    email_subject: '',
                    email_text: '',
                    email_html: ''
                },
                oldTemplate: '',
                renderedTemplate: '',

                showNameControls: true,
                showTextMessageControls: true,
                showEmailSubjectControls: true,
                showEmailTextControls: true,
                showEmailHtmlControls: false,

                createForm: new Form({
                    name: '',
                    type: '',
                    text_message: '',
                    email_subject: '',
                    email_text: '',
                    email_html: '',
                }),
            };
        },
        computed: {
            readyToSubmitForm: function () {
                return this.oldTemplate.name.length > 0 && 
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
        mounted: function () {
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
                require('brace/mode/html');
                require('brace/theme/chrome');
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
                        this.$toastr.error("Unable to save");
                    });
            },
            generateRoute
        }
    }
</script>
