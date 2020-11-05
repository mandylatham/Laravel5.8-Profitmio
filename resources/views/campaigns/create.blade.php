@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/campaigns-create.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.agencies = @json($agencies);
        window.dealerships = @json($dealerships);
        window.campaignIndexUrl = "{{ route('campaigns.index') }}";
        window.saveCampaignUrl = "{{ route('campaigns.store') }}";
        window.searchPhoneUrl = "{{ route('phone.search') }}";
        window.provisionPhoneUrl = "{{ route('phone.provision') }}";
    </script>
    <script src="{{ asset('js/campaigns-create.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="campaign-create" v-cloak>
        <div class="row">
            <div class="col-12 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2 wizard-container">
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
                                <date-pick dusk="starts-on-field" :custom-formatter="formatDate" v-model="campaignForm.start"
                                    :has-input-element="true" :input-attributes="datePickInputClasses" type="date"
                                    @input="clearError(campaignForm, 'start')" :class="{'is-invalid': campaignForm.errors.has('start')}"></date-pick>
                                <input-errors :error-bag="campaignForm.errors" :field="'start'"></input-errors>
                            </div>
                            <div class="form-group col-6">
                                <label for="end">Ends on</label>
                                <date-pick dusk="ends-on-field" name="end" :custom-formatter="formatDate" v-model="campaignForm.end"
                                    :has-input-element="true" :input-attributes="datePickInputClasses" type="date"
                                    @input="clearError(campaignForm, 'end')" :class="{'is-invalid': campaignForm.errors.has('end')}"></date-pick>
                                <input-errors :error-bag="campaignForm.errors" :field="'end'"></input-errors>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="expires">Expires on</label>
                                <date-pick dusk="expires-on-field" name="expires" :custom-formatter="formatDate" v-model="campaignForm.expires"
                                    :has-input-element="true" :input-attributes="datePickInputClasses"
                                    @input="clearError(campaignForm, 'expires')" :class="{'is-invalid': campaignForm.errors.has('expires')}"></date-pick>
                                <input-errors :error-bag="campaignForm.errors" :field="'expires'"></input-errors>
                            </div>
                        </div>
                    </tab-content>
                    <tab-content title="Accounts" icon="fas fa-user" :before-change="validateAccountsTab">
                        <div class="form-row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="agency">Agency</label>
                                    <v-select dusk="agency-select" :options="agencies" label="name" v-model="agencySelected" class="filter--v-select" @input="clearError(campaignForm, 'agency')" :class="{'is-invalid': campaignForm.errors.has('agency')}"></v-select>
                                    <input-errors :error-bag="campaignForm.errors" :field="'agency'"></input-errors>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="Dealership">Dealership</label>
                                    <v-select dusk="dealership-select" :options="dealerships" label="name" v-model="dealershipSelected" class="filter--v-select" @input="clearError(campaignForm, 'dealership')" :class="{'is-invalid': campaignForm.errors.has('dealership')}"></v-select>
                                    <input-errors :error-bag="campaignForm.errors" :field="'dealership'"></input-errors>
                                </div>
                            </div>
                        </div>
                    </tab-content>
                    <tab-content title="Phone Numbers" icon="fas fa-phone fa-rotate-90">
                        <h4 class="mt-4 mb-3" v-if="availableCallSources.length > 0">
                            <button class="btn pm-btn pm-btn-purple" type="button" v-b-modal.add-phone-modal>
                                <i class="fas fa-plus mr-2"></i>
                                Add Phone Number
                            </button>
                        </h4>
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <th>Number</th>
                                <th>Forward</th>
                                <th>Call Source</th>
                            </tr>
                            </thead>
                            <tbody v-if="phoneNumbers.length === 0">
                            <tr>
                                <td colspan="3">
                                    <div class="text-center text-danger font-weight-bold mt-4 mb-2">No Phone Numbers</div>
                                </td>
                            </tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="phone in phoneNumbers">
                                    <td><p>@{{ phone.phone_number }}</p></td>
                                    <td><p>@{{ phone.forward }}</p></td>
                                    <td><p>@{{ getCallSourceName(phone.call_source_name) }}</p></td>
                                </tr>
                            </tbody>
                        </table>
                    </tab-content>
                    <tab-content title="Features" icon="fas fa-cog">
                        <h4 class="mb-3">Additional Features</h4>
                        <div class="card mb-3 adf_crm_export-container">
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
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row no-gutters">
                                    <div class="col-12 col-md-6">
                                        <div class="feature-input">
                                            <p-check color="primary" :disabled="!campaign_has_mailer_phone" class="p-default" name="enable_text_to_value" v-model="campaignForm.enable_text_to_value">Enable Text To Value</p-check>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 feature-table-col" v-if="campaignForm.enable_text_to_value">
                                        <div class="feature-table">
                                            <form v-if="campaignForm.enable_text_to_value">
                                                <div class="alert alert-info mt-2">
                                                    <i class="fa fa-info-circle mr-2"></i>
                                                    Available placeholders: <span v-pre>@{{first_name}}, @{{last_name}}, @{{make}}, @{{model}}, @{{year}}, @{{text_to_value_amount}}</span>
                                                </div>
                                                <div class="mt-3 mb-0" v-if="campaignForm.enable_text_to_value">
                                                    <textarea name="text_to_value_message" class="form-control" required v-model="campaignForm.text_to_value_message"></textarea>
                                                </div>
                                            </form>
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
                                            <p-check color="primary" class="p-default" name="enable_call_center" v-model="campaignForm.enable_call_center">Enable Call Center</p-check>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 feature-table-col" v-if="campaignForm.enable_call_center">
                                        <div class="feature-table">
                                            <table class="table table-sm m-0">
                                                <thead>
                                                <tr>
                                                    <th>CloudOne Campaign ID</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="text" name="cloud_one_campaign_id" class="form-control" required v-model="campaignForm.cloud_one_campaign_id">
                                                        <div class="text-sm mt-2 invalid-feedback" :style="{display: campaignForm.errors.has('cloud_one_campaign_id') ? 'block' : 'none'}">
                                                            <div>CloudOne Campaign ID is required.</div>
                                                        </div>
                                                    </td>
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
                                            <p-check color="primary" class="p-default" name="enable_facebook_campaign" v-model="campaignForm.enable_facebook_campaign">Enable Facebook Campaign</p-check>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 feature-table-col" v-if="campaignForm.enable_facebook_campaign">
                                        <div class="feature-table">
                                            <table class="table table-sm m-0">
                                                <thead>
                                                <tr>
                                                    <th>Facebook Campaign ID</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="text" name="facebook_campaign_id" class="form-control" required v-model="campaignForm.facebook_campaign_id">
                                                        <div class="text-sm mt-2 invalid-feedback" :style="{display: campaignForm.errors.has('facebook_campaign_id') ? 'block' : 'none'}">
                                                            <div>Facebook Campaign ID is required.</div>
                                                        </div>
                                                    </td>
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
                                <p-radio color="primary" name="country" v-model="searchPhoneNumberForm.country" value="US">US</p-radio>
                                <p-radio color="primary" name="country" v-model="searchPhoneNumberForm.country" value="CA">CA</p-radio>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <input type="text" class="form-control" name="area_code" placeholder="Area Code" v-model="searchPhoneNumberForm.areaCode" @change="clearError(searchPhoneNumberForm)" :class="{'is-invalid': searchPhoneNumberForm.errors.has('area_code')}">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <input type="text" class="form-control" name="postal_code" placeholder="Zip" v-model="searchPhoneNumberForm.inPostalCode" @change="clearError(searchPhoneNumberForm)" :class="{'is-invalid': searchPhoneNumberForm.errors.has('postal_code')}">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <input type="text" class="form-control" name="contains" placeholder="Contains ex. Cars..." v-model="searchPhoneNumberForm.contains" @change="clearError(searchPhoneNumberForm)" :class="{'is-invalid': searchPhoneNumberForm.errors.has('contains')}">
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
            </div>
            <div class="card mt-3" v-if="showAvailablePhoneNumbers && availablePhoneNumbers.length > 0">
                <div class="card-body">
                    <h5>Available Phone Numbers</h5>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="phone_number">Phone Number</label>
                                <v-select name="phone_number" :options="availablePhoneNumbers" index="value" v-model="purchasePhoneNumberForm.phone_number" class="filter--v-select" @input="clearError(purchasePhoneNumberForm, 'phone_number')" :class="{'is-invalid': purchasePhoneNumberForm.errors.has('phone_number')}"></v-select>
                                <input-errors :error-bag="purchasePhoneNumberForm.errors" :field="'phone_number'"></input-errors>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="forward">Forward Number</label>
                                <input type="text" class="form-control" name="forward" v-model="purchasePhoneNumberForm.forward"></v-select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="call_source_name">Call Source</label>
                                <v-select name="call_source_name" :options="availableCallSources" index="name" v-model="purchasePhoneNumberForm.call_source_name" class="filter--v-select"></v-select>
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
