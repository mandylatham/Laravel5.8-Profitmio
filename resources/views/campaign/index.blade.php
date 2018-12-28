@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/campaign-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script src="{{ asset('js/campaign-index.js') }}"></script>
    <script>
        window.app.setFormUrl("{{ route('campaign.index') }}");
    </script>
@endsection

@section('main-content')
    <div class="container-fluid" id="campaign-index">
        <div class="row align-items-end no-gutters mb-md-3">
            <div class="col-12 col-sm-5 col-lg-3">
                <div class="form-group filter--form-group">
                    <label>Filter By Company</label>
                    <v-select :options="companies" v-model="filters.companySelected" class="filter--v-select" @input="onPageChanged"></v-select>
                </div>
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3">
                <input type="text" v-model="filters.searchTerm" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" @keyup.enter="fetchData()">
            </div>
        </div>
        <div class="row align-items-end no-gutters">
            <div class="col-12">
                <pm-responsive-table :rows="campaigns" :columns="columnData" :pagination="pagination" :is-loading="isLoading" @page-changed="onPageChanged">
                    <template slot="name" slot-scope="{row}">
                        @{{ row.name }} <span class="font-weight-bold ml-1">(id: @{{ row.id }})</span>
                    </template>
                    <template slot="recipients_count" slot-scope="{row}">
                        <span class="pm-font-user-icon"></span>
                        <span class="ml-3">@{{ row.recipients_count }}</span>
                    </template>
                    <template slot="phone_responses_count" slot-scope="{row}">
                        <span class="pm-font-phone-icon"></span>
                        <span class="ml-3">@{{ row.phone_responses_count }}</span>
                    </template>
                    <template slot="email_responses_count" slot-scope="{row}">
                        <span class="pm-font-mail-icon"></span>
                        <span class="ml-3">@{{ row.email_responses_count }}</span>
                    </template>
                    <template slot="text_responses_count" slot-scope="{row}">
                        <span class="pm-font-message-icon"></span>
                        <span class="ml-3">@{{ row.text_responses_count }}</span>
                    </template>
                    <template slot="options" slot-scope="{row}">
                        <a class="pm-btn btn btn-transparent">
                            <span class="pm-font-system-icon custom-class"></span>
                        </a>
                        <a class="pm-btn btn btn-transparent">
                            <span class="pm-font-edit-icon custom-class"></span>
                        </a>
                    </template>
                </pm-responsive-table>
            </div>
        </div>
    </div>
@endsection
