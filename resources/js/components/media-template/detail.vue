<template>
<div>
    <div class="card">
        <div class="card-body p-5">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 v-if="!nameInput" @click="toggleNameInput()" class="editable template-name">{{ template.name }}</h1>
                    <div v-if="nameInput">
                        <div class="form-group mb-1">
                            <h1><input name="name" class="form-control" v-model="template.name" aria-label="Template Name" aria-describedby="save-name-button"></h1>
                        </div>
                        <div class="form-group">
                            <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveNameField()">
                                Save
                            </button>
                            <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelNameField()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4" v-if="template.type == 'sms'">
                <div class="col-12">
                    <strong>Text Message</strong>
                </div>
                <div class="col-6">
                    <div class="box">
                        <p v-if="!textMessageInput" @click="toggleTextMessageInput()" class="editable">{{ template.text_message }}</p>
                        <div v-if="textMessageInput">
                            <div class="form-group mb-1">
                                <p><textarea name="text_message" class="form-control" v-model="template.text_message"></textarea></p>
                            </div>
                            <div class="form-group">
                                <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveTextMessageField()">
                                    Save
                                </button>
                                <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelTextMessageField()">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card card-primary">
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
                        <p v-if="!emailSubjectInput" @click="toggleEmailSubjectInput()" class="editable">{{ template.email_subject }}</p>
                        <div v-if="emailSubjectInput">
                            <div class="form-group mb-1">
                                <p><textarea name="email_subject" class="form-control" v-model="template.email_subject"></textarea></p>
                            </div>
                            <div class="form-group">
                                <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveEmailSubjectField()">
                                    Save
                                </button>
                                <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelEmailSubjectField()">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                        <div class="card-header"><p>Email Subject Preview</p></div>
                    <div class="card card-primary">
                        <div class="card-body" v-html="renderedTemplate.email_subject"></div>
                    </div>
                </div>
            </div>
            <div class="row mb-4" v-if="template.type == 'email'">
                <div class="col-12">
                    <strong>Email Short-Text</strong>
                </div>
                <div class="col-12 col-md-6">
                    <div class="box">
                        <p v-if="!emailTextInput" @click="toggleEmailTextInput()" class="editable">{{ template.email_text }}</p>
                        <div v-if="emailTextInput">
                            <div class="form-group mb-1">
                                <p><textarea name="email_text" class="form-control" v-model="template.email_text"></textarea></p>
                            </div>
                            <div class="form-group">
                                <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveEmailTextField()">
                                    Save
                                </button>
                                <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelEmailTextField()">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card card-primary">
                        <div class="card-header"><p>Email Short-Text Preview</p></div>
                        <div class="card-body" v-html="renderedTemplate.email_text"></div>
                    </div>
                </div>
            </div>
            <div class="row mb-4" v-if="template.type == 'email'">
                <div class="col-12">
                    <strong>Email HTML</strong>
                </div>
                <div class="col-12 col-md-6 mb-4">
                    <button class="btn btn-outline-info" v-if="!emailHtmlInput" @click="emailHtmlInput = !emailHtmlInput">Edit HTML</button>
                    <div v-if="emailHtmlInput">
                        <div class="form-group mb-1">
                            <editor v-model="template.email_html" lang="html" height="500" @init="initEditor"></editor>
                        </div>
                        <div class="form-group">
                            <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveEmailHtmlField()">
                                Save
                            </button>
                            <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelEmailHtmlField()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card card-primary email-preview">
                        <div class="card-header"><p>Email HTML Preview</p></div>
                        <div class="card-body" v-html="renderedTemplate.email_html"></div>
                    </div>
                </div>
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
            template: {
                type: Object,
                required: true,
                default: function () {
                    return {};
                }
            },
            url: {
                type: String,
                required: true,
                default: function () {
                    return '';
                }
            }
        },
        data() {
            return {
                mediaTemplateClosed: true,
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
            };
        },
        mounted: function () {
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
                this.updateForm.name = this.oldTemplate.name;
                this.updateForm.text_message = this.oldTemplate.text_message;
                this.updateForm.email_subject = this.oldTemplate.email_subject;
                this.updateForm.email_text = this.oldTemplate.email_text;
                this.updateForm.email_html = this.oldTemplate.email_html;
            },
            initEditor: function (editor) {
                require('brace/mode/html');
                require('brace/theme/chrome');
            },
            htmlify: function (value) {
                if (value === null) return;
                if (value.length === 0) return;
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
            toggleNameInput: function () {
                this.nameInput = !this.nameInput;
            },
            toggleTextMessageInput: function () {
                this.textMessageInput = !this.textMessageInput;
            },
            toggleEmailSubjectInput: function () {
                this.emailSubjectInput = !this.emailSubjectInput;
            },
            toggleEmailTextInput: function () {
                this.emailTextInput = !this.emailTextInput;
            },
            toggleEmailHtmlInput: function () {
                this.emailHtmlInput = !this.emailHtmlInput;
            },
            saveNameField: function () {
                this.oldTemplate = JSON.parse(JSON.stringify(template));
                this.updateFields();
                this.updateForm.post(this.url)
                    .then(response => {
                        this.renderedTemplate.text_message = this.htmlify(this.oldTemplate.text_message);
                        this.toggleNameInput();
                        this.$toastr.success("Update successful");
                    })
                    .catch(error => {
                        this.$toastr.error("Unable to save");
                    });
            },
            cancelNameField: function () {
                this.template.name = this.oldTemplate.name;
                this.toggleNameInput();
            },
            saveTextMessageField: function () {
                this.oldTemplate.text_message = JSON.parse(JSON.stringify(template.text_message));
                this.updateFields();
                this.updateForm.post(this.url)
                    .then(response => {
                        this.renderedTemplate.text_message = this.htmlify(this.oldTemplate.text_message);
                        this.toggleTextMessageInput();
                        this.$toastr.success("Update successful");
                    })
                    .catch(error => {
                        this.$toastr.error("Unable to save");
                    });
            },
            cancelTextMessageField: function () {
                this.template.text_message = this.oldTemplate.text_message;
                this.toggleTextMessageInput();
            },
            saveEmailSubjectField: function () {
                this.oldTemplate.email_subject = JSON.parse(JSON.stringify(template.email_subject));
                this.updateFields();
                this.updateForm.post(this.url)
                    .then(response => {
                        this.renderedTemplate.email_subject = this.htmlify(this.oldTemplate.email_subject);
                        this.toggleEmailSubjectInput();
                        this.$toastr.success("Update successful");
                    })
                    .catch(error => {
                        this.$toastr.error("Unable to save");
                    });
            },
            cancelEmailSubjectField: function () {
                this.template.email_subject = this.oldTemplate.email_subject;
                this.toggleEmailSubjectInput();
            },
            saveEmailTextField: function () {
                this.oldTemplate.email_text = JSON.parse(JSON.stringify(template.email_text));
                this.updateFields();
                this.updateForm.post(this.url)
                    .then(response => {
                        this.renderedTemplate.email_text = this.htmlify(this.oldTemplate.email_text);
                        this.toggleEmailTextInput();
                        this.$toastr.success("Update successful");
                    })
                    .catch(error => {
                        this.$toastr.error("Unable to save");
                    });
            },
            cancelEmailTextField: function () {
                this.template.email_text = this.oldTemplate.email_text;
                this.toggleEmailTextInput();
            },
            saveEmailHtmlField: function () {
                this.oldTemplate.email_html = JSON.parse(JSON.stringify(template.email_html));
                this.updateFields();
                this.updateForm.post(this.url)
                    .then(response => {
                        this.renderedTemplate.email_html = this.htmlify(this.oldTemplate.email_html);
                        this.toggleEmailHtmlInput();
                        this.$toastr.success("Update successful");
                    })
                    .catch(error => {
                        this.$toastr.error("Unable to save");
                    });
            },
            cancelEmailHtmlField: function () {
                this.template.email_html = this.oldTemplate.email_html;
                this.toggleEmailHtmlInput();
            },
            generateRoute
        }
    }
</script>
