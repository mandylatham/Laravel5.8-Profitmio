@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/company-details.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.campaignEditUrl = "{{ route('campaigns.edit', ['campaign' => ':campaignId']) }}";
        window.campaignStatsUrl = "{{ route('campaigns.stats', ['campaign' => ':campaignId']) }}";
        window.appointmentsUrl = "{{ route('appointment.for-calendar-display') }}";
        window.campaignViewUrl = "{{ route('campaigns.view', ['campaign' => ':campaignId']) }}";
        window.campaignDropIndex = "{{ route('campaigns.drops.index', ['campaign' => ':campaignId']) }}";
        window.campaignRecipientIndex = "{{ route('campaigns.recipient-lists.index', ['campaign' => ':campaignId']) }}";
        window.campaignResponseConsoleIndex = "{{ route('campaign.response-console.index', ['campaign' => ':campaignId']) }}";
        window.searchCampaignFormUrl = "{{ route('campaign.for-user-display') }}";
        window.searchUserFormUrl = "{{ route('user.for-user-display') }}";
        window.userEditUrl = "{{ route('user.view', ['user' => ':userId']) }}";
        window.userImpersonateUrl = "{{ route('admin.impersonate', ['user' => ':userId']) }}";
        window.searchCampaignAccessUserUrl = "{{ route('user.for-user-display') }}";
        window.resendInvitationUrl = "{{ route('admin.resend-invitation') }}";
        window.toggleCampaignAccessUserUrl = "{{ route('campaigns.toggle-user-access', ['campaign' => ':campaignId', 'user' => ':userId']) }}";
        window.updateCompanyImageUrl = "{{ route('companies.update-avatar', ['company' => $company->id]) }}";
        @if (!auth()->user()->isAdmin())
            window.userActivateUrl = "{{ route('user.activate', ['user' => ':userId', 'company' => get_active_company()]) }}";
        window.userDeactivateUrl = "{{ route('user.deactivate', ['user' => ':userId', 'company' => get_active_company()]) }}";
        @endif
            window.company = @json($company);
        window.indexUrl = "{{ route('company.index') }}";
        window.updateUrl = "{{ route('company.update', ['company' => $company->id]) }}";
        window.isAdmin = @json(auth()->user()->isAdmin());
    </script>
    <script src="{{ asset('js/company-details.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="company-details" v-cloak>
        <a class="btn pm-btn pm-btn-blue go-back mb-3" href="{{ route('company.index') }}">
            <i class="fas fa-arrow-circle-left mr-2"></i> Go Back
        </a>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card profile mb-5">
                    <div class="card-body pb-5">
                        <div class="row no-gutters">
                            <div class="col-md-4 col-lg-3 col-xl-2 company-image-container mt-5">
                                <div class="company-avatar" v-if="!editImage"
                                     :style="{backgroundImage: 'url(' + company.image + ')'}">
                                    <span class="company-avatar--selector" @click="editImage = !editImage">
                                        <i class="fas fa-pencil-alt mr-2"></i>
                                    </span>
                                </div>
                                <resumable v-if="editImage" :target-url="targetUrl" ref="resumable"
                                           @file-added="onFileAdded"
                                           @file-success="onFileSuccess"></resumable>
                            </div>
                            <div class="col-md pl-3">
                                <div class="d-flex justify-content-end">
                                    <button dusk="edit-button" class="btn pm-btn pm-btn-outline-purple mb-3"
                                            @click="showCompanyFormControls = true" v-if="!showCompanyFormControls">
                                        <i class="fas fa-pencil-alt mr-2"></i>
                                        Edit
                                    </button>
                                </div>
                                <h1 v-if="!showCompanyFormControls" class="editable company-name">@{{ company.name
                                    }}</h1>
                                <div v-if="showCompanyFormControls">
                                    <div class="form-group">
                                        <h1><input id="name" name="name" class="form-control"
                                                   v-model="modifiedCompany.name" aria-label="Company Name"></h1>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label class="form-label">Country</label>
                                            <p v-if="!showCompanyFormControls"
                                               class="editable company-address form-control">@{{ company.country === 'us' ? 'United States' : 'Canada' }}</p>
                                            <select name="country" class="form-control"
                                                    v-model="modifiedCompany.country" aria-label="Company Country"
                                                    v-if="showCompanyFormControls">
                                                <option value="us">United States</option>
                                                <option value="ca">Canada</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Address</label>
                                            <p v-if="!showCompanyFormControls"
                                               class="editable company-address form-control">@{{ company.address }}</p>
                                            <input name="address" class="form-control" v-model="modifiedCompany.address"
                                                   aria-label="Company Address" v-if="showCompanyFormControls">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Address2</label>
                                            <p v-if="!showCompanyFormControls"
                                               class="editable company-address2 form-control">@{{ company.address2
                                                }}</p>
                                            <input name="address2" class="form-control"
                                                   v-model="modifiedCompany.address2" aria-label="Company Address2"
                                                   v-if="showCompanyFormControls">
                                        </div>
                                        <div class="row no-gutters">
                                            <div class="form-group col-12 col-lg-6">
                                                <label class="form-label">City</label>
                                                <p v-if="!showCompanyFormControls"
                                                   class="editable company-city form-control">@{{ company.city }}</p>
                                                <input name="city" class="form-control" v-model="modifiedCompany.city"
                                                       aria-label="Company City" v-if="showCompanyFormControls">
                                            </div>
                                            <div class="form-group col-6 col-lg-3">
                                                <label class="form-label">State</label>
                                                <p v-if="!showCompanyFormControls"
                                                   class="editable company-state form-control">@{{ company.state }}</p>
                                                <input name="state" class="form-control" v-model="modifiedCompany.state"
                                                       aria-label="Company State" v-if="showCompanyFormControls">
                                            </div>
                                            <div class="form-group col-6 col-lg-3">
                                                <label class="form-label">Zip</label>
                                                <p v-if="!showCompanyFormControls"
                                                   class="editable company-zip form-control">@{{ company.zip }}</p>
                                                <input name="zip" class="form-control" v-model="modifiedCompany.zip"
                                                       aria-label="Company Zip" v-if="showCompanyFormControls">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label class="form-label">Phone</label>
                                            <p v-if="!showCompanyFormControls"
                                               class="editable company-address form-control">@{{ company.phone }}</p>
                                            <input name="phone" class="form-control" v-model="modifiedCompany.phone"
                                                   aria-label="Company Phone" v-if="showCompanyFormControls">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Company Website</label>
                                            <p v-if="!showCompanyFormControls"
                                               class="editable company-address2 form-control">@{{ company.url }}</p>
                                            <input name="url" class="form-control" v-model="modifiedCompany.url"
                                                   aria-label="Company Website" v-if="showCompanyFormControls">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Facebook</label>
                                            <p v-if="!showCompanyFormControls"
                                               class="editable company-address2 form-control">@{{ company.facebook
                                                }}</p>
                                            <input name="facebook" class="form-control"
                                                   v-model="modifiedCompany.facebook" aria-label="Company Facebook"
                                                   v-if="showCompanyFormControls">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Twitter</label>
                                            <p v-if="!showCompanyFormControls"
                                               class="editable company-address2 form-control">@{{ company.twitter }}</p>
                                            <input name="twitter" class="form-control" v-model="modifiedCompany.twitter"
                                                   aria-label="Company Twitter" v-if="showCompanyFormControls">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row no-gutters">
                            <div class="col-12 form-controls">
                                <div class="form-group" v-if="showCompanyFormControls">
                                    <button dusk="save-company-button" class="btn btn-sm btn-outline-primary mr-1" type="button"
                                            @click="saveCompanyForm()">
                                        Save
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                            @click="cancelCompanyForm()">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <b-card no-body>
                    <b-tabs card>
                        <b-tab title="CAMPAIGNS" active>
                            @if($hasCampaigns)
                                <div class="row no-gutters mb-md-3">
                                    <div class="col-12 col-sm-5 col-lg-4 offset-sm-7 offset-lg-8">
                                        <input type="text" v-model="searchCampaignForm.q"
                                               class="form-control filter--search-box" aria-describedby="search"
                                               placeholder="Search" @keyup.enter="fetchCampaigns">
                                    </div>
                                </div>
                            @endif
                            <div class="row no-gutters mt-3">
                                <div class="col-12">
                                    <div class="loader-spinner table-loader-spinner" v-if="loadingCampaigns">
                                        <spinner-icon></spinner-icon>
                                    </div>
                                    <div class="no-items-row"
                                         v-if="countActiveCampaigns === 0 && countInactiveCampaigns === 0">
                                        No Items
                                    </div>
                                    <div class="campaign-group-label" v-if="countActiveCampaigns > 0">ACTIVE</div>
                                    <campaign v-for="campaign in campaigns" v-if="campaign.status === 'Active'"
                                              :key="campaign.id" :campaign="campaign"></campaign>
                                    <div class="campaign-group-label" v-if="countInactiveCampaigns > 0">INACTIVE</div>
                                    <campaign v-for="campaign in campaigns" v-if="campaign.status !== 'Active'"
                                              :key="campaign.id" :campaign="campaign"></campaign>
                                    @if($hasCampaigns)
                                        <pm-pagination :pagination="campaignsPagination"
                                                       @page-changed="onCampaignPageChanged"></pm-pagination>
                                    @endif
                                </div>
                            </div>
                        </b-tab>
                        <b-tab title="USERS">
                            <div class="row no-gutters mb-md-4">
                                <div class="col-12 col-sm-5 col-lg-4">
                                    <a dusk="add-user-button" class="btn pm-btn pm-btn-outline-purple float-left"
                                       href="{{ route('user.create', ['company' => $company->id]) }}"><i class="fas fa-plus mr-3"></i>New User</a>
                                </div>
                                <div class="col-none col-sm-2 col-lg-4"></div>
                                <div class="col-12 col-sm-5 col-lg-4">
                                    <input type="text" v-model="searchUserForm.q"
                                           class="form-control filter--search-box" aria-describedby="search"
                                           placeholder="Search" @keyup.enter="fetchUsers">
                                </div>
                            </div>
                            <div class="row no-gutters mt-3">
                                <div class="col-12">
                                    <div class="loader-spinner table-loader-spinner" v-if="loadingUsers">
                                        <spinner-icon></spinner-icon>
                                    </div>
                                    <div class="no-items-row" v-if="users.length === 0">
                                        No Items
                                    </div>
                                    <div class="user-row" v-for="user in users">
                                        <div class="row no-gutters">
                                            <div class="col-12 col-md-8 col-xl-3">
                                                <div
                                                    class="user-row--id justify-content-center justify-content-xl-start">
                                                    <strong class="mr-2">ID: @{{ user.id }}</strong>
                                                    <span v-if="user.first_name && user.last_name"
                                                        class="user-name">@{{ user.first_name }} @{{ user.last_name }}</span>
                                                    <span class="user-name" v-if="!user.first_name && !user.last_name">No Name</span>
                                                    <user-role class="ml-3" :role="'site_admin'"
                                                               v-if="user.is_admin"></user-role>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4 col-xl-2">
                                                <div
                                                    class="user-row--companies justify-content-center justify-content-xl-start">
                                                    <div>Active Companies <span class="ml-2 counter">@{{ user.active_companies }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                                <div
                                                    class="user-row--email justify-content-center justify-content-xl-start">
                                                    <i class="fas fa-envelope mr-2"></i>@{{ user.email || '--' }}
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-4 col-xl-2">
                                                <div
                                                    class="user-row--phone-number justify-content-center justify-content-xl-start">
                                                    <span class="pm-font-phone-icon mr-2"></span>@{{ user.phone
                                                    || '--' }}
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4 col-xl-2">
                                                <div
                                                    class="user-row--options justify-content-center align-items-xl-start">
                                                    <a :href="generateRoute(userEditUrl, {'userId': user.id})"
                                                       class="btn btn-link pm-btn-link pm-btn-link-warning"
                                                       title="Edit">
                                                        <i class="far fa-eye mr-3"></i> View
                                                    </a>
                                                    <a :href="generateRoute(userImpersonateUrl, {'userId': user.id})"
                                                       class="btn btn-link pm-btn-link pm-btn-link-blue"
                                                       title="Impersonate" v-if="user.has_active_companies">
                                                        <i class="fas fa-lock-open mr-3"></i> Impersonate
                                                    </a>
                                                    <button :disabled="loadingInvitation"
                                                       class="btn btn-link pm-btn-link pm-btn-link-black"
                                                       v-if="!user.is_profile_ready" @click="resendInvitation(user)">
                                                        <span v-if="!loadingInvitation"><i class="fas fa-envelope mr-3"></i>Resend Invitation</span>
                                                        <spinner-icon :size="'sm'" v-if="loadingInvitation"></spinner-icon>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <pm-pagination :pagination="usersPagination"
                                                   @page-changed="onUserPageChanged"></pm-pagination>
                                </div>
                            </div>
                        </b-tab>
                        <b-tab title="CAMPAIGN ACCESS" id="campaign-access-tab">
                            @if($hasCampaigns)
                                <div class="row no-gutters mb-md-3">
                                    <div class="col-12 col-sm-5 col-lg-4 offset-sm-7 offset-lg-8">
                                        <input type="text" v-model="searchCampaignForm.q"
                                               class="form-control filter--search-box" aria-describedby="search"
                                               placeholder="Search" @keyup.enter="fetchCampaigns">
                                    </div>
                                </div>
                            @endif
                            <div class="loader-spinner table-loader-spinner" v-if="loadingCampaigns">
                                <spinner-icon></spinner-icon>
                            </div>
                            <div class="no-items-row" v-if="countActiveCampaigns === 0 && countInactiveCampaigns === 0">
                                No Items
                            </div>
                            <div class="company-access-row" v-for="campaign in campaigns">
                                <strong class="mr-3">Campaign @{{ campaign.id }}</strong>
                                <span>@{{ campaign.name }}</span>
                                <span class="access-link">
                                    <a class="btn pm-btn pm-btn-purple" href="javascript:;"
                                       @click="openCampaignAccessModal(campaign)">Configure Access</a>
                                </span>
                            </div>
                            @if($hasCampaigns)
                                <pm-pagination class="mt-3" :pagination="campaignsPagination"
                                               @page-changed="onCampaignPageChanged"></pm-pagination>
                            @endif
                        </b-tab>
                    </b-tabs>
                </b-card>
            </div>
        </div>
        <b-modal ref="configureAccessModal" size="lg" id="configure-access" hide-footer>
            <template slot="modal-header">
                <h4>Campaign Access</h4>
                <span class="close-modal-header float-right" @click="closeModal">
                    <i class="fas fa-times float-right"></i>
                </span>
            </template>
            <div class="card">
                <div class="card-body">
                    <input type="text" v-model="searchCampaignAccessUser.q" class="form-control mb-3 filter--search-box"
                           aria-describedby="search"
                           placeholder="Search" @keyup.enter="fetchUsersForCampaignAccess(campaignSelected)">
                    <div class="loader-spinner table-loader-spinner" v-if="loadingCampaignAccessUsers">
                        <spinner-icon></spinner-icon>
                    </div>
                    <div class="no-items-row" v-if="usersForCampaignAccess.length === 0">
                        No Items
                    </div>
                    <table class="table table-bordered table-sm" v-if="usersForCampaignAccess.length > 0">
                        <thead>
                        <th></th>
                        <th>User</th>
                        <th class="text-center">Status</th>
                        </thead>
                        <tbody>
                        <tr v-for="user in usersForCampaignAccess">
                            <td width="20%" class="align-middle text-center">
                                <p-check color="primary" class="p-default p-fill p-switch" v-model="user.has_access"
                                         @change="toggleCampaignAccess(user)">&nbsp;
                                </p-check>
                            </td>
                            <td class="align-middle user-row">
                                <div v-if="user.first_name || user.last_name">@{{ user.first_name }} @{{ user.last_name }}</div>
                                <div v-else>No Name <span class="badge badge-warning" v-if="user.has_pending_invitations">Pending Invitation</span></div>
                                <small>@{{ user.email }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-warning" v-if="user.has_pending_invitations">Has Pending Invitations</span>
                                <user-status :is-active="user.is_active" v-else></user-status>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <pm-pagination :pagination="campaignAccessUsersPagination"
                                   @page-changed="onCampaignAccessUserPageChanged"></pm-pagination>
                </div>
            </div>
        </b-modal>
    </div>
@endsection
