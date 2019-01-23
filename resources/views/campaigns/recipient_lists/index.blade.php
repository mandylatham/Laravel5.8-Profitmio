@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/recipients-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchRecipientsUrl = "{{ route('campaigns.recipients.for-user-display', ['campaign' => $campaign->id]) }}";
{{--        window.deleteRecipientUrl = "{{ route('campaigns.recipients.delete', ['campaign' => $campaign->id, 'drop' => ':dropId']) }}";--}}
{{--        window.downloadRecipientUrl = "{{ route('campaigns.recipients.index', ['campaign' => $campaign->id]) }}";--}}
    </script>
    <script src="{{ asset('js/recipients-index.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="recipients-index" v-cloak>
        <div class="row align-items-end no-gutters mb-md-3">
            <div class="col-12 col-sm-5 col-lg-4">
                <a class="btn pm-btn pm-btn-blue">
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
                    <div class="col-12 col-lg-3">
                        <div class="recipient-name--container">
                            <div class="recipient-name--icon">
                                <list-icon></list-icon>
                                <span class="pm-font-system-icon"></span>
                            </div>
                            <div class="recipient-name--data">
                                <strong>@{{ row.name }}</strong>
                                <small>Database List</small>
                                <div class="recipient-name--id">List File ID: @{{ row.id }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="recipient-stats--container">
                            <div class="recipient-stats--stat">
                                <users-icon></users-icon>
                                <span class="pm-font-companies-icon"></span>
                                <span>Total</span>
                                <strong>@{{ row.total }}</strong>
                            </div>
                            <div class="recipient-stats--stat">
                                <mail-icon></mail-icon>
                                <span class="pm-font-mail-icon"></span>
                                <span>Email</span>
                                <strong>@{{ row.email_count }}</strong>
                            </div>
                            <div class="recipient-stats--stat">
                                <phone-icon></phone-icon>
                                <span class="pm-font-phone-icon"></span>
                                <span>Phone</span>
                                <strong>@{{ row.phone_count }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="recipient-stats--container">
                            <div class="recipient-stats--stat">
                                <map-icon></map-icon>
                                <span class="pm-font-templates-icon"></span>
                                <span>Conquest</span>
                                <strong>@{{ row.conquest_count }}</strong>
                            </div>
                            <div class="recipient-stats--stat">
                                <database-icon></database-icon>
                                <span class="pm-font-dealer-db-icon"></span>
                                <span>Dealer DB</span>
                                <strong>@{{ row.dealer_count }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <a class="btn btn-action">
                            MODIFY MEMBER
                        </a>
                        <div class="options-group">
                            <a class="pm-btn btn btn-transparent">
                                <download-icon></download-icon>
                            </a>
                            <a class="pm-btn btn btn-transparent">
                                <trash-icon></trash-icon>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
