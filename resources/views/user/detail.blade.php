@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/user-detail.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.getCompanyUrl = @json(route('company.for-dropdown'));
        window.searchCampaignFormUrl = "{{ route('campaign.for-user-display') }}";
        window.searchCompaniesFormUrl = "{{ route('company.for-user-display') }}";
        window.updateUserUrl = "{{ route('user.update', ['user' => ':userId']) }}";
        window.updatePasswordUrl = @json(route('user.update-password', ['user' => $user->id]));
        window.timezones = @json($timezones);
        window.updateCompanyDataUrl = "{{ route('user.update-company-data', ['user' => ':userId']) }}";
        window.user = @json($user);
        window.deleteUserUrl = "{{ route('user.delete', ['user' => $user->id]) }}";
        window.updateUserPhotoUrl = "{{ route('user.update-avatar', ['user' => $user->id]) }}";
        window.campaignEditUrl = "{{ route('campaigns.edit', ['campaign' => ':campaignId']) }}";
        window.campaignStatsUrl = "{{ route('campaigns.stats', ['campaign' => ':campaignId']) }}";
        window.campaignDropIndex = "{{ route('campaigns.drops.index', ['campaign' => ':campaignId']) }}";
        window.campaignRecipientIndex = "{{ route('campaigns.recipient-lists.index', ['campaign' => ':campaignId']) }}";
        window.campaignResponseConsoleIndex = "{{ route('campaign.response-console.index', ['campaign' => ':campaignId']) }}";
        window.isAdmin = @json(auth()->user()->isAdmin());
        window.resendInvitationUrl = "{{ route('admin.resend-invitation') }}";
        @if (auth()->user()->isAdmin())
            window.userRole = 'site_admin';
        @else
            window.userRole = @json($userRole);
        @endif
        window.userIndexUrl = "{{ route('user.index') }}";
    </script>
    <script src="{{ asset('js/user-detail.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="user-view" v-cloak>
        <a class="btn pm-btn pm-btn-blue go-back mb-3" href="{{ $userRole == 'user' ? route('dashboard') : route('user.index') }}">
            <i class="fas fa-arrow-circle-left mr-2"></i> Go Back
        </a>
        <div class="card profile mb-5">
            <div class="card-body pb-5">
                <div class="row no-gutters">
                    <div class="col-12 col-md-4 col-lg-3 col-xl-2 company-image-container">
                        <div class="user-avatar" v-if="!editImage"
                             :style="{backgroundImage: 'url(' + originalUser.image_url + ')'}">
                            <span class="user-avatar--selector" v-if="showUserFormControls" @click="editImage = !editImage">
                                <i class="fas fa-pencil-alt mr-2"></i>
                            </span>
                        </div>
                        <resumable v-if="editImage" :target-url="targetUrl" ref="resumable" @file-added="onFileAdded"
                                   @file-success="onFileSuccess"></resumable>
                    </div>
                    <div class="col-12 col-md-8 col-lg-9 col-xl-10">
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn pm-btn pm-btn-outline-purple mr-2" type="button" v-b-modal.passwordModal>
                                <i class="fas fa-key mr-2"></i>
                                Change Password
                            </button>
                            <button class="btn pm-btn pm-btn-outline-purple" @click="showUserFormControls = true"
                                    v-if="!showUserFormControls">
                                <i class="fas fa-pencil-alt mr-2"></i>
                                Edit
                            </button>
                        </div>
                        <form @submit.prevent="saveUser">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <p v-if="!showUserFormControls" class="editable form-control">@{{
                                            originalUser.first_name }}</p>
                                        <input name="first_name" tabindex="1" class="form-control"
                                               v-model="user.first_name" aria-label="First name"
                                               v-if="showUserFormControls">
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email</label>
                                        <p v-if="!showUserFormControls" class="editable form-control">@{{
                                            originalUser.email }}</p>
                                        <input name="email" tabindex="3" class="form-control" disabled
                                               v-model="user.email" aria-label="Email" v-if="showUserFormControls">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <p v-if="!showUserFormControls" class="editable form-control">@{{
                                            originalUser.last_name }}</p>
                                        <input name="last_name" tabindex="2" class="form-control"
                                               v-model="user.last_name" aria-label="Last name"
                                               v-if="showUserFormControls">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Phone</label>
                                        <p v-if="!showUserFormControls" class="editable company-address form-control">
                                            @{{ originalUser.phone_number }}</p>
                                        <input name="phone" tabindex="4" class="form-control"
                                               v-model="user.phone_number" aria-label="Company Phone"
                                               v-if="showUserFormControls">
                                    </div>
                                </div>
                                <div class="col-12 form-controls">
                                    <div class="form-group" v-if="showUserFormControls">
                                        <button class="btn btn-sm btn-outline-primary mr-1" type="submit">
                                            Save
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                @click="cancelUser">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <b-modal ref="passwordModalRef" title="Change Password" id="passwordmodal" size="sm">
                            <template slot="modal-header">
                                <h4>Change Your Password</h4>
                                <span class="close-modal-header float-right" @click="closeModal('passwordModalRef')">
                                    <i class="fas fa-times float-right"></i>
                                </span>
                            </template>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <form action="" method="" autocomplete="off">
                                                <div class="form-group">
                                                    <label for="old_password" class="form-label">Current Password</label>
                                                    <input type="password" name="old_password" class="form-control" 
                                                        autocomplete="false" v-model="updatePasswordForm.old_password">
                                                    <div v-if="updatePasswordForm.errors.has('old_password')">
                                                        <p class="text-danger">@{{ updatePasswordForm.errors.get('old_password')[0] }}</p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="password" class="form-label">New Password</label>
                                                    <input type="password" name="password" class="form-control" 
                                                        autocomplete="false" v-model="updatePasswordForm.password">
                                                    <div v-if="updatePasswordForm.errors.has('password')">
                                                        <p class="text-danger">@{{ updatePasswordForm.errors.get('password')[0] }}</p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                                    <input type="password" name="password_confirmation" class="form-control" 
                                                        autocomplete="false" v-model="updatePasswordForm.password_confirmation">
                                                    <div v-if="updatePasswordForm.errors.has('password_confirmation')">
                                                        <p class="text-danger">@{{ updatePasswordForm.errors.get('password_confirmation')[0] }}</p>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <template slot="modal-footer">
                                <button class="btn pm-btn pm-btn-purple" @click="updatePassword">Update</button>
                            </template>
                        </b-modal>
                    </div>
                </div>
                <div class="row">
                </div>
            </div>
        </div>
        <b-card no-body>
            <b-tabs card>
                <b-tab title="CAMPAIGN" active>
                    @if($hasCampaigns)
                        <div class="row align-items-end no-gutters mb-md-3">
                            <div class="col-12 col-sm-5 col-lg-4">
                                <div class="form-group filter--form-group">
                                    <label>Filter By Company</label>
                                    <v-select :options="companies" label="name" v-model="campaignCompanySelected"
                                              class="filter--v-select" @input="onCampaignCompanySelected"></v-select>
                                </div>
                            </div>
                            <div class="col-none col-sm-2 col-lg-4"></div>
                            <div class="col-12 col-sm-5 col-lg-4">
                                <input type="text" v-model="searchCampaignForm.q"
                                       class="form-control filter--search-box" aria-describedby="search"
                                       placeholder="Search" @keyup.enter="fetchCampaigns">
                            </div>
                        </div>
                    @endif
                    <div class="row align-items-end no-gutters mt-3">
                        <div class="col-12">
                            <div class="loader-spinner" v-if="loadingCampaigns">
                                <spinner-icon></spinner-icon>
                            </div>
                            <div class="no-items-row" v-if="countActiveCampaigns === 0 && countInactiveCampaigns === 0">
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
                <b-tab title="COMPANY">
                    <div class="row align-items-end no-gutters mb-md-4">
                        <div class="col-12 col-sm-5 col-lg-4">
                        </div>
                        <div class="col-none col-sm-2 col-lg-4"></div>
                        <div class="col-12 col-sm-5 col-lg-4">
                            <input type="text" v-model="searchCompanyForm.q" class="form-control filter--search-box"
                                   aria-describedby="search"
                                   placeholder="Search" @keyup.enter="fetchCompanies">
                        </div>
                    </div>
                    <div class="row align-items-end no-gutters mt-3">
                        <div class="col-12">
                            <div class="loader-spinner" v-if="loadingCompanies">
                                <spinner-icon></spinner-icon>
                            </div>
                            <div class="no-items-row" v-if="countCompanies === 0">
                                No Items
                            </div>
                            <div class="company" v-for="company in companiesForList">
                                <div class="row no-gutters">
                                    <div class="col-12 col-md-4 company-info">
                                        <div class="company-info--image">
                                            <div class="ci-img" :style="{backgroundImage: company.image_url}"></div>
                                        </div>
                                        <div class="company-info--data">
                                            <strong>@{{ company.name }}</strong>
                                            <p>@{{ company.address }}</p>
                                        </div>
                                    </div>
                                    <div class="col-4 col-md-3 company-role">
                                        @if (!$user->isAdmin())
                                            <v-select :options="roles" :disabled="!canEditCompanyRole(company)"
                                                      v-model="company.role" class="filter--v-select"
                                                      @input="updateCompanyData(company)" :clearable="false">
                                                <template slot="selected-option" slot-scope="option">
                                                    @{{ option.label | userRole }}
                                                </template>
                                                <template slot="option" slot-scope="option">
                                                    @{{ option.label | userRole }}
                                                </template>
                                            </v-select>
                                        @else
                                            <user-role :short-version="false" :role="'site_admin'"></user-role>
                                        @endif
                                    </div>
                                    <div class="col-4 col-md-3 company-timezone">
                                        <v-select :options="timezones" :disabled="!canEditCompanyTz(company)"
                                                  v-model="company.timezone" class="filter--v-select"
                                                  @input="updateCompanyData(company)" :clearable="false">
                                            <template slot="selected-option" slot-scope="option">
                                                @{{ option.label }}
                                            </template>
                                            <template slot="option" slot-scope="option">
                                                @{{ option.label }}
                                            </template>
                                        </v-select>
                                    </div>
                                    <div class="col-4 col-md-2 company-active-campaigns"
                                         v-if="company.is_profile_ready">
                                        <small>Active Campaigns</small>
                                        <div>
                                            <span class="pm-font-campaigns-icon"></span>
                                            <span class="company-active-campaigns--counter">@{{ company.active_campaigns_for_user }}</span>
                                        </div>
                                    </div>
                                    <div class="col-4 col-md-2 company-resend-invitation"
                                         v-if="!company.is_profile_ready">
                                        <button class="btn pm-btn pm-btn-purple" :disabled="loadingInvitation"
                                                type="button" @click="resendInvitation(company)">
                                            <span v-if="!loadingInvitation"><i class="fas fa-envelope mr-3"></i>Resend Invitation</span>
                                            <spinner-icon :size="'sm'" class="white" v-if="loadingInvitation"></spinner-icon>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <pm-pagination :pagination="companiesPagination"
                                           @page-changed="onCompanyPageChanged"></pm-pagination>
                        </div>
                    </div>
                </b-tab>
            </b-tabs>
        </b-card>
    </div>
@endsection
