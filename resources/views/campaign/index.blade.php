@extends('layouts.base', [
    'hasSidebar' => false
])

@section('main-content')
    <div class="container-fluid" id="campaign-index">
        <div class="row align-items-end no-gutters">
            <div class="col-12 col-sm-5 col-lg-3">
                <div class="form-group filter--form-group">
                    <label>Filter By Company</label>
                    <v-select :options="pageData.campaignIndex.companies" class="filter--v-select"></v-select>
                </div>
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3">
                <input type="text" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" v-model="searchTerm">
            </div>
            <div class="col-12">
                <pm-responsive-table :rows="rows" :columns="columns">
                    <template slot="name" slot-scope="{row}">
                        @{{ row.name }} <span class="font-weight-bold ml-1">(id: @{{ row.id }})</span>
                    </template>
                    <template slot="recipients_count" slot-scope="{row}">
                        <user-icon></user-icon>
                        <span class="ml-10">@{{ row.recipients_count }}</span>
                    </template>
                    <template slot="phone_responses_count" slot-scope="{row}">
                        <phone-icon></phone-icon>
                        <span class="ml-10">@{{ row.phone_responses_count }}</span>
                    </template>
                    <template slot="email_responses_count" slot-scope="{row}">
                        <mail-icon></mail-icon>
                        <span class="ml-10">@{{ row.email_responses_count }}</span>
                    </template>
                    <template slot="text_responses_count" slot-scope="{row}">
                        <message-circle-icon></message-circle-icon>
                        <span class="ml-10">@{{ row.text_responses_count }}</span>
                    </template>
                    <template slot="options" slot-scope="{row}">
                        <a class="pm-btn btn btn-transparent">
                            <home-icon class="custom-class"></home-icon>
                        </a>
                        <a class="pm-btn btn btn-transparent">
                            <edit-icon class="custom-class"></edit-icon>
                        </a>
                    </template>
                </pm-responsive-table>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    var companies = {{ json_encode($companies) }};
@endsection
