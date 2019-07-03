@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/deployments-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.campaignEditUrl = @json(route('campaigns.edit', ['campaign' => $campaign->id]));
        window.searchDropsUrl = "{{ route('campaigns.drops.for-user-display', ['campaign' => $campaign->id]) }}";
        window.deleteDropUrl = "{{ route('campaigns.drops.delete', ['campaign' => $campaign->id, 'drop' => ':dropId']) }}";
        window.dropIndexUrl = "{{ route('campaigns.drops.index', ['campaign' => $campaign->id]) }}";
        window.dropEditUrl = @json(route('campaigns.drops.edit', ['campaign' => $campaign->id, 'drop' => ':dropId']));
        window.dropRunSmsUrl = @json(route('campaigns.drops.details', ['campaign' => $campaign->id, 'drop' => ':dropId']));
        window.startDropUrl = @json(route('deployments.start', ['deployment' => ':dropId']));
    </script>
    <script src="{{ asset('js/deployments-index.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="drops-index" v-cloak>
        <div class="row align-items-end no-gutters">
            <div class="col-12 col-sm-5 col-lg-4 mb-3">
                @if (auth()->user()->isAdmin())
                <b-dropdown toggle-class="pm-btn pm-btn-purple">
                    <template slot="button-content">
                        <i class="fas fa-plus mr-2"></i> NEW
                    </template>
                    <b-dropdown-item href="{{ route('campaigns.mailer.create', ['campaign' => $campaign->id]) }}"><i class="fas fa-mail-bulk mr-2"></i>Mailer</b-dropdown-item>
                    <b-dropdown-item href="{{ route('campaigns.drops.create', ['campaign' => $campaign->id, 'type' => 'mail']) }}"><i class="fa fa-envelope mr-2"></i> Mail</b-dropdown-item>
                    <b-dropdown-item href="{{ route('campaigns.drops.create', ['campaign' => $campaign->id, 'type' => 'sms']) }}"><i class="fa fa-comment mr-2"></i>SMS</b-dropdown-item>
                </b-dropdown>
                @endif
            </div>
        </div>
        <div class="row align-items-end no-gutters">
            <div class="col-12 col-sm-5 col-lg-3 mb-3">
                <div class="form-group filter--form-group">
                    <label>Filter By Type</label>
                    <v-select :options="types" v-model="typeSelected" class="filter--v-select" @input="fetchData"></v-select>
                </div>
            </div>
            <div class="col-none col-sm-2 col-lg-5"></div>
            <div class="col-12 col-sm-5 col-lg-4 mb-3">
                <input type="text" v-model="searchDropForm.q" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" @keyup.enter="fetchData">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="table-loader-spinner" v-if="loading">
                    <spinner-icon></spinner-icon>
                </div>
                <div class="no-items-row" v-if="drops.length === 0">
                    No Items
                </div>
                <div class="drop" v-for="drop in drops">
                    <div class="row no-gutters">
                        <div class="col-12 col-md-4 drop-info">
                            <div class="drop-info--type">
                                <span>
                                    <i class="fa fa-envelope mr-2" v-if="drop.type === 'email'"></i>
                                    <i class="fa fa-comment mr-2" v-else-if="drop.type === 'sms'"></i>
                                    <i class="fas fa-mail-bulk mr-2" v-else-if="drop.type === 'mailer'"></i>
                                    <i class="pm-font-templates-icon mr-2" v-else></i>
                                    ID: @{{ drop.id }}
                                </span>
                            </div>
                            <div class="drop-info--date">
                                <span class="pm-font-date-icon mr-3"></span>
                                <span v-if="drop.type !== 'mailer'">@{{ (drop.status === 'Completed' ? drop.completed_at_formatted : drop.send_at_formatted) }}</span>
                                <span v-if="drop.type === 'mailer'">@{{ (drop.status === 'Completed' ? drop.completed_at_formatted : drop.send_at_formatted) }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 drop-status text-center">
                            <drop-status :status="drop.status"></drop-status>
                        </div>
                        <div class="col-6 col-md-3 drop-recipient">
                            <i class="pm-font-recipients-icon mr-3"></i> @{{ drop.recipients }} Recipients
                        </div>
                        <div class="col-6 col-md-2 drop-options">
                            <p v-if="drop.status === 'Deleted'" class="drop-options--no-actions">No Actions Available</p>
                            <div v-else>
                                <a v-if="drop.type === 'sms' && drop.status === 'Processing'" :href="generateRoute(dropRunSmsUrl, {'dropId': drop.id})" class="btn pm-btn pm-btn-green mr-2">
                                    RUN
                                </a>
                                <button type="button" v-if="drop.type === 'sms' && drop.status === 'Pending'" class="btn pm-btn pm-btn-green mr-2" @click="startDrop(drop)">
                                    START
                                </button>
                                <a :href="generateRoute(dropEditUrl, {'dropId': drop.id})" class="btn btn-link pm-btn-link pm-btn-link-primary">
                                    <i class="pm-font-edit-icon"></i>
                                </a>
                                <a href="javascript:;" v-if="drop.status !== 'Completed'" @click.prevent="deleteDrop(drop)" class="btn btn-link pm-btn-link pm-btn-link-warning">
                                    <i class="far fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <pm-pagination :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
            </div>
        </div>
    </div>
@endsection
