
@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/media-template-create.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.createUrl = @json(route('template.create'));
        window.indexUrl = @json(route('template.index'));
        window.templateType = @json(Request::get('type'));
        window.emailTemplate = @json(session('email_html'));
    </script>
    <script src="{{ asset('js/media-template-create.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="template-create" v-cloak>
        <div class="row no-gutters mt-3">
            <div class="col-12 no-gutters">
                <a href="{{ route('template.index') }}" class="pm-btn mb-4">
                    <i class="fas fa-chevron-circle-left mr-1"></i>
                    Back
                </a>
               <div class="card">
                   <div class="card-header">Create new template</div>
                    <div class="card-body p-5">
                        <div class="row mb-4" v-if="template.type != 'email' && template.type != 'sms'">
                            <div class="col-12">
                                <div class="type-buttons-container">
                                    <button class="btn pm-btn pm-btn-outline-purple mr-2 pl-4 pr-4 pt-2 pb-2" @click="selectType('sms')">
                                        <i class="fa fa-comment mr-2"></i>
                                        SMS
                                    </button>
                                    <button class="btn pm-btn pm-btn-outline-purple pl-4 pr-4 pt-2 pb-2" @click="selectType('email')">
                                        <i class="fa fa-envelope mr-2"></i>
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
        </div>
    </div>
@endsection
