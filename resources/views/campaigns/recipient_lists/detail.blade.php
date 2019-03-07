@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/recipients-detail.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchRecipientsUrl = @json(route('campaigns.recipient-lists.recipients.for-user-display', ['campaign' => $campaign->id, 'list' => $list->id]));
        window.deleteRecipientsUrl = @json(route('campaigns.recipients.delete', ['campaign' => $campaign->id]));
    </script>
    <script src="{{ asset('js/recipients-detail.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="recipients-detail" v-cloak>
        <div class="row">
            <div class="col mb-3">
                <a class="btn pm-btn pm-btn-blue" href="{{ route('campaigns.recipient-lists.index', ['campaign' => $campaign->id]) }}">
                    <i class="fas fa-arrow-circle-left mr-2"></i> Go Back
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col mb-3">
                <h2 class="m-0">List {{ $list->name }} Recipients</h2>
            </div>
        </div>
        <div class="row align-items-end mb-3">
            <div class="col-12 col-sm-5 col-lg-4">
                <button class="btn btn-danger pm-btn" :disabled="recipients.length === 0" type="button" @click="deleteRecipients()"><i class="far fa-trash-alt mr-3"></i>Delete Checked</button>
            </div>
            <div class="col-none col-sm-2 col-lg-4"></div>
            <div class="col-12 col-sm-5 col-lg-4">
                <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" @keyup.enter="fetchData()">
            </div>
        </div>
        <div>
            <div class="table-loader-spinner" v-if="loading">
                <spinner-icon></spinner-icon>
            </div>
            <div class="no-items-row" v-if="recipients.length === 0">
                No Items
            </div>
            <div class="table-container" v-if="recipients.length > 0">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" width="60px">
                                <p-check color="primary" class="p-default" name="checkAll" v-model="checkAll" @change="selectAllRecipients"></p-check>
                            </th>
                            <th>Person</th>
                            <th>Address</th>
                            <th>Vehicle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="recipient in recipients">
                            <td class="text-center">
                                <i v-if="recipient.dropped_at" class="fas fa-paper-plane"></i>
                                <p-check v-if="!recipient.dropped_at" color="primary" class="p-default" name="checkAll" v-model="recipient.checked"></p-check>
                            </td>
                            <td>
                                <div>@{{ recipient.name }}</div>
                                <div>@{{ recipient.email }}</div>
                                <div>@{{ recipient.phone }}</div>
                            </td>
                            <td>
                                <div>@{{ recipient.address1 }}</div>
                                <div>@{{ recipient.city }}, @{{ recipient.state }} @{{ recipient.zip }}</div>
                            </td>
                            <td>
                                <div>@{{ recipient.vehicle }}</div>
                                <div>@{{ recipient.vin }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <pm-pagination class="mt-3" :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
            </div>
        </div>
    </div>
@endsection
