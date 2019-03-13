@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/deployments-create.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchRecipientsUrl = @json(route('campaigns.recipients.search', ['campaign' => $campaign->id]));
        window.getTemplateJsonUrl = @json(route('templates.show-json', ['template' => ':templateId']));
        window.createCampaignUrl = @json(route('campaigns.drops.store', ['campaign' => $campaign->id]));
        window.addGroupsUrl = @json(route('campaigns.drops.add-groups', ['campaign' => $campaign->id]));
        window.dropsIndexUrl = @json(route('campaigns.drops.index', ['campaign' => $campaign->id]));
        window.templates = @json($templates);
    </script>
    <script src="{{ asset('js/deployments-create.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="deployments-create" v-cloak>
        <div class="wizard-container">
            <div class="table-loader-spinner" v-if="showGlobalLoader">
                <spinner-icon></spinner-icon>
            </div>
            <form-wizard :title="''" :subtitle="''" :step-size="'sm'" :color="'#572E8D'" @on-complete="save">
                <tab-content title="Add Recipients" icon="pm-font-recipients-icon">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="total text-center">
                                <div class="total-label">Recipient Count</div>
                                <div class="total-value">@{{ totalRecipients }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="total text-center">
                                <div class="total-label">Group Count</div>
                                <div class="total-value">@{{ groups.length }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="contact_method">Contact Method</label>
                                <v-select dusk="contact-method-select" name="contact_method" :options="contactMethods" class="filter--v-select" v-model="searchFilters.contact_method" @input="fetchRecipientsGroup"></v-select>
                            </div>
                            <div class="form-group">
                                <label for="data_source_conquest">Data Source</label>
                                <p-check dusk="data-source-conquest-check" color="primary" class="p-default d-block mb-3" name="data_source_conquest" v-model="searchFilters.data_source_conquest" @change="fetchRecipientsGroup">Conquest</p-check>
                                <p-check dusk="data-source-database-check" color="primary" class="p-default d-block mb-3" name="data_source_database" v-model="searchFilters.data_source_database" @change="fetchRecipientsGroup">Database</p-check>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="recipients">Recipient List</label>
                                <select dusk="recipient-list-select" class="form-control" name="recipients" id="recipients" multiple v-model="searchFilters.recipients" @change="fetchRecipientsGroup">
                                    <option value="all">All</option>
                                    @foreach ($recipientLists as $list)
                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="max" >Max Per Group</label>
                                <input dusk="max-per-group-field" type="number" min="0" class="form-control" name="max" required placeholder="Max per Groups" v-model="searchFilters.max" @input="fetchRecipientsGroup">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 col-lg-8">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th>Group</th>
                                    <th>Count</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="group in groups">
                                    <td>@{{ group.name }}</td>
                                    <td>@{{ group.count }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </tab-content>
                <tab-content title="Media" icon="far fa-image">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="template">Use a Template</label>
                                <v-select name="template" :label="'name'" :options="templates" class="filter--v-select" v-model="templateData.template" @input="fetchTemplate"></v-select>
                            </div>
                            <div class="form-group">
                                <label for="typ">Drop Type</label>
                                <select dusk="drop-type-select" id="type" name="type" class="form-control" required v-model="templateData.type">
                                    <option value="email">Email</option>
                                    <option value="sms">SMS</option>
                                    <option disabled><s>Voice</s></option>
                                </select>
                            </div>
                            <div class="email-fields" v-if="templateData.type == 'email'">
                                <div class="form-group">
                                    <label for="email_subject">Email Subject</label>
                                    <input dusk="drop-email-subject-input" id="email_subject" type="text" class="form-control" name="email_subject"
                                           placeholder="Email Subject" v-model="templateData.email_subject"
                                           autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="email_text">Email Text</label>
                                    <textarea dusk="drop-email-text-input" id="email_text" class="form-control" name="email_text" v-model="templateData.email_text"
                                              placeholder="Email Plain Text" autocomplete="off"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="email_html">Email HTML</label>
                                    <editor class="drop-email-html" v-model="templateData.email_html" lang="html" height="800" @init="initEditor"></editor>
                                </div>
                            </div>
                            <div id="sms-fields" v-if="templateData.type == 'sms'">
                                <div class="form-group">
                                    <label for="text_message">Text Message</label>
                                    <textarea dusk="drop-text-message-input" class="form-control" name="text_message" placeholder="Text Message"
                                              autocomplete="off" v-model="templateData.text_message"></textarea>
                                </div>
                                <div class="form-group" v-if="templateData.send_vehicle_image">
                                    <label for="text_message_image">Vehicle Image</label>
                                    <input type="text" name="text_message_image" class="form-control" placeholder="Vehicle Image Location" v-model="templateData.text_message_image">
                                </div>
                            </div>
                        </div>
                    </div>
                </tab-content>
                <tab-content title="Schedule" icon="far fa-calendar-alt">
                    <table class="table table-bordered table-sm schedule-time-table">
                        <thead>
                            <tr>
                                <th>Group</th>
                                <th>Recipients</th>
                                <th>Date / Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="time-row" v-for="(group, index) in groups">
                                <td class="align-middle">@{{ group.name }}</td>
                                <td class="align-middle">@{{ group.count }}</td>
                                <td class="align-middle">
                                    <date-picker :dusk="'group-datetime-' + index" v-model="group.datetime" lang="en" type="datetime" format="MM/DD/YYYY [at] HH:mm"></date-picker>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </tab-content>
                <template slot="finish">
                    <button type="button" class="wizard-btn" :disabled="loading">
                        <span v-if="!loading">Finish</span>
                        <spinner-icon :size="'sm'" class="white" v-if="loading"></spinner-icon>
                    </button>
                </template>
            </form-wizard>
        </div>
    </div>
@endsection
