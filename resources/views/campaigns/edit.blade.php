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
        window.searchPhoneUrl = @json(route('phone.search'));
        window.provisionPhoneUrl = @json(route('phone.provision'));
        window.getCampaignPhonesUrl = @json(route('phone.list', ['campaign' => $campaign->id]));
        window.savePhoneNumberUrl = @json(route('phone.store', ['campaign' => $campaign->id, 'phone' => ':phone_number_id']));
    </script>
    <script src="{{ asset('js/campaigns-edit.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="campaigns-edit" v-cloak>
        <a class="btn pm-btn pm-btn-blue go-back mb-3" href="{{ auth()->user()->isAdmin() ? route('campaigns.index') : route('dashboard') }}">
            <i class="fas fa-arrow-circle-left mr-2"></i> Go Back
        </a>
        <div class="row">
            <div class="col-12">
                <b-card no-body>
                    <b-tabs card>
                        <b-tab title="BASICS" active>
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
                            <button type="button" class="btn pm-btn pm-btn-purple mt-3" @click="saveCampaign">
                                <span v-if="!loading">Save</span>
                                <spinner-icon class="white" :size="'xs'" v-if="loading"></spinner-icon>
                            </button>
                        </b-tab>
                        <b-tab title="ACCOUNTS">
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
                            <button type="button" class="btn pm-btn pm-btn-purple mt-3" @click="saveCampaign">
                                <span v-if="!loading">Save</span>
                                <spinner-icon class="white" :size="'xs'" v-if="loading"></spinner-icon>
                            </button>
                        </b-tab>
                        <b-tab title="PHONE NUMBERS">
                            <h4 class="mt-4 mb-3"><button class="btn pm-btn pm-btn-purple" type="button" v-b-modal.add-phone-modal><i class="fas fa-plus mr-2"></i>Add Phone Number</button>
                            </h4>
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th>Number</th>
                                    <th>Forward</th>
                                    <th>Call Source</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody v-if="campaignPhones.length === 0">
                                <tr>
                                    <td colspan="4">
                                        <div class="text-center text-danger font-weight-bold mt-4 mb-2">No Phone Numbers</div>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-for="phone in campaignPhones">
                                        <td><p class="form-control">@{{ phone.phone_number }}</p></td>
                                        <td>
                                            <p class="editable form-control" v-if="!showPhoneNumberForm[phone.id]" @click="enablePhoneNumberForm(phone)">@{{ phone.forward }}</p>
                                            <input type="text" class="form-control" v-model="editPhoneNumberForm[phone.id].forward" v-if="showPhoneNumberForm[phone.id]">
                                        </td>
                                        <td>
                                            <p class="editable form-control" v-if="!showPhoneNumberForm[phone.id]" @click="enablePhoneNumberForm(phone)">@{{ getCallSourceName(phone.call_source_name) }}</p>
                                            <v-select class="filter--v-select" index="name" v-model="editPhoneNumberForm[phone.id].call_source_name" :options="availableCallSourcesWithCurrent(phone.call_source_name)" v-if="showPhoneNumberForm[phone.id]"></v-select>
                                        </td>
                                        <td>
                                            <button role="button" class="btn pm-btn btn-outline-purple" v-if="false" @click="editPhoneNumber(phone)">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button role="button" class="btn pm-btn pm-btn-purple" v-if="showPhoneNumberForm[phone.id]" @click="savePhoneNumber(phone)">
                                                <i class="fa fa-save"></i>
                                            </button>
                                            <button role="button" class="btn pm-btn btn-outline-default" v-if="showPhoneNumberForm[phone.id]" @click="cancelPhoneNumber(phone)">
                                                Cancel
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </b-tab>
                        <b-tab title="CONTACT">
                            <h4 class="mb-3">Additional Features</h4>
                            <div class="card mb-3 card-feature">
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
                            <div class="card mb-3 card-feature">
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
                            <div class="card mb-3 card-feature">
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
                            <div class="card mb-3 card-feature">
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
                            <div class="card mb-3 card-feature">
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
                            <button type="button" class="btn pm-btn pm-btn-purple mt-3" @click="saveCampaign">
                                <span v-if="!loading">Save</span>
                                <spinner-icon class="white" :size="'xs'" v-if="loading"></spinner-icon>
                            </button>
                        </b-tab>
                    </b-tabs>
                </b-card>
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
                                <input type="text" class="form-control" name="area_code" placeholder="Area Code" v-model="searchPhoneNumberForm.areaCode" @change="clearError(searchPhoneNumberForm)" :class="{'is-invalid': searchPhoneNumberForm.errors.has('area_code')}">
                                <input-errors :error-bag="searchPhoneNumberForm.errors" :field="'area_code'"></input-errors>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <input type="text" class="form-control" name="postal_code" placeholder="Zip" v-model="searchPhoneNumberForm.inPostalCode" @change="clearError(searchPhoneNumberForm)" :class="{'is-invalid': searchPhoneNumberForm.errors.has('postal_code')}">
                                <input-errors :error-bag="searchPhoneNumberForm.errors" :field="'postal_code'"></input-errors>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <input type="text" class="form-control" name="contains" placeholder="Contains ex. Cars..." v-model="searchPhoneNumberForm.contains" @change="clearError(searchPhoneNumberForm)" :class="{'is-invalid': searchPhoneNumberForm.errors.has('contains')}">
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
                                <input type="text" class="form-control" size="16" name="forward" v-model="purchasePhoneNumberForm.forward"></v-select>
                                <input-errors :error-bag="purchasePhoneNumberForm.errors" :field="'forward'"></input-errors>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="call_source">Call Source</label>
                                <v-select name="call_source" :options="availableCallSources" index="name" v-model="purchasePhoneNumberForm.call_source_name" class="filter--v-select"></v-select>
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
