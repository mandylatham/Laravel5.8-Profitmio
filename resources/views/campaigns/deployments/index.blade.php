@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/deployments-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchDropsUrl = "{{ route('campaigns.drops.for-user-display', ['campaign' => $campaign->id]) }}";
        window.deleteDropUrl = "{{ route('campaigns.drops.delete', ['campaign' => $campaign->id, 'drop' => ':dropId']) }}";
        window.dropIndexUrl = "{{ route('campaigns.drops.index', ['campaign' => $campaign->id]) }}";
        window.dropEditUrl = @json(route('campaigns.drops.edit', ['campaign' => $campaign->id, 'drop' => ':dropId']));
        window.dropRunSmsUrl = @json(route('campaigns.drops.details', ['campaign' => $campaign->id, 'drop' => ':dropId']));
    </script>
    <script src="{{ asset('js/deployments-index.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="drops-index" v-cloak>
        <div class="row align-items-end no-gutters">
            <div class="col-12 col-sm-5 col-lg-4 mb-3">
                @if (auth()->user()->isAdmin())
                <a class="btn pm-btn pm-btn-blue" href="{{ route('campaigns.drops.create', ['campaign' => $campaign->id]) }}">
                    <i class="fas fa-plus mr-2"></i> NEW
                </a>
                @endif
            </div>
            <div class="col-none col-sm-2 col-lg-4"></div>
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
                                <span class="pm-font-mail-icon" v-if="drop.type === 'email'"></span>
                                <span class="fa fa-comment" v-else-if="drop.type === 'sms'"></span>
                                <span class="pm-font-templates-icon" v-else></span>
                            </div>
                            <div class="drop-info--date">
                                <span class="pm-font-date-icon mr-3"></span>@{{ (drop.status === 'Completed' ? drop.completed_at : drop.send_at) | amDateTimeFormat('MM/DD/YYYY | H:mm A') }}
                            </div>
                        </div>
                        <div class="col-12 col-md-3 drop-status text-center">
                            <drop-status :status="drop.status"></drop-status>
                        </div>
                        <div class="col-6 col-md-3 drop-recipient">
                            <i class="pm-font-recipients-icon mr-3"></i> @{{ drop.recipients }} Recipients
                        </div>
                        <div class="col-6 col-md-2 drop-options">
                            <p v-if="drop.status === 'Completed' || drop.status === 'Cancelled' || drop.status === 'Processing' || drop.status === 'Deleted'" class="drop-options--no-actions">No Actions Available</p>
                            <div v-else>
                                <a v-if="drop.type === 'sms'" :href="generateRoute(dropRunSmsUrl, {'dropId': drop.id})" class="btn pm-btn pm-btn-green mr-2">
                                    RUN
                                </a>
                                <a :href="generateRoute(dropEditUrl, {'dropId': drop.id})" class="btn btn-link pm-btn-link pm-btn-link-primary">
                                    <i class="pm-font-edit-icon"></i>
                                </a>
                                <a href="javascript:;" @click.prevent="deleteDrop(drop)" class="btn btn-link pm-btn-link pm-btn-link-warning">
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
