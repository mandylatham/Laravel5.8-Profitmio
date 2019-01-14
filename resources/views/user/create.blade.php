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
    </script>
    <script src="{{ asset('js/user-create.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="user-create">
        <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2 col-md-4 offset-md-4">
                <form action="" @submit.prevent="saveCompany">
                    <div class="form-group">
                        <label for="role">Role</label>
                        <v-select :options="roles" v-model="userForm.role" class="filter--v-select">
                            <template slot="selected-option" slot-scope="option">
                                @{{ option.label | userRole }}
                            </template>
                            <template slot="option" slot-scope="option">
                                @{{ option.label | userRole }}
                            </template>
                        </v-select>
                    </div>
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" required v-model="userForm.first_name">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" required v-model="userForm.last_name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" required v-model="userForm.email">
                    </div>
                    <div class="form-group" v-if="isAdmin && userForm.role !== 'site_admin'">
                        <label for="email">Company</label>
                        <v-select :options="companies" label="name" v-model="companySelected" class="filter--v-select">
                        </v-select>
                    </div>
                    <button type="submit" :disabled="loading" class="btn pm-btn-submit pm-btn pm-btn-purple pm-btn-md mt-4">
                        <span v-if="!loading"><i class="fas fa-plus mr-2"></i>Add User</span>
                        <div class="loader-spinner" v-if="loading">
                            <spinner-icon></spinner-icon>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
