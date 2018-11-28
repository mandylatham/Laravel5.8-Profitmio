@extends('layouts.remark')

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-md-6 offset-md-3">
                    <button type="button" role="button"
                            data-url="{{ route('company.user.index', ['company' => $company->id]) }}"
                            class="btn btn-sm float-left btn-default waves-effect campaign-edit-button"
                            data-toggle="tooltip" data-original-title="Go Back"
                            style="margin-right: 15px; background: rgba(255, 255, 255, 0.2); border-size: 0.5px;">
                        <i class="icon fa-angle-left" style="color: #efefef" aria-hidden="true"></i>
                    </button>
                    <h3 class="page-title text-default d-flex align-items-center">
                        #{{ $user->id}} {{ $user->first_name }} {{ $user->last_name }}<small class="ml-auto"># {{ $company->id }}: {{ ucwords($company->name) }}</small>
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
                            <form class="form" method="post" action="{{ route('company.user.update', ['company' => $company->id, 'user' => $user->id]) }}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="role" class="floating-label">Role</label>
                                    <select name="role" value="{{ $userCompanyRole }}" class="form-control" required>
                                        <option selected disabled>Choose role...</option>
                                        <option value="admin" {{ $userCompanyRole === 'admin' ? 'selected' : '' }}>@role('admin')</option>
                                        <option value="user" {{ $userCompanyRole === 'user' ? 'selected' : '' }}>@role('user')</option>
                                    </select>
                                </div>
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
                                <div class="form-group">
                                    <label for="timezone" class="floating-label">Timezone</label>
                                    <select value="{{ $userCompanyTimezone }}" name="timezone" class="form-control" data-plugin="select2">
                                        <option disabled {{ $userCompanyTimezone == '' ? 'selected' : '' }}>Choose Timezone...
                                        </option>
                                        @foreach (App\Models\User::getPossibleTimezonesForUser() as $timezone)
                                            <option {{ $userCompanyTimezone == $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">Update User</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
