@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/recipients-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchRecipientsUrl = @json(route('campaigns.recipient-lists.for-user-display', ['campaign' => $campaign->id]));
        window.uploadRecipientsUrl = @json(route('campaigns.recipient-lists.upload', ['campaign' => $campaign->id]));
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
                <a class="btn pm-btn pm-btn-blue" :class="{'asdf': true, 'asdfs': false}" v-b-modal.upload-recipient-modal href="javascript:;">
                    <i class="fas fa-plus mr-2"></i> UPLOAD NEW LIST
                </a>
            </div>
            <div class="col-none col-sm-2 col-lg-4"></div>
            <div class="col-12 col-sm-5 col-lg-4">
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
                    <div class="col-12 col-sm-6 col-lg-3 recipient-stats">
                        <div class="recipient-stats--stat">
                            <span class="pm-font-companies-icon"></span>
                            <span>Total</span>
                            <strong>@{{ row.total_recipients || 0 }}</strong>
                        </div>
                        <div class="recipient-stats--stat">
                            <span class="pm-font-mail-icon"></span>
                            <span>Email</span>
                            <strong>@{{ row.total_valid_emails || 0 }}</strong>
                        </div>
                        <div class="recipient-stats--stat">
                            <span class="pm-font-phone-icon"></span>
                            <span>Phone</span>
                            <strong>@{{ row.total_valid_phones || 0 }}</strong>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3 recipient-stats">
                        <div class="recipient-stats--stat">
                            <span class="pm-font-templates-icon"></span>
                            <span>Conquest</span>
                            <strong>@{{ row.total_conquest || 0 }}</strong>
                        </div>
                        <div class="recipient-stats--stat">
                            <span class="pm-font-dealer-db-icon"></span>
                            <span>Dealer DB</span>
                            <strong>@{{ row.total_dealer_db || 0 }}</strong>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2 recipient-options">
                        <a class="btn btn-action" :href="generateRoute(showRecipientListUrl, {listId: row.id})">
                            MODIFY MEMBER
                        </a>
                        <div class="options-group">
                            <a class="btn pm-btn btn-transparent" href="javascript:;">
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
