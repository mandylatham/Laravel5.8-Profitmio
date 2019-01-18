@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/deployments-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchDropsUrl = "{{ route('campaigns.drops.for-user-display', ['campaign' => $campaign->id]) }}";
        window.deleteDropUrl = "{{ route('campaigns.drops.delete', ['campaign' => $campaign->id, 'drop' => ':dropId']) }}";
        window.dropIndexUrl = "{{ route('campaigns.drops.index', ['campaign' => $campaign->id]) }}";
    </script>
    <script src="{{ asset('js/deployments-index.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="drops-index">
        <div class="row align-items-end no-gutters mb-md-3">
            <div class="col-12 col-sm-5 col-lg-4">
                <a class="btn pm-btn pm-btn-blue" href="{{ route('campaigns.create') }}">
                    <i class="fas fa-plus mr-2"></i> NEW
                </a>
            </div>
            <div class="col-none col-sm-2 col-lg-4"></div>
            <div class="col-12 col-sm-5 col-lg-4">
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
                                <span class="pm-font-mail-icon" v-if="drop.ype === 'email'"></span>
                                <span class="pm-font-sms-icon" v-else-if="drop.ype === 'sms'"></span>
                                <span class="pm-font-templates-icon" v-else></span>
                            </div>
                            <div class="drop-info--date">
                                <span class="pm-font-date-icon mr-3"></span>@{{ (drop.status === 'Completed' ? drop.completed_at : drop.send_at) | amDateTimeFormat('MM/DD/YYYY | H:mm A') }}
                            </div>
                        </div>
                        <div class="col-4 col-md-3 drop-status">
                            <drop-status :status="drop.status"></drop-status>
                        </div>
                        <div class="col-4 col-md-3 drop-recipient">
                            <i class="pm-font-recipients-icon mr-3"></i> Recipients @{{ drop.recipients }}
                        </div>
                        <div class="col-4 col-md-2 drop-options">
                            <p v-if="drop.status === 'Completed' || drop.status === 'Cancelled' || drop.status === 'Processing' || drop.status === 'Deleted'" class="drop-options--no-actions">No Actions Available</p>
                            <div v-else class="justify-content-center align-items-xl-start">
                                <a :href="generateRoute(dropEditUrl, {'dropId': drop.id})" class="btn btn-link pm-btn-link pm-btn-link-primary">
                                    <i class="pm-font-edit-icon mr-3"></i> Edit
                                </a>
                                <a href="javascript:;" @click.prevent="deleteDrop(drop)" class="btn btn-link pm-btn-link pm-btn-link-warning">
                                    <i class="far fa-trash-alt mr-3"></i> Delete
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
