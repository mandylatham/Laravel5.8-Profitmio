@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/media-template-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchFormUrl = "{{ route('template.for-user-display') }}";
        window.getCompanyUrl = "{{ route('company.for-dropdown') }}";
        window.companySelected = @json($companySelected);
        window.q = @json($q);
    </script>
    <script src="{{ asset('js/media-template-index.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="template-index">
        <div class="row align-items-end no-gutters mb-md-3">
            <div class="col-12 offset-sm-7 col-sm-5 offset-sm-9 col-lg-3 mt-3">
                <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" @keyup.enter="fetchData">
            </div>
        </div>
        <div class="row align-items-end no-gutters mt-3">
            <div class="col-12">
                <div class="loader-spinner" v-if="isLoading">
                    <spinner-icon></spinner-icon>
                </div>
                <media-template v-for="template in templates" :key="template.id" :media_template="template"></media-template>
                <pm-pagination :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
            </div>
        </div>
    </div>
@endsection
