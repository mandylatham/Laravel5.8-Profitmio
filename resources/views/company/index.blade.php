@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/company-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchFormUrl = "{{ route('company.for-user-display') }}";
        window.q = @json($q);
    </script>
    <script src="{{ asset('js/company-index.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="company-index">
        <div class="row align-items-end no-gutters mb-md-3">
            <div class="col-12 col-sm-5 col-lg-3">
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3">
                <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" @keyup.enter="fetchData()">
            </div>
        </div>
        <div class="row align-items-end no-gutters">
            <div class="col-12">
                <pm-responsive-table :rows="companies" :columns="columnData" :pagination="pagination" :is-loading="isLoading" @page-changed="onPageChanged">
                    <template slot="id" slot-scope="{row}">
                        <span class="company-id">ID: @{{ row.id }}</span>
                        <span class="company-image" :style="{backgroundImage: 'url('+row.image_url+')'}"></span>
                        <span class="company-name">@{{ row.name }}</span>
                    </template>
                    <template slot="options" slot-scope="{row}">
                        <a class="btn btn-link pm-btn-link pm-btn-link-warning" href="">
                            <edit-2-icon></edit-2-icon>
                        </a>
                        <a href="" class="btn btn-link pm-btn-link pm-btn-link-danger">
                            <trash-icon></trash-icon>
                        </a>
                    </template>
                </pm-responsive-table>
            </div>
        </div>
    </div>
@endsection