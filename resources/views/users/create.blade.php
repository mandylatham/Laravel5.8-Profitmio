@extends('layouts.remark')

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-md-6 offset-md-3">
                    <button type="button" role="button"
                            data-url="{{ route('user.index') }}"
                            class="btn btn-sm float-left btn-default waves-effect campaign-edit-button"
                            data-toggle="tooltip" data-original-title="Go Back"
                            style="margin-right: 15px; background: rgba(255, 255, 255, 0.2); border-size: 0.5px;">
                        <i class="icon fa-angle-left" style="color: #efefef" aria-hidden="true"></i>
                    </button>
                    <h3 class="page-title text-default">
                        New User
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
                            <form class="form" method="post" action="{{ route('user.store') }}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="role" class="floating-label">Role</label>
                                    <select name="role" value="{{ old('role') }}" class="form-control" required>
                                        <option selected disabled>Choose role...</option>
                                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="first_name" class="floating-label">First Name</label>
                                    <input type="text" class="form-control empty" name="first_name"
                                           placeholder="First Name"
                                           value="{{ old('first_name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name" class="floating-label">Last Name</label>
                                    <input type="text" class="form-control empty" name="last_name"
                                           placeholder="Last Name"
                                           value="{{ old('last_name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="floating-label">Email</label>
                                    <input type="email" class="form-control empty" name="email" placeholder="Email"
                                           value="{{ old('email') }}" required>
                                </div>
                                <button type="submit" class="btn btn-success">Add User</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
