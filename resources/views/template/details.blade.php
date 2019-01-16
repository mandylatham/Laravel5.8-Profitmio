@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/media-template-details.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.updateUrl = "{{ route('template.update', ['template' => $template->id]) }}";
        window.deleteUrl = "{{ route('template.delete', ['template' => $template->id]) }}";
        window.template = @json($template);
    </script>
    <script src="{{ asset('js/media-template-details.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="template-details">
        <div class="row no-gutters mt-3">
            <div class="col-12 no-gutters">
                <a href="{{ route('template.index') }}" class="btn btn-outline-primary mb-4">
                    <i class="fas fa-chevron-left mr-1"></i>
                    Back
                </a>
                <div class="card">
                    <div class="card-body p-5">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h1 v-if="!showNameControls" @click="showNameControls = true" class="editable template-name">@{{ template.name }}</h1>
                                <div v-if="showNameControls">
                                    <div class="form-group mb-1">
                                        <h1><input name="name" class="form-control" v-model="template.name" aria-label="Template Name" aria-describedby="save-name-button"
                                            @click="showNameControls = true"></h1>
                                    </div>
                                    <div class="form-group" v-if="showNameControls">
                                        <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveField('name')">
                                            Save
                                        </button>
                                        <button id="cancel-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelField('name')">
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
                                    <p v-if="!showTextMessageControls" @click="showTextMessageControls = true" class="editable">@{{ template.text_message }}</p>
                                    <div v-if="showTextMessageControls">
                                        <div class="form-group mb-1">
                                            <p><textarea name="text_message" class="form-control" v-model="template.text_message"></textarea></p>
                                        </div>
                                        <div class="form-group">
                                            <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveField('text_message')">
                                                Save
                                            </button>
                                            <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelField('text_message')">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card card-primary">
                                    <div class="card-header">Text Message Preview</div>
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
                                    <p v-if="!showEmailSubjectControls" @click="showEmailSubjectControls = true" class="editable">@{{ template.email_subject }}</p>
                                    <div v-if="showEmailSubjectControls">
                                        <div class="form-group mb-1">
                                            <p><textarea name="email_subject" class="form-control" v-model="template.email_subject"></textarea></p>
                                        </div>
                                        <div class="form-group">
                                            <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveField('email_subject')">
                                                Save
                                            </button>
                                            <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelField('email_subject')">
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
                                    <p v-if="!showEmailTextControls" @click="showEmailTextControls = true" class="editable">@{{ template.email_text }}</p>
                                    <div v-if="showEmailTextControls">
                                        <div class="form-group mb-1">
                                            <p><textarea name="email_text" class="form-control" v-model="template.email_text"></textarea></p>
                                        </div>
                                        <div class="form-group">
                                            <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveField('email_text')">
                                                Save
                                            </button>
                                            <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelField('email_text')">
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
                                <button class="btn btn-outline-info" v-if="!showEmailHtmlControls" @click="showEmailHtmlControls = true">Edit HTML</button>
                                <div v-if="showEmailHtmlControls">
                                    <div class="form-group mb-1">
                                        <editor v-model="template.email_html" lang="html" height="500" @init="initEditor"></editor>
                                    </div>
                                    <div class="form-group">
                                        <button id="save-name-button" class="btn btn-sm btn-outline-primary mr-1" type="button" @click="saveField('email_html')">
                                            Save
                                        </button>
                                        <button id="save-name-button" class="btn btn-sm btn-outline-secondary" type="button" @click="cancelField('email_html')">
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
        </div>
    </div>
@endsection
