@extends('layouts.remark')

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-md-6 offset-md-3">
                    <h3 class="page-title text-default d-flex align-items-center">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </h3>
                    <div class="page-header-actions">
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="row-fluid" data-plugin="matchHeight" data-by-row="true">
                <div class="col-md-6 offset-md-3">
                    <div class="panel">
                        <div class="panel-body" data-fv-live="enabled">
                            @if ($errors->count() > 0)
                                <div class="alert alert-danger">
                                    <h3>There were some errors:</h3>
                                    <ul>
                                        @foreach ($errors->all() as $message)
                                            <li>{{ $message }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form class="form" method="post" action="{{ route('profile.update') }}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="first_name" class="floating-label">First Name</label>
                                    <input type="text" class="form-control empty" name="first_name"
                                           placeholder="First Name"
                                           value="{{ $user->first_name  }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name" class="floating-label">Last Name</label>
                                    <input type="text" class="form-control empty" name="last_name"
                                           placeholder="Last Name"
                                           value="{{ $user->last_name }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="floating-label">Email</label>
                                    <input type="email" class="form-control empty" name="email" placeholder="Email"
                                           value="{{ $user->email  }}" required>
                                </div>
                                <button type="submit" class="btn btn-success">Update Account</button>
                            </form>
                        </div>
                    </div>
                    <div class="panel mt-30">
                        <div class="panel-body" data-fv-live="enabled">
                            <form class="form" method="post" action="{{ route('profile.update-password') }}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="password" class="floating-label">Current Password</label>
                                    <input type="password" class="form-control empty" name="password" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password" class="floating-label">New Password</label>
                                    <input type="password" class="form-control empty" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password_confirmation" class="floating-label">Confirm New Password</label>
                                    <input type="password" class="form-control empty" name="new_password_confirmation" required>
                                </div>
                                <button type="submit" class="btn btn-success">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
