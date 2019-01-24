@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/campaigns-edit.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.agencies = @json($agencies);
        window.dealerships = @json($dealerships);
        window.campaign = @json($campaign);
        window.agencySelected = @json($campaign->agency);
        window.dealershipSelected = @json($campaign->dealership);
        window.saveCampaignUrl = @json(route('campaigns.update', ['campaign' => $campaign->id]));
        window.campaignStatsUrl = @json(route('campaigns.stats', ['campaign' => $campaign->id]));
        console.log(window.campaign);
    </script>
    <script src="{{ asset('js/campaigns-edit.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="campaigns-edit" v-cloak>
        <div class="row">
            <div class="col-12 col-lg-10 offset-lg-1 wizard-container">
                <form-wizard :title="''" :subtitle="''" :step-size="'sm'" :color="'#572E8D'" @on-complete="saveCampaign">
                        <tab-content title="Basics" icon="fas fa-list-ul" :before-change="validateBasicTab">
                            <div class="form-group">
                                <label for="name">Campaign Name</label>
                                <input type="text" class="form-control" name="name" required v-model="campaignForm.name" @change="clearError(campaignForm, 'name')" :class="{'is-invalid': campaignForm.errors.has('name')}">
                                <input-errors :error-bag="campaignForm.errors" :field="'name'"></input-errors>
                            </div>
                            <div class="form-group">
                                <label for="order">Order #</label>
                                <input type="text" class="form-control" name="order" autocomplete="off" v-model="campaignForm.order" @change="clearError(campaignForm, 'order')" :class="{'is-invalid': campaignForm.errors.has('order')}">
                                <input-errors :error-bag="campaignForm.errors" :field="'order'"></input-errors>
                            </div>
                            <div class="form-group">
                                <label for="name">Status</label>
                                <select name="status" class="form-control" v-model="campaignForm.status">
                                    <option value="Active">Active</option>
                                    <option value="Archived">Archived</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Upcoming">Upcoming</option>
                                </select>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label for="start">Starts on</label>
                                    <date-pick :display-format="'MM/DD/YYYY'" v-model="campaignForm.start" :has-input-element="true" :input-attributes="datePickInputClasses" @input="clearError(campaignForm, 'start')" :class="{'is-invalid': campaignForm.errors.has('start')}"></date-pick>
                                    <input-errors :error-bag="campaignForm.errors" :field="'start'"></input-errors>
                                </div>
                                <div class="form-group col-6">
                                    <label for="end">Ends on</label>
                                    <date-pick name="end" :display-format="'MM/DD/YYYY'" v-model="campaignForm.end" :has-input-element="true" :input-attributes="datePickInputClasses" @input="clearError(campaignForm, 'end')" :class="{'is-invalid': campaignForm.errors.has('end')}"></date-pick>
                                    <input-errors :error-bag="campaignForm.errors" :field="'end'"></input-errors>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label for="expires">Expires on</label>
                                    <date-pick name="expires" :display-format="'MM/DD/YYYY'" v-model="campaignForm.expires" :has-input-element="true" :input-attributes="datePickInputClasses" @input="clearError(campaignForm, 'expires')" :class="{'is-invalid': campaignForm.errors.has('expires')}"></date-pick>
                                    <input-errors :error-bag="campaignForm.errors" :field="'expires'"></input-errors>
                                </div>
                            </div>
                        </tab-content>
                        <tab-content title="Accounts" icon="fas fa-user" :before-change="validateAccountsTab">
                            <div class="form-row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="agency">Agency</label>
                                        <v-select :options="agencies" label="name" v-model="agencySelected" class="filter--v-select" @input="clearError(campaignForm, 'agency')" :class="{'is-invalid': campaignForm.errors.has('agency')}"></v-select>
                                        <input-errors :error-bag="campaignForm.errors" :field="'agency'"></input-errors>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="Dealership">Dealership</label>
                                        <v-select :options="dealerships" label="name" v-model="dealershipSelected" class="filter--v-select" @input="clearError(campaignForm, 'dealership')" :class="{'is-invalid': campaignForm.errors.has('dealership')}"></v-select>
                                        <input-errors :error-bag="campaignForm.errors" :field="'dealership'"></input-errors>
                                    </div>
                                </div>
                            </div>
                        </tab-content>
                        <tab-content title="Phone Numbers" icon="fas fa-phone fa-rotate-90">
                            <h4 class="mt-4 mb-3"><button class="btn pm-btn pm-btn-purple" type="button" v-b-modal.add-phone-modal><i class="fas fa-plus mr-2"></i>Generate Phone Number</button>
                            </h4>
                            <table class="table table-sm table-bordered">
                                <thead>
                                <tr>
                                    <th>Number</th>
                                    <th>Forward</th>
                                    <th>Call Source</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="phoneNumbers.length === 0">
                                    <td colspan="3">
                                        <div class="text-center text-danger font-weight-bold mt-4 mb-2">No Phone Numbers</div>
                                        <div class="text-center mb-4">
                                            <button class="btn pm-btn pm-btn-md pm-btn-purple" type="button" v-b-modal.add-phone-modal><i class="fas fa-plus mr-2"></i>Add your first Phone Number</button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </tab-content>
                        <tab-content title="Contact" icon="fas fa-cog">
                            <h4 class="mb-3">Additional Features</h4>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row no-gutters">
                                        <div class="col-12 col-md-6">
                                            <div class="feature-input">
                                                <p-check color="primary" class="p-default" name="adf_crm_export" v-model="campaignForm.adf_crm_export">Enable ADF CRM Export</p-check>
                                                <form @submit.prevent="addFieldToAdditionalFeature('addCrmExportEmail', campaignForm.adf_crm_export_email)">
                                                    <div class="input-group mt-3 mb-0" v-if="campaignForm.adf_crm_export">
                                                        <input type="email" class="form-control" required v-model="addCrmExportEmail">
                                                        <div class="input-group-append">
                                                            <button class="btn pm-btn pm-btn-purple" type="submit">Add</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 feature-table-col" v-if="campaignForm.adf_crm_export">
                                            <div class="feature-table">
                                                <table class="table table-sm m-0">
                                                    <thead>
                                                    <tr>
                                                        <th>Emails to Notify</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(email, index) in campaignForm.adf_crm_export_email">
                                                        <td>@{{ email }}</td>
                                                        <td class="text-center align-middle">
                                                            <a href="javascript:;" @click="removeAdditionalFeature(index, campaignForm.adf_crm_export_email)">
                                                                <i class="far fa-times-circle"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="campaignForm.adf_crm_export_email.length === 0">
                                                        <td colspan="2" class="text-center">No items.</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row no-gutters">
                                        <div class="col-12 col-md-6">
                                            <div class="feature-input">
                                                <p-check color="primary" class="p-default" name="lead_alerts" v-model="campaignForm.lead_alerts">Enable Lead Alerts</p-check>
                                                <form @submit.prevent="addFieldToAdditionalFeature('leadAlertEmail', campaignForm.lead_alert_emails)">
                                                    <div class="input-group mt-3 mb-0" v-if="campaignForm.lead_alerts">
                                                        <input type="email" class="form-control" required v-model="leadAlertEmail">
                                                        <div class="input-group-append">
                                                            <button class="btn pm-btn pm-btn-purple" type="submit">Add</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 feature-table-col" v-if="campaignForm.lead_alerts">
                                            <div class="feature-table">
                                                <table class="table table-sm m-0">
                                                    <thead>
                                                    <tr>
                                                        <th>Emails to Notify</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(email, index) in campaignForm.lead_alert_emails">
                                                        <td>@{{ email }}</td>
                                                        <td class="text-center align-middle">
                                                            <a href="javascript:;" @click="removeAdditionalFeature(index, campaignForm.lead_alert_emails)">
                                                                <i class="far fa-times-circle"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="campaignForm.lead_alert_emails.length === 0">
                                                        <td colspan="2" class="text-center">No items.</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row no-gutters">
                                        <div class="col-12 col-md-6">
                                            <div class="feature-input">
                                                <p-check color="primary" class="p-default" name="service_dept" v-model="campaignForm.service_dept">Service Dept Notifications</p-check>
                                                <form @submit.prevent="addFieldToAdditionalFeature('serviceDeptEmail', campaignForm.service_dept_email)">
                                                    <div class="input-group mt-3 mb-0" v-if="campaignForm.service_dept">
                                                        <input type="email" class="form-control" required v-model="serviceDeptEmail">
                                                        <div class="input-group-append">
                                                            <button class="btn pm-btn pm-btn-purple" type="submit">Add</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 feature-table-col" v-if="campaignForm.service_dept">
                                            <div class="feature-table">
                                                <table class="table table-sm m-0">
                                                    <thead>
                                                    <tr>
                                                        <th>Emails to Notify</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(email, index) in campaignForm.service_dept_email">
                                                        <td>@{{ email }}</td>
                                                        <td class="text-center align-middle">
                                                            <a href="javascript:;" @click="removeAdditionalFeature(index, campaignForm.service_dept_email)">
                                                                <i class="far fa-times-circle"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="campaignForm.service_dept_email.length === 0">
                                                        <td colspan="2" class="text-center">No items.</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row no-gutters">
                                        <div class="col-12 col-md-6">
                                            <div class="feature-input">
                                                <p-check color="primary" class="p-default" name="client_passthrough" v-model="campaignForm.client_passthrough">Enable Client Passthrough</p-check>
                                                <form @submit.prevent="addFieldToAdditionalFeature('clientPassThroughEmail', campaignForm.client_passthrough_email)">
                                                    <div class="input-group mt-3 mb-0" v-if="campaignForm.client_passthrough">
                                                        <input type="email" class="form-control" required v-model="clientPassThroughEmail">
                                                        <div class="input-group-append">
                                                            <button class="btn pm-btn pm-btn-purple" type="submit">Add</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 feature-table-col" v-if="campaignForm.client_passthrough">
                                            <div class="feature-table">
                                                <table class="table table-sm m-0">
                                                    <thead>
                                                    <tr>
                                                        <th>Emails to Notify</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(email, index) in campaignForm.client_passthrough_email">
                                                        <td>@{{ email }}</td>
                                                        <td class="text-center align-middle">
                                                            <a href="javascript:;" @click="removeAdditionalFeature(index, campaignForm.client_passthrough_email)">
                                                                <i class="far fa-times-circle"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="campaignForm.client_passthrough_email.length === 0">
                                                        <td colspan="2" class="text-center">No items.</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row no-gutters">
                                        <div class="col-12 col-md-6">
                                            <div class="feature-input">
                                                <p-check color="primary" class="p-default" name="sms_on_callback" v-model="campaignForm.sms_on_callback">SMS On Callback</p-check>
                                                <form @submit.prevent="addFieldToAdditionalFeature('smsOnCallbackNumber', campaignForm.sms_on_callback_number)">
                                                    <div class="input-group mt-3 mb-0" v-if="campaignForm.sms_on_callback">
                                                        <input type="tel" class="form-control" required v-model="smsOnCallbackNumber">
                                                        <div class="input-group-append">
                                                            <button class="btn pm-btn pm-btn-purple" type="submit">Add</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 feature-table-col" v-if="campaignForm.sms_on_callback">
                                            <div class="feature-table">
                                                <table class="table table-sm m-0">
                                                    <thead>
                                                    <tr>
                                                        <th>Phones to Notify</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(phone, index) in campaignForm.sms_on_callback_number">
                                                        <td>@{{ phone }}</td>
                                                        <td class="text-center align-middle">
                                                            <a href="javascript:;" @click="removeAdditionalFeature(index, campaignForm.sms_on_callback_number)">
                                                                <i class="far fa-times-circle"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="campaignForm.sms_on_callback_number.length === 0">
                                                        <td colspan="2" class="text-center">No items.</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
        <b-modal ref="addPhoneModalRef" id="add-phone-modal" size="lg" hide-footer>
            <template slot="modal-header">
                <h4>Add a new Phone Number</h4>
                <span class="close-modal-header float-right" @click="closeModal('addPhoneModalRef')">
                    <i class="fas fa-times float-right"></i>
                </span>
            </template>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <p-radio color="primary" name="crountry" v-model="searchPhoneNumberForm.country" value="US">US</p-radio>
                                <p-radio color="primary" name="crountry" v-model="searchPhoneNumberForm.country" value="CA">CA</p-radio>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <input type="text" class="form-control" name="area_code" placeholder="Area Code" v-model="searchPhoneNumberForm.area_code" @change="clearError(searchPhoneNumberForm, 'area_code')" :class="{'is-invalid': searchPhoneNumberForm.errors.has('area_code')}">
                                <input-errors :error-bag="searchPhoneNumberForm.errors" :field="'area_code'"></input-errors>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <input type="text" class="form-control" name="postal_code" placeholder="Zip" v-model="searchPhoneNumberForm.postal_code" @change="clearError(searchPhoneNumberForm, 'postal_code')" :class="{'is-invalid': searchPhoneNumberForm.errors.has('postal_code')}">
                                <input-errors :error-bag="searchPhoneNumberForm.errors" :field="'postal_code'"></input-errors>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <input type="text" class="form-control" name="contains" placeholder="Contains ex. Cars..." v-model="searchPhoneNumberForm.contains" @change="clearError(searchPhoneNumberForm, 'contains')" :class="{'is-invalid': searchPhoneNumberForm.errors.has('contains')}">
                                <input-errors :error-bag="searchPhoneNumberForm.errors" :field="'contains'"></input-errors>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn pm-btn pm-btn-purple" type="button" @click="searchPhones" :disabled="loadingPhoneModal">
                                <span v-if="!loadingPhoneModal">Search Phones</span>
                                <spinner-icon class="white" :size="'xs'" v-if="loadingPhoneModal"></spinner-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3 text-center p-3" v-if="showAvailablePhoneNumbers && availablePhoneNumbers.length === 0">
                <strong class="text-danger">Your search returned no results.</strong>
                <div>Please try again.</div>
            </div>
            <div class="card mt-3" v-if="showAvailablePhoneNumbers && availablePhoneNumbers.length > 0">
                <div class="card-body">
                    <h5>Available Phone Numbers</h5>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="phone_number">Phone Number</label>
                                <v-select name="phone_number" :options="availablePhoneNumbers" v-model="purchasePhoneNumberForm.phone_number" class="filter--v-select" @input="clearError(purchasePhoneNumberForm, 'phone_number')" :class="{'is-invalid': purchasePhoneNumberForm.errors.has('phone_number')}"></v-select>
                                <input-errors :error-bag="purchasePhoneNumberForm.errors" :field="'phone_number'"></input-errors>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="forward">Forward Number</label>
                                <input type="text" class="form-control" name="forward" v-model="purchasePhoneNumberForm.forward" @input="clearError(purchasePhoneNumberForm, 'forward')" :class="{'is-invalid': purchasePhoneNumberForm.errors.has('forward')}"></v-select>
                                <input-errors :error-bag="purchasePhoneNumberForm.errors" :field="'forward'"></input-errors>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="call_source">Call Source</label>
                                <input type="text" class="form-control" name="call_source" v-model="purchasePhoneNumberForm.call_source" @input="clearError(purchasePhoneNumberForm, 'call_source')" :class="{'is-invalid': purchasePhoneNumberForm.errors.has('call_source')}"></v-select>
                                <input-errors :error-bag="purchasePhoneNumberForm.errors" :field="'call_source'"></input-errors>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn pm-btn pm-btn-purple" type="button" @click="purchasePhoneNumber" :disabled="loadingPurchaseNumber">
                                <span v-if="!loadingPurchaseNumber">$ Purchase Number</span>
                                <spinner-icon :size="'xs'" class="white" v-if="loadingPurchaseNumber"></spinner-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
@endsection

{{--@extends('layouts.remark_campaign')--}}

{{--@section('header')--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/jquery-wizard/jquery-wizard.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/icheck/icheck.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/typeahead-js/typeahead.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-tokenfield/bootstrap-tokenfield.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-datepicker/bootstrap-datepicker.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/formvalidation/formValidation.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('css/sweetalert.css') }}">--}}
    {{--<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('css/jsgrid.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('css/jsgrid-theme.css') }}">--}}
    {{--<style type="text/css" media="all">--}}
        {{--form > h4 {--}}
            {{--margin-top: 20px;--}}
        {{--}--}}
        {{--.btn.dropdown-toggle.btn-default {--}}
            {{--margin-top: 8px;--}}
        {{--}--}}
    {{--</style>--}}
{{--@endsection--}}

{{--@section('manualStyle')--}}
    {{--.wizard-buttons {--}}
        {{--padding-top: 50px;--}}
    {{--}--}}
{{--@endsection--}}

{{--@section('campaign_content')--}}
    {{--<div class="container-fluid">--}}
        {{--<form data-fv-live="enabled" id="campaign-form" class="form form-horizontal" action="{{ secure_url('/campaign/' . $campaign->id . '/update') }}" method="post">--}}
            {{--{{ csrf_field() }}--}}
            {{--<input type="hidden" name="phone_number_id" value="{{ $campaign->phone_number_id }}">--}}
            {{--<input type="hidden" name="campaign_id" value="{{ $campaign->id }}">--}}
            {{--@if ($errors->count() > 0)--}}
            {{--<div class="row-fluid">--}}
                {{--<div class="col-md-12">--}}
                    {{--<div class="alert alert-danger">--}}
                        {{--<h3>There were some errors:</h3>--}}
                        {{--<ul>--}}
                            {{--@foreach ($errors->all() as $message)--}}
                            {{--<li>{{ $message }}</li>--}}
                            {{--@endforeach--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--@endif--}}
            {{--<div class="row-fluid">--}}
                {{--<div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2">--}}
                    {{--<h2>Edit Campaign</h2>--}}
                    {{--<div class="nav-tabs-horizontal" data-plugin="tabs">--}}
                        {{--<ul class="nav nav-tabs nav-tabs-reverse" role="tablist">--}}
                            {{--<li class="nav-item" role="presentation" style="display: list-item;">--}}
                                {{--<a class="nav-link active"--}}
                                   {{--data-toggle="tab"--}}
                                   {{--href="#exampleTabsReverseOne"--}}
                                   {{--aria-controls="exampleTabsReverseOne"--}}
                                   {{--role="tab">--}}
                                    {{--Details--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li class="nav-item" role="presentation" style="display: list-item;">--}}
                                {{--<a class="nav-link"--}}
                                   {{--data-toggle="tab"--}}
                                   {{--href="#exampleTabsReverseTwo"--}}
                                   {{--aria-controls="exampleTabsReverseTwo"--}}
                                   {{--role="tab">--}}
                                    {{--Accounts--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li class="nav-item" role="presentation" style="display: list-item;">--}}
                                {{--<a class="nav-link"--}}
                                   {{--data-toggle="tab"--}}
                                   {{--href="#exampleTabsReverseThree"--}}
                                   {{--aria-controls="exampleTabsReverseThree"--}}
                                   {{--role="tab">--}}
                                    {{--Phone--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li class="nav-item" role="presentation" style="display: list-item;">--}}
                                {{--<a class="nav-link"--}}
                                   {{--data-toggle="tab"--}}
                                   {{--href="#exampleTabsReverseFour"--}}
                                   {{--aria-controls="exampleTabsReverseFour"--}}
                                   {{--role="tab">--}}
                                    {{--Leads--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li class="dropdown nav-item" role="presentation" style="display: none;">--}}
                                {{--<a class="dropdown-toggle nav-link" data-toggle="dropdown" href="#" aria-expanded="false">Dropdown </a>--}}
                                {{--<div class="dropdown-menu" role="menu">--}}
                                    {{--<a class="dropdown-item"--}}
                                       {{--data-toggle="tab"--}}
                                       {{--href="exampleTabsReverseOne"--}}
                                       {{--aria-controls="exampleTabsReverseOne"--}}
                                       {{--role="tab"--}}
                                       {{--style="display: none;">--}}
                                        {{--Details--}}
                                    {{--</a>--}}
                                    {{--<a class="dropdown-item"--}}
                                       {{--data-toggle="tab"--}}
                                       {{--href="#exampleTabsReverseTwo"--}}
                                       {{--aria-controls="exampleTabsReverseTwo"--}}
                                       {{--role="tab"--}}
                                       {{--style="display: none;">--}}
                                        {{--Accounts--}}
                                    {{--</a>--}}
                                    {{--<a class="dropdown-item"--}}
                                       {{--data-toggle="tab"--}}
                                       {{--href="#exampleTabsReverseThree"--}}
                                       {{--aria-controls="exampleTabsReverseThree"--}}
                                       {{--role="tab">--}}
                                        {{--Phone--}}
                                    {{--</a>--}}
                                    {{--<a class="dropdown-item"--}}
                                       {{--data-toggle="tab"--}}
                                       {{--href="#exampleTabsReverseFour"--}}
                                       {{--aria-controls="exampleTabsReverseFour"--}}
                                       {{--role="tab">--}}
                                        {{--Leads--}}
                                    {{--</a>--}}
                                {{--</div>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                        {{--<div class="tab-content pt-20">--}}
                            {{--<div class="tab-pane active" id="exampleTabsReverseOne" role="tabpanel">--}}
                                {{--<div class="form-group floating">--}}
                                    {{--<label for="name" class="floating-label">Campaign Name</label>--}}
                                    {{--<input type="text" class="form-control" name="name" data-fv-field="name" value="{{ old('name') ?: $campaign->name }}" required>--}}
                                {{--</div>--}}
                                {{--<div class="form-group floating">--}}
                                    {{--<label for="order" class="floating-label">Order #</label>--}}
                                    {{--<input type="text" class="form-control" name="order" autocomplete="off" data-fv-field="order" value="{{ old('order') ?: $campaign->order_id }}" required>--}}
                                {{--</div>--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="name" class="floating-label">Status</label>--}}
                                    {{--<select name="status" class="form-control">--}}
                                        {{--<option {{ old('status') ?: $campaign->status == 'Active' ? 'selected' : '' }}>Active</option>--}}
                                        {{--<option {{ old('status') ?: $campaign->status == 'Archived' ? 'selected' : '' }}>Archived</option>--}}
                                        {{--<option {{ old('status') ?: $campaign->status == 'Completed' ? 'selected' : '' }}>Completed</option>--}}
                                        {{--<option {{ old('status') ?: $campaign->status == 'Upcoming' ? 'selected' : '' }}>Upcoming</option>--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                                {{--<div class="form-group floating">--}}
                                    {{--<div class="input-daterange" data-plugin="datepicker" style="padding-bottom: 40px">--}}
                                        {{--<div class="input-group">--}}
                                            {{--<span class="input-group-addon">--}}
                                                {{--<i class="icon md-calendar" aria-hidden="true"></i>--}}
                                            {{--</span>--}}
                                            {{--<input type="text" class="form-control {{ empty(old('start')) && empty($campaign->starts_at) ? 'empty' : ''  }}" name="start" placeholder="Starts on" value="{{ old('start') ?: ! empty($campaign->starts_at) ? $campaign->starts_at->format("m/d/Y") : '' }}">--}}
                                        {{--</div>--}}
                                        {{--<div class="input-group">--}}
                                            {{--<span class="input-group-addon">to</span>--}}
                                            {{--<input type="text" class="form-control {{ empty(old('end')) && empty($campaign->ends_at) ? 'empty' : ''  }}" name="end" placeholder="Ends on" value="{{ old('end') ?: ! empty($campaign->ends_at) ? $campaign->ends_at->format("m/d/Y") : '' }}">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="form-group" style="padding-top: 10px">--}}
                                    {{--<div class="input-group">--}}
                                        {{--<span class="input-group-addon">--}}
                                            {{--<i class="icon md-calendar" aria-hidden="true"></i>--}}
                                        {{--</span>--}}
                                        {{--<input type="text" class="form-control datepicker" value="{{ old('expires') ?: ! empty($campaign->expires_at) ? $campaign->expires_at->format("m/d/Y") : '' }}" name="expires" placeholder="Expires on" data-plugin="datepicker">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="tab-pane" id="exampleTabsReverseTwo" role="tabpanel">--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="agency" class="floating-label">Agency</label>--}}
                                    {{--<select class="form-control select2" name="agency" data-width="auto" autocomplete="off" required>--}}
                                        {{--<option disabled selected>Agency</option>--}}
                                        {{--@if ($agencies->count() > 0)--}}
                                            {{--@foreach ($agencies as $agency)--}}
                                                {{--<option value="{{ $agency->id }}" {{ $campaign->agency_id == $agency->id ? 'selected' : '' }}>{{ $agency->name }}</option>--}}
                                            {{--@endforeach--}}
                                        {{--@endif--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="client" class="floating-label">Dealership</label>--}}
                                    {{--<select class="form-control select2" name="client" data-width="auto" autocomplete="off" required>--}}
                                        {{--<option disabled selected>Client</option>--}}
                                        {{--@if ($dealerships->count() > 0)--}}
                                            {{--@foreach ($dealerships as $dealership)--}}
                                                {{--<option value="{{ $dealership->id }}" {{ $campaign->dealership_id == $dealership->id ? 'selected' : '' }}>{{ $dealership->name }}</option>--}}
                                            {{--@endforeach--}}
                                        {{--@endif--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="tab-pane" id="exampleTabsReverseThree" role="tabpanel">--}}
                                {{--<button id="#phone-search-button"--}}
                                        {{--type="button"--}}
                                        {{--role="button"--}}
                                        {{--class="btn btn-round btn-sm btn-success pull-right"--}}
                                        {{--data-toggle="modal"--}}
                                        {{--data-target="#addPhoneModal"--}}
                                {{-->--}}
                                    {{--<i class="icon md-plus" aria-label="Add a new phone number"></i>--}}
                                    {{--Add Phone Number--}}
                                {{--</button>--}}
                                {{--<div style="margin-top: 10px;">--}}
                                    {{--@if ($campaign->phones)--}}
                                        {{--<table class="table table-hover" id="campaign_phone_number_table">--}}
                                            {{--<thead>--}}
                                            {{--<tr>--}}
                                                {{--<th>Number</th>--}}
                                                {{--<th>Forward</th>--}}
                                                {{--<th>Call Source</th>--}}
                                                {{--<th>Actions</th>--}}
                                            {{--</tr>--}}
                                            {{--</thead>--}}
                                            {{--<tbody>--}}
                                            {{--@foreach ($campaign->phones as $phone)--}}
                                                {{--<tr data-phone_number_id="{{ $phone->phone_number_id }}">--}}
                                                    {{--<td class="form-item"--}}
                                                        {{--data-field_name="phone_number_id"--}}
                                                        {{--data-field_value="{{ $phone->phone_number_id }}">{{ $phone->phone_number }}</td>--}}
                                                    {{--<td class="form-item"--}}
                                                        {{--data-field_name="forward"--}}
                                                        {{--data-field_value="{{ $phone->forward }}">{{ $phone->forward }}</td>--}}
                                                    {{--<td class="form-item"--}}
                                                        {{--data-field_name="call_source_name"--}}
                                                        {{--data-field_value="{{ $phone->call_source_name }}">{{ ucwords($phone->call_source_name) }}</td>--}}
                                                    {{--<td class="form-buttons">--}}
                                                        {{--<button type="button" class="btn btn-sm btn-default edit-number" onClick="edit_number($(this))">Edit</button>--}}
                                                        {{--@if ($campaign->isExpired())--}}
                                                            {{--<button type="button" class="btn btn-sm btn-danger release-number" onClick="release_number($(this))">Release</button>--}}
                                                        {{--@endif--}}
                                                    {{--</td>--}}
                                                {{--</tr>--}}
                                            {{--@endforeach--}}
                                            {{--</tbody>--}}
                                        {{--</table>--}}
                                    {{--@endif--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="tab-pane" id="exampleTabsReverseFour" role="tabpanel">--}}
                                {{--<div class="checkbox floating">--}}
                                    {{--<label>--}}
                                        {{--<input name="adf_crm_export" type="checkbox" class="icheckbox-primary" {{ $campaign->adf_crm_export ? 'checked="checked"' : '' }}> Enable ADF CRM Export--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                                {{--<div id="adf_crm_integration_form" class="col-md-11 col-md-offset-1">--}}
                                    {{--<div class="form-group floating">--}}
                                        {{--<label for="adf_crm_export_email" class="floating-label">ADF CRM Email</label>--}}
                                        {{--<input type="text" class="form-control multi-email" name="adf_crm_export_email" value="{{ old('adf_crm_export_email') ?: $campaign->adf_crm_export_email }}">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="checkbox floating">--}}
                                    {{--<label>--}}
                                        {{--<input name="lead_alerts" type="checkbox" class="icheckbox-primary" {{ $campaign->lead_alerts ? 'checked="checked"' : '' }}>--}}
                                        {{--Enable Lead Alerts--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                                {{--<div id="adf_crm_lead_alert_email_form" class="col-md-11 col-md-offset-1">--}}
                                    {{--<div class="form-group floating">--}}
                                        {{--<label for="lead_alert_email" class="floating-label">Lead Alert Email(s)</label>--}}
                                        {{--<input type="text" class="form-control multi-email" name="lead_alert_email" value="{{ old('lead_alert_email') ?: $campaign->lead_alert_email }}">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="checkbox floating">--}}
                                    {{--<label>--}}
                                        {{--<input name="client_passthrough" type="checkbox" class="icheckbox-primary" {{ $campaign->client_passthrough ? 'checked="checked"' : '' }}> Enable Client Passthrough--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                                {{--<div id="adf_crm_client_passthrough_form" class="col-md-11 col-md-offset-1">--}}
                                    {{--<div class="form-group floating">--}}
                                        {{--<label for="client_passthrough_email" class="floating-label">Client Passthrough Email(s)</label>--}}
                                        {{--<input type="text" class="form-control multi-email" name="client_passthrough_email" value="{{ old('client_passthrough_email') ?: $campaign->client_passthrough_email }}">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="checkbox floating">--}}
                                    {{--<label>--}}
                                        {{--<input name="service_dept" type="checkbox" class="icheckbox-primary" {{ $campaign->service_dept ? 'checked="checked"' : '' }}> Enable Service Dept Notifications--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                                {{--<div id="adf_crm_service_dept_email_form" class="col-md-11 col-md-offset-1">--}}
                                    {{--<div class="form-group floating">--}}
                                        {{--<label for="service_dept_email" class="floating-label">Service Dept Email(s)</label>--}}
                                        {{--<input type="text" class="form-control multi-email" name="service_dept_email" value="{{ old('service_dept_email') ?: $campaign->service_dept_email }}">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="checkbox floating">--}}
                                    {{--<label>--}}
                                        {{--<input name="sms_on_callback" type="checkbox" class="icheckbox-primary" {{ $campaign->sms_on_callback ? 'checked="checked"' : '' }}> Enable SMS On Callback--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                                {{--<div id="adf_crm_sms_on_callback_form" class="col-md-11 col-md-offset-1">--}}
                                    {{--<div class="form-group floating">--}}
                                        {{--<label for="sms_on_callback_number" class="floating-label">SMS On Callback Number</label>--}}
                                        {{--<input type="text" class="form-control" name="sms_on_callback_number" value="{{ old('sms_on_callback_number') ?: $campaign->sms_on_callback_number }}">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<button id="save-campaign-button" class="btn btn-success float-right">Save</button>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</form>--}}
    {{--</div>--}}
    {{--<div class="modal fade show" id="addPhoneModal" aria-labelledby="addPhoneModalLabel" role="dialog" tabindex="-1">--}}
        {{--<div class="modal-dialog">--}}
            {{--<div class="modal-content">--}}
                {{--<div class="modal-header">--}}
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                        {{--<span aria-hidden="true">×</span>--}}
                    {{--</button>--}}
                    {{--<h4 class="modal-title" id="addPhoneModalLabel">Add a new Phone Number</h4>--}}
                {{--</div>--}}
                {{--<div class="modal-body">--}}
                    {{--<form id="phone-search-form" class="form" action="{{ secure_url('/phones/search') }}" method="post">--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-md-3 form-group">--}}
                                {{--<label class="radio-inline ">--}}
                                    {{--<input name="country" class="form-group" type="radio" value="US" checked="checked"> US--}}
                                {{--</label>--}}
                                {{--<label class="radio-inline">--}}
                                    {{--<input name="country" class="form-group" type="radio" value="CA"> CA--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-3 form-group">--}}
                                {{--<input type="text" class="form-control" name="areaCode" placeholder="Area Code">--}}
                            {{--</div>--}}
                            {{--<div class="col-md-3 form-group">--}}
                                {{--<input type="text" class="form-control" name="inPostalCode" placeholder="Zip">--}}
                            {{--</div>--}}
                            {{--<div class="col-md-3 form-group">--}}
                                {{--<input type="text" class="form-control" name="contains" placeholder="Contains ex. Cars...">--}}
                            {{--</div>--}}
                            {{--<div class="col-md-12 float-right">--}}
                                {{--<button id="phone-search-button" class="btn btn-primary waves-effect" type="button">Search Phones</button>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-12" style="margin-top: 15px;" id="phone-search-results">--}}
                            {{--</div>--}}
                            {{--<ul class="list-group" id="phone_numbers"></ul>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--@endsection--}}

{{--@section('scriptTags')--}}
    {{--<script src="{{ secure_url('js/Plugin/material.js') }}"></script>--}}
    {{--<script src="{{ secure_url('js/Plugin/jquery-wizard.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/jquery-wizard/jquery-wizard.js') }}"></script>--}}

    {{--<script src="{{ secure_url('js/Plugin/icheck.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/icheck/icheck.js') }}"></script>--}}

    {{--<script src="{{ secure_url('js/Plugin/bootstrap-tokenfield.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/bootstrap-tokenfield/bootstrap-tokenfield.js') }}"></script>--}}

    {{--<script src="{{ secure_url('js/Plugin/bootstrap-datepicker.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>--}}

    {{--<script src="{{ secure_url('vendor/typeahead-js/typeahead.bundle.min.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/formvalidation/formValidation.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/formvalidation/framework/bootstrap.js') }}"></script>--}}
    {{--<script src="{{ secure_url('js/Plugin/sweetalert.min.js') }}"></script>--}}

    {{--<script src="{{ secure_url('js/Plugin/formatter.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/formatter/jquery.formatter.js') }}"></script>--}}

    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>--}}
    {{--<script type="text/javascript" src="{{ secure_url('vendor/jsgrid/jsgrid.min.js') }}"></script>--}}

    {{--<script type="text/html" id="phone_number_row">--}}
        {{--<td class="form-item"--}}
            {{--data-field_name="phone_number_id"--}}
            {{--data-field_value="__PHONE_ID__">__PHONE_NUMBER__</td>--}}
        {{--<td class="form-item"--}}
            {{--data-field_name="forward"--}}
            {{--data-field_value="__FORWARD__">__FORWARD__</td>--}}
        {{--<td class="form-item"--}}
            {{--data-field_name="call_source_name"--}}
            {{--data-field_value="__CALL_SOURCE__">__CALL_SOURCE__</td>--}}
        {{--<td class="form-buttons">--}}
            {{--<button type="button" class="btn btn-sm btn-default edit-number" onClick="edit_number($(this))">Edit</button>--}}
            {{--@if ($campaign->isExpired())--}}
                {{--<button type="button" class="btn btn-sm btn-danger release-number" onClick="release_number($(this))">Release</button>--}}
            {{--@endif--}}
        {{--</td>--}}
    {{--</script>--}}

    {{--<script type="text/javascript">--}}
        {{--var edit_number = function () { console.log('in edit_number function')};--}}
        {{--var reset_phone_form = function () { console.log('in reset_phone_form function')};--}}
        {{--var release_number = function () { console.log('in release_number function')};--}}

        {{--$(document).keypress(function(event) {--}}
            {{--if (event.which == '13') {--}}
                {{--event.preventDefault();--}}
            {{--}--}}
        {{--});--}}

        {{--$(document).ready(function() {--}}
            {{--var enable_adf = false;--}}
            {{--var enable_alerts = false;--}}
            {{--var enable_client_passthrough = false;--}}
            {{--var enable_service_dept = false;--}}
            {{--var enable_sms_on_callback = false;--}}
            {{--var schedules = 1;--}}

            {{--$("#wtf").change(function() {--}}
                {{--if ($(this).val().length > 0) {--}}
                    {{--$("input[name=forward]").removeClass("empty");--}}
                {{--} else {--}}
                    {{--$("input[name=forward]").addClass("empty");--}}
                {{--}--}}
            {{--});--}}

            {{--if (! $("input[name=adf_crm_export]").prop("checked")) {--}}
                {{--$("#adf_crm_integration_form").toggle();--}}
            {{--}--}}
            {{--if (! $("input[name=lead_alerts]").prop("checked")) {--}}
                {{--$("#adf_crm_lead_alert_email_form").toggle();--}}
            {{--}--}}
            {{--if (! $("input[name=client_passthrough]").prop("checked")) {--}}
                {{--$("#adf_crm_client_passthrough_form").toggle();--}}
            {{--}--}}
            {{--if (! $("input[name=service_dept]").prop("checked")) {--}}
                {{--$("#adf_crm_service_dept_email_form").toggle();--}}
            {{--}--}}
            {{--if (! $("input[name=sms_on_callback]").prop("checked")) {--}}
                {{--$("#adf_crm_sms_on_callback_form").toggle();--}}
            {{--}--}}

            {{--$("#add-new-client").click(function() {--}}
                {{--sweetAlert("New Client", "Added a new client", "success");--}}
            {{--});--}}

            {{--$("input[name=adf_crm_export]").change(function() {--}}
                {{--enable_adf = !enable_adf;--}}
                {{--$("#adf_crm_integration_form").toggle();--}}
            {{--});--}}
            {{--$("input[name=lead_alerts]").change(function() {--}}
                {{--enable_alerts = !enable_alerts;--}}
                {{--$("#adf_crm_lead_alert_email_form").toggle();--}}
            {{--});--}}
            {{--$("input[name=client_passthrough]").change(function() {--}}
                {{--enable_client_passthrough = !enable_client_passthrough;--}}
                {{--$("#adf_crm_client_passthrough_form").toggle();--}}
            {{--});--}}
            {{--$("input[name=service_dept]").change(function() {--}}
                {{--enable_service_dept = !enable_service_dept;--}}
                {{--$("#adf_crm_service_dept_email_form").toggle();--}}
            {{--});--}}
            {{--$("input[name=sms_on_callback]").change(function() {--}}
                {{--enable_sms_on_callback = !enable_sms_on_callback;--}}
                {{--$("#adf_crm_sms_on_callback_form").toggle();--}}
            {{--});--}}

            {{--$(".multi-email").tokenfield({--}}
                {{--"inputType": "email"--}}
            {{--});--}}

            {{--$('.select2').select2();--}}

            {{--var formatPhone = function (phone) {--}}
                {{--phone = phone.replace('+1', '').trim();--}}

                {{--if (phone.length == 10) {--}}
                    {{--console.log('phone number is 10 long');--}}
                    {{--var areaCode = phone.substring(0, 3);--}}
                    {{--var prefix = phone.substring(3, 6);--}}
                    {{--var lastFour = phone.substring(6,10);--}}

                    {{--return '('+areaCode+') '+prefix+'-'+lastFour;--}}
                {{--}--}}

                {{--return false;--}}
            {{--};--}}

            {{--var refreshPhones = function () {--}}
                {{--$.get("{{ secure_url('/campaign/'.$campaign->id.'/phone-list-json') }}",--}}
                    {{--function (data) {--}}
                        {{--$("#campaign_phone_number_table > tbody").remove();--}}

                        {{--var html = '<tbody>';--}}
                        {{--$(data).each(function (id, phone) {--}}
                            {{--var phone_number, forward, source = '';--}}
                            {{--if (phone.phone_number) {--}}
                                {{--phone_number = (phone.phone_number.length > 0 ? formatPhone(phone.phone_number) : '');--}}
                            {{--}--}}
                            {{--if (phone.forward) {--}}
                                {{--forward = (phone.forward.length > 0 ? formatPhone(phone.forward) : '');--}}
                            {{--}--}}

                            {{--if (phone.call_source_name) {--}}
                                {{--source = (phone.call_source_name.length > 0 ? phone.call_source_name : '');--}}
                            {{--}--}}
                            {{--html += '<tr data-phone_number_id="' + phone.phone_number_id + '"><td>' + phone_number +--}}
                                {{--'</td><td>' + forward + '</td><td>' + source +--}}
                                {{--'</td><td><button class="btn btn-default btn-sm">Edit</button><button class="btn btn-danger btn-sm">Disable</button></td></tr>';--}}
                        {{--});--}}
                        {{--html += '</tbody>';--}}

                        {{--$("#campaign_phone_number_table").append(html);--}}
                    {{--}, 'json');--}}
            {{--};--}}

            {{--$("#phone-search-button").click(function() {--}}
                {{--$.post(--}}
                    {{--"{{ secure_url('/phones/search') }}",--}}
                    {{--$("#phone-search-form").serialize(),--}}
                    {{--function (data) {--}}
                        {{--var html = '<form id="phone-form"><table class="table table-hover table-striped table-bordered">' +--}}
                            {{--'<input type="hidden" style="display: none;" name="client_id" value="' + $("select[name=client]").val() + '">' +--}}
                            {{--'<input type="hidden" style="display: none;" name="campaign_id" value="{{ $campaign->id }}">' +--}}
                            {{--'<div class="form-group"><label for="phone_number" class="form-label">Phone Number</label><select name="phone_number" class="form-control" required="required"><option disabled="disabled" selected="selected">Choose a number</option>';--}}
                        {{--$(data.numbers).each(function (phone) {--}}
                            {{--html += '<option value="' + $(this)[0].phoneNumber + '">' + $(this)[0].phone + ': ' + $(this)[0].location + '</option>';--}}
                        {{--});--}}
                        {{--html += '</select></div>' +--}}
                            {{--'<div class="form-group"><label for="forward" class="form-label">Forward Number</label><input type="text" name="forward" class="form-control" required="required"></div>' +--}}
                            {{--'<div class="form-group"><label for="call_source_name" class="form-label">Call Source</label>' +--}}
                            {{--'<select name="call_source_name" class="form-control" required="required"><option>Email</option><option>Mailer</option><option>SMS</option></select></div>' +--}}
                            {{--'<div class="col-md-12 float-right">' +--}}
                            {{--'    <button id="add-phone" class="btn btn-success waves-effect" data-dismiss="modal" type="button">$ Purchase Number</button>' +--}}
                            {{--'</div></form>';--}}

                        {{--$("#phone-search-results").html(html);--}}

                        {{--$("#add-phone").click(function() {--}}
                            {{--$.post(--}}
                                {{--"{{ secure_url('/phones/provision') }}",--}}
                                {{--{--}}
                                    {{--phone_number: $("select[name=phone_number]").val(),--}}
                                    {{--call_source_name: $("select[name=call_source_name] option:selected").val(),--}}
                                    {{--forward: $("input[name=forward]").val(),--}}
                                    {{--campaign_id: $("input[name=campaign_id]").val(),--}}
                                    {{--client_id: $("select[name=client] option:selected").val()--}}
                                {{--},--}}
                                {{--function (data) {--}}
                                    {{--refreshPhones();--}}
                                    {{--$("#phone-search-results").empty();--}}
                                    {{--$("#phone-search-form")[0].reset();--}}
                                {{--},--}}
                                {{--'json'--}}
                            {{--);--}}
                        {{--});--}}
                    {{--},--}}
                    {{--'json'--}}
                {{--);--}}
            {{--});--}}

            {{--$("#save-campaign-button").click(function() {--}}
                {{--$("#save-campaign-button").attr('disabled', 'disabled').addClass('disabled');--}}

                {{--$(this).parent().closest('form').submit();--}}
            {{--});--}}

            {{--release_number = function (btn) {--}}
                {{--var $td_id = btn.parent().parent().children('td[data-field_name=phone_number_id]').first();--}}
                {{--var phone_id = $td_id.data('field_value');--}}

                {{--var form = $.ajax({--}}
                    {{--url: "{{ env('APP_URL') }}/campaign/{{ $campaign->id }}/phone/" + phone_id + "/release",--}}
                    {{--method: "post",--}}
                {{--});--}}
                {{--form.fail(function (response) {--}}
                    {{--reset_phone_form(phone_id, phone_number, forward, call_source, element);--}}
                    {{--alert("Unable to release phone number");--}}
                {{--});--}}
                {{--form.done(function (response) {--}}
                    {{--btn.parent().parent().slideUp();--}}
                {{--});--}}
            {{--};--}}

            {{--edit_number = function (btn) {--}}
                {{--if ($("#save-phone-number-btn").lenth > 0)--}}
                {{--{--}}
                    {{--return;--}}
                {{--}--}}

                {{--var $buttons = btn.parent();--}}
                {{--var $td_id = btn.parent().parent().children('td[data-field_name=phone_number_id]').first();--}}
                {{--var phone_id = $td_id.data('field_value');--}}
                {{--var phone_number = $td_id.html();--}}
                {{--var $td_forward = btn.parent().parent().children('td[data-field_name=forward]').first();--}}
                {{--var forward = $td_forward.data('field_value');--}}
                {{--var $td_call_source = btn.parent().parent().children('td[data-field_name=call_source_name]').first();--}}
                {{--var call_source = $td_call_source.data('field_value') || '';--}}
                {{--$td_id.html('<p class="form-control disabled">'+phone_number+'</p>');--}}
                {{--$td_forward.html('<input type="text" class="input-sm form-control" size="11" name="forward" value="'+forward+'">');--}}
                {{--$td_call_source.html('<select class="input-sm form-control" name="call_source_name" value="'+call_source+'"><option>Email</option><option>Mailer</option><option>SMS</option></select>');--}}
                {{--$buttons.html('<button id="save-phone-number-btn" class="btn btn-success" type="button" role="button" onClick="submit_phone_form(\''+phone_id+'\',\''+phone_number+'\',\''+forward+'\',\''+call_source+'\',$(this))">Save</button> ' +--}}
                    {{--'<button id="cancel-phone-number-btn" class="btn btn-default" type="button" role="button" onClick="reset_phone_form(\''+phone_id+'\',\''+phone_number+'\',\''+forward+'\',\''+call_source+'\',$(this))">Cancel</button>');--}}
            {{--};--}}

            {{--reset_phone_form = function (phone_id, phone_number, forward, call_source, element) {--}}
                {{--var html_block = $("#phone_number_row").html()--}}
                    {{--.replace(/__PHONE_ID__/g, phone_id)--}}
                    {{--.replace(/__PHONE_NUMBER__/g, phone_number)--}}
                    {{--.replace(/__FORWARD__/g, forward)--}}
                    {{--.replace(/__CALL_SOURCE__/g, call_source);--}}
                {{--element.parent().parent().html(html_block);--}}
            {{--};--}}

            {{--submit_phone_form = function (phone_id, phone_number, forward, call_source, element) {--}}
                {{--var form = $.ajax({--}}
                    {{--url: "{{ env('APP_URL') }}/campaign/{{ $campaign->id }}/phone/" + phone_id + "/edit",--}}
                    {{--method: "post",--}}
                    {{--data: {--}}
                        {{--forward: $("[name=forward]").val(),--}}
                        {{--call_source_name: $("select[name=call_source_name] option:selected").val()--}}
                    {{--}--}}
                {{--});--}}
                {{--form.fail(function (response) {--}}
                    {{--reset_phone_form(phone_id, phone_number, forward, call_source, element);--}}
                    {{--alert("Unable to process form");--}}
                {{--});--}}
                {{--form.done(function (response) {--}}
                    {{--reset_phone_form(phone_id, phone_number, $("[name=forward]").val(), $("[name=call_source_name] option:selected").val(), element);--}}
                {{--});--}}
            {{--};--}}
        {{--});--}}
    {{--</script>--}}
{{--@endsection--}}
