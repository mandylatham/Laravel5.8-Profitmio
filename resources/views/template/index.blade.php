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
        window.templateEdit = "{{ route('template.edit', ['template' => ':templateId']) }}";
        window.templateDelete = "{{ route('template.delete', ['campaign' => ':templateId']) }}";
        window.q = @json($q);
    </script>
    <script src="{{ asset('js/media-template-index.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="template-index" v-cloak>
        <div class="row mt-md-3">
            <div class="col-12">
                <button v-b-modal.add-template-modal class="btn btn-primary mr-3">
                    <i class="fa fa-plus mr-2"></i>
                    Add new template
                </button>

                <b-modal id="add-template-modal" hide-footer title="How do you want to add a template?">
                    <div class="add-template-buttons">
                        <a href="{{ route('template.create-form') }}" class="btn btn-default">
                            <i class="fa fa-code mr-3"></i>
                            Create template normally</a>
                        <button class="btn btn-default">
                            <i class="fa fa-cogs mr-3"></i>
                            Use email builder app</button>
                    </div>
                </b-modal>
            </div>
        </div>
        <div class="row align-items-end no-gutters mb-md-3">
            <div class="col-12 offset-sm-7 col-sm-5 offset-lg-9 col-lg-3 mt-3">
                <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" @keyup.enter="fetchData">
            </div>
        </div>
        <div class="row align-items-end no-gutters mt-3">
            <div class="col-12">
                <div class="loader-spinner" v-if="isLoading">
                    <spinner-icon></spinner-icon>
                </div>
                <div class="no-items-row" v-if="templates.length === 0">
                    No Items
                </div>
                <media-template v-if="templates.length > 0" v-for="template in templates" :key="template.id" :media_template="template"></media-template>
                <pm-pagination v-if="templates.length > 0" :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
            </div>
        </div>
    </div>
@endsection
