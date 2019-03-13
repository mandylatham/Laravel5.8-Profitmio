@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/company-index.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchFormUrl = "{{ route('company.for-user-display') }}";
        window.companyEdit = "{{ route('company.details', ['company' => ':companyId']) }}";
        window.companyDelete = "{{ route('company.delete', ['company' => ':companyId']) }}";
        window.q = @json($q);
    </script>
    <script src="{{ asset('js/company-index.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="company-index" v-cloak>
        <div class="row align-items-end no-gutters">
            <div class="col-12 col-sm-5 col-lg-3 mb-3">
                <a dusk="create-company-button" href="{{ route('company.create') }}" class="btn pm-btn pm-btn-blue">
                    <i class="fa fa-plus mr-2"></i>
                    Add Company
                </a>
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3 mb-3">
                <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" @keyup.enter="fetchData()">
            </div>
        </div>
        <h1 class="page-title">Companies</h1>
        <div class="row no-gutters company-component inactive" v-for="(company, index) in companies" :key="company.id">
            <div class="col-12 col-md-5 company-header">
                <div class="company-header--title">
                    <p>Company @{{ company.id }}</p>
                    <strong>@{{ company.name }}</strong>
                    <p class="mt-2 mb-0 text-capitalize">@{{ company.type }}</p>
                </div>
            </div>
            <div class="col-4 col-md-2 company-postcard">
                <company-type no-label :company_type="company.type"></company-type>
            </div>
            <div class="col-4 col-md-2 company-date">
                <span>Created On</span>
                <span>@{{ company.created_at | amDateFormat('MM.DD.YY') }}</span>
            </div>
            <div class="col-4 col-md-3 company-links">
                <a class="btn pm-btn pm-btn-purple pm-btn-md justify-content-center" :href="generateRoute(companyEdit, {'companyId': company.id})">
                    <span class="fa fa-edit"></span> Edit
                </a>
                <a href="#" @click="deleteCompany(company.id, index)" class="btn pm-btn pm-btn-danger pm-btn-md justify-content-center">
                    <span class="fa fa-trash"></span> Delete
                </a>
            </div>
        </div>
        <pm-pagination v-if="companies.length > 0" :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
    </div>
@endsection
