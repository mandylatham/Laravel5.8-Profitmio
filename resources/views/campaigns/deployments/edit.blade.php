@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/deployments-edit.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.drop = @json($drop);
        window.dropsIndexUrl = @json(route('campaigns.drops.index', ['campaign' => $campaign->id]));
        window.updateDropUrl = @json(route('campaigns.drops.update', ['campaign' => $campaign->id, 'drop' => $drop->id]));
    </script>
    <script src="{{ asset('js/deployments-edit.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="deployments-edit" v-cloak>
        <div class="edit-container">
            <h4 class="mb-4">Drop Details</h4>
            <form @submit.prevent="save">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label for="type">Drop Type</label>
                            <select id="type" name="type" class="form-control" required v-model="drop.type">
                                <option value="email">Email</option>
                                <option value="sms">SMS</option>
                                <option disabled><s>Voice</s></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="form-group">
                            <label for="type">Send Date</label>
                            <date-picker v-model="drop.send_at_date" lang="en" type="date" format="MM/DD/YYYY" placeholder=""></date-picker>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="form-group">
                            <label for="type">Send Time</label>
                            <date-picker v-model="drop.send_at_time" lang="en" type="time" format="HH:mm" placeholder=""></date-picker>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" v-if="drop.type === 'email'">
                        <div class="form-group">
                            <label for="email_subject">Email Subject</label>
                            <input id="email_subject" type="text" class="form-control" name="email_subject"
                                   placeholder="Email Subject" v-model="drop.email_subject"
                                   autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="email_text">Email Text</label>
                            <textarea id="email_text" class="form-control" name="email_text" v-model="drop.email_text"
                                      placeholder="Email Plain Text" autocomplete="off"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="email_html">Email HTML</label>
                            <editor v-model="drop.email_html" lang="html" height="800" @init="initEditor"></editor>
                        </div>
                    </div>
                    <div class="col-12" v-if="drop.type === 'sms'">
                        <div class="form-group">
                            <label for="text_message">Text Message</label>
                            <textarea class="form-control" name="text_message" placeholder="Text Message"
                                      autocomplete="off" v-model="drop.text_message"></textarea>
                        </div>
                        <div class="form-group" v-if="drop.send_vehicle_image">
                            <label for="text_message_image">Vehicle Image</label>
                            <input type="text" name="text_message_image" class="form-control" placeholder="Vehicle Image Location" v-model="drop.text_message_image">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn pm-btn pm-btn-purple mt-3" :disabled="loading">
                    <span v-if="!loading">Save</span>
                    <spinner-icon :size="'sm'" class="white" v-if="loading"></spinner-icon>
                </button>
            </form>
        </div>
    </div>
@endsection
