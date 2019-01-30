@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/recipients-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchRecipientsUrl = @json(route('campaigns.recipient-lists.for-user-display', ['campaign' => $campaign->id]));
        window.uploadRecipientsUrl = @json(route('campaigns.recipient-lists.upload', ['campaign' => $campaign->id]));
        window.downloadRecipientListUrl = @json(route('campaigns.recipient-lists.download', ['campaign' => $campaign->id, 'list' => ':listId']));
        window.saveRecipientsUrl = @json(route('campaigns.recipient-lists.store', ['campaign' => $campaign->id]));
        window.recipientsIndexUrl = @json(route('campaigns.recipient-lists.index', ['campaign' => $campaign->id]));
        window.deleteRecipientUrl = @json(route('campaigns.recipient-lists.delete', ['campaign' => $campaign->id, 'list' => ':listId']));
        window.showRecipientListUrl = @json(route('campaigns.recipient-lists.show', ['campaign' => $campaign->id, 'list' => ':listId']));
        window.recipientListDeleteStatsUrl = @json(route('campaigns.recipient-lists.delete-stats', ['campaign' => $campaign->id, 'list' => ':listId']));
    </script>
    <script src="{{ asset('js/recipients-index.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="recipients-index" v-cloak>
        <div class="row align-items-end no-gutters mb-3">
            <div class="col-12 col-sm-5 col-lg-4">
                <a class="btn pm-btn pm-btn-blue" href="{{ route('campaigns.index') }}">
                    <i class="fas fa-chevron-circle-left mr-2"></i> Back
                </a>
            </div>
            <div class="col-none col-sm-2 col-lg-4"></div>
            <div class="col-12 col-sm-5 col-lg-4">
                <a class="btn pm-btn pm-btn-blue float-right" v-b-modal.upload-recipient-modal href="javascript:;">
                    <i class="fas fa-plus mr-2"></i> UPLOAD NEW LIST
                </a>
            </div>
        </div>
        <div class="recipients-container">
            <div class="table-loader-spinner" v-if="loading">
                <spinner-icon></spinner-icon>
            </div>
            <div class="no-items-row" v-if="recipientList.length === 0">
                No Items
            </div>
            <div class="recipient mb-4" v-for="row in recipientList">
                <div class="row no-gutters">
                    <div class="col-12 col-sm-6 col-lg-4 recipient-name">
                        <div class="recipient-name--icon">
                            <span class="pm-font-system-icon"></span>
                        </div>
                        <div class="recipient-name--data">
                            <strong>@{{ row.name }}</strong>
                            <small>Database List</small>
                            <div class="recipient-name--id">List File ID: @{{ row.id }}</div>
                        </div>
                    </div>
                    {{--@if ($list->recipients_added)--}}
                        {{--<div class="col-md-3 col-sm-4 list-details">--}}
                            {{--<div class="text-primary">--}}
                                {{--<div class="list-count"><i class="ml-2 mr-2 icon fa fa-users"></i> Total</div>--}}
                                {{--<div class="list-count count">{{ $list->recipients->count() }}</div>--}}
                            {{--</div>--}}
                            {{--<div>--}}
                                {{--<div class="list-count"><i class="ml-2 mr-2 icon fa fa-envelope"></i> Emails</div>--}}
                                {{--<div class="list-count count">{{ $list->withEmails() }}</div>--}}
                            {{--</div>--}}
                            {{--<div>--}}
                                {{--<div class="list-count"><i class="ml-2 mr-2 icon fa fa-phone"></i> Phones</div>--}}
                                {{--<div class="list-count count">{{ $list->withPhones() }}</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-3 col-sm-4 list-details">--}}
                            {{--<div>--}}
                                {{--<div class="list-count"><i class="ml-2 mr-2 icon fa fa-map"></i> Conquest</div>--}}
                                {{--<div class="list-count count">{{ $list->fromConquest(false) }}</div>--}}
                            {{--</div>--}}
                            {{--<div>--}}
                                {{--<div class="list-count"><i class="ml-2 mr-2 icon fa fa-database"></i> Dealer DB</div>--}}
                                {{--<div class="list-count count">{{ $list->fromDealerDb(true) }}</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--@else--}}
                        {{--<div class="col-md-6 col-sm-8">--}}
                            {{--@if ($list->failed_at)--}}
                                {{--<div><h4 class="text-danger">List failed to load: </h4><p class="text-danger">{{ $list->failed_reason }}</p></div>--}}
                            {{--@else--}}
                                {{--<div class="alert"><i class="icon fa-spinner fa-spin "></i> <strong>Loading Recipients...</strong> <i>(refresh to update)</i></div>--}}
                            {{--@endif--}}
                        {{--</div>--}}
                    {{--@endif--}}
                    <div class="col-12 col-sm-6 col-lg-3 recipient-stats">
                        <div class="recipient-stats--stat">
                            <span class="pm-font-companies-icon"></span>
                            <span>Total</span>
                            <strong>@{{ row.recipient_count || 0 }}</strong>
                        </div>
                        <div class="recipient-stats--stat">
                            <span class="pm-font-mail-icon"></span>
                            <span>Email</span>
                            <strong>@{{ row.with_email || 0 }}</strong>
                        </div>
                        <div class="recipient-stats--stat">
                            <span class="pm-font-phone-icon"></span>
                            <span>Phone</span>
                            <strong>@{{ row.with_phone || 0 }}</strong>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3 recipient-stats">
                        <div class="recipient-stats--stat">
                            <span class="pm-font-templates-icon"></span>
                            <span>Conquest</span>
                            <strong>@{{ row.from_conquest || 0 }}</strong>
                        </div>
                        <div class="recipient-stats--stat">
                            <span class="pm-font-dealer-db-icon"></span>
                            <span>Dealer DB</span>
                            <strong>@{{ row.from_dealer || 0 }}</strong>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2 recipient-options">
                        <a class="btn btn-action" :href="generateRoute(showRecipientListUrl, {listId: row.id})">
                            MODIFY MEMBER
                        </a>
                        <div class="options-group">
                            <a class="btn pm-btn btn-transparent" :href="generateRoute(downloadRecipientListUrl, {listId: row.id})" download>
                                <download-icon></download-icon>
                            </a>
                            <a class="btn pm-btn btn-transparent" href="javascript:;" @click.prevent="removeList(row)">
                                <spinner-icon :size="'xs'" v-if="loadingStats"></spinner-icon>
                                <trash-icon v-if="!loadingStats"></trash-icon>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <pm-pagination class="mt-3" :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
        </div>
        <b-modal ref="addPhoneModalRef" id="upload-recipient-modal" size="lg" hide-footer>
            <template slot="modal-header">
                <h4>Upload Recipients</h4>
                <span class="close-modal-header float-right" @click="closeModal()">
                    <i class="fas fa-times float-right"></i>
                </span>
            </template>
            <upload-recipient :target-url="uploadRecipientsUrl"></upload-recipient>
        </b-modal>
    </div>
@endsection
