@extends('layouts.base', [
    'hasSidebar' => false
])

@section('head-styles')
    <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.user = @json($user);
        window.updateUserUrl = "{{ route('profile.update') }}";
        window.updatePasswordUrl = "{{ route('profile.update-password') }}";
    </script>
    <script src="{{ asset('js/profile.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="profile" v-cloak>
        <div class="row">
            <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                <a class="btn pm-btn pm-btn-blue go-back mb-3 mt-3" href="{{ route('dashboard') }}">
                    <i class="fas fa-arrow-circle-left mr-2"></i> Go Back
                </a>
                <h1 class="mb-3 mt-3">Your Profile</h1>
                <form @submit.prevent="updateProfile">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label" label="first_name">First Name</label>
                                <input name="first_name" class="form-control" v-model="userForm.first_name">
                            </div>
                            <div class="form-group">
                                <label class="form-label" label="last_name">Last Name</label>
                                <input name="last_name" class="form-control" v-model="userForm.last_name">
                            </div>
                            <div class="form-group">
                                <label class="form-label" label="email">Email</label>
                                <input name="email" class="form-control" v-model="userForm.email">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn pm-btn pm-btn-purple" :disabled="loadingUserForm">
                                    <span v-if="!loadingUserForm">Save</span>
                                    <spinner-icon class="white" :size="'xs'" v-if="loadingUserForm"></spinner-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <form @submit.prevent="resetPassword">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Current Password</label>
                                <input type="password" required name="current_password" class="form-control" v-model="passwordForm.current_password">
                            </div>
                            <div class="form-group">
                                <label class="form-label">New Password</label>
                                <input type="password" required name="new_password" class="form-control" v-model="passwordForm.new_password">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" required name="new_password_confirmation" class="form-control" v-model="passwordForm.new_password_confirmation">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn pm-btn pm-btn-purple" :disabled="loadingPasswordForm">
                                    <span v-if="!loadingPasswordForm">Save</span>
                                    <spinner-icon class="white" :size="'xs'" v-if="loadingPasswordForm"></spinner-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
