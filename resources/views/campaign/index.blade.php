@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/campaign-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script src="{{ asset('js/campaign-index.js') }}"></script>
    <script>
        window.app.addPageVariables('companies', []);
        window.app.addPageVariables('campaigns', {!! $campaigns !!});
    </script>
@endsection

@section('main-content')
    <div class="container-fluid" id="campaign-index">
        <div class="row align-items-end no-gutters">
            <form @onBlur="onSubmit">
                <div class="col-12 col-sm-5 col-lg-3">
                    <div class="form-group filter--form-group">
                        <p class="text-danger" v-show="form.errors.has(company)">@{{ form.errors.get(company) }}</p>
                        <label>Filter By Company</label>
                        <v-select :options="pageVariables.companies" v-on:select="search()" class="filter--v-select"></v-select>
                    </div>
                </div>
                <div class="col-none col-sm-2 col-lg-6"></div>
                <div class="col-12 col-sm-5 col-lg-3">
                    <input type="text" v-model="pageVariables.searchTerm" v-on:key-press.enter="search()" class="form-control filter--search-box" aria-describedby="search"
                           placeholder="Search">
                </div>
            </form>
            <div class="col-12">
                <pm-responsive-table :rows="pageVariables.campaigns" :columns="pageVariables.columns">
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
