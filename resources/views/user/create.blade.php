@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/user-create.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.addUserUrl = "{{ route('user.store') }}";
        window.isAdmin = @json(auth()->user()->isAdmin());
        window.getCompaniesUrl = "{{ route('company.for-dropdown') }}";
        window.userIndexUrl = "{{ route('user.index') }}";
        window.companySelectedId = @json(isset($company) ? $company->id : null);
    </script>
    <script src="{{ asset('js/user-create.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="user-create" v-cloak>
        <div class="row">
            <div class="col-12 col-md-6 offset-md-3">
                <a class="btn pm-btn pm-btn-blue mb-3" href="{{ route('user.index') }}">
                    <i class="fas fa-arrow-circle-left mr-2"></i> Go Back
                </a>
                <div class="card">
                    <div class="card-body">
                        <form action="" @submit.prevent="saveCompany">
                            <div class="form-group">
                                <label for="role">Role</label>
                                <v-select dusk="role-select" name="role" :options="roles" v-model="userForm.role" class="filter--v-select">
                                    <template slot="selected-option" slot-scope="option">
                                        @{{ option.label | userRole }}
                                    </template>
                                    <template slot="option" slot-scope="option">
                                        @{{ option.label | userRole }}
                                    </template>
                                </v-select>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" name="email" class="form-control" required v-model="userForm.email">
                            </div>
                            @if(isset($companies))
                            <div class="form-group" v-if="isAdmin && userForm.role !== 'site_admin'">
                                <label for="email">Company</label>
                                <v-select dusk="company-select" name="company" :options="companies" label="name" v-model="companySelected" class="filter--v-select">
                                </v-select>
                            </div>
                            @endif
                            @if(isset($company))
                                <input type="hidden" name="company" value="{{ $company->id }}">
                            @endif
                            <button dusk="save-user-button" type="submit" :disabled="loading" class="btn pm-btn-submit pm-btn pm-btn-purple pm-btn-md mt-4">
                                <span v-if="!loading"><i class="fas fa-plus mr-2"></i>Add User</span>
                                <div class="loader-spinner" v-if="loading">
                                    <spinner-icon></spinner-icon>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
