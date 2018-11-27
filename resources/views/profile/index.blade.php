@extends('layouts.remark')

@section('header')
    <link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.min.css') }}">
@endsection

@section('manualStyle')
    .company-image {
    width: 40px;
    height: 40px;
    background-size: cover;
    background-position: 50% 50%;
    background-color: #dadada;
    border-radius: 50%;
    }
@endsection

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
                @if (!auth()->user()->isAdmin())
                <div class="col-xxl-8 offset-xxl-2 col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-body">
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
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Role</th>
                                        <th>Timezone</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($companies as $company)
                                        <tr class="user-row">
                                            <td class="id-row v-center"><strong>{{ $company->id }}</strong></td>
                                            <td width="60px" class="text-center">
                                                <div class="company-image" style="background-image: url('{{ $company->image_url }}')"></div>
                                            </td>
                                            <td class="v-center">{{ $company->name }}</td>
                                            <td class="text-capitalize v-center">{{ $company->type }}</td>
                                            <td class="v-center">@role($user->getRole($company))</td>
                                            <td class="v-center">
                                                @php
                                                    $timezones = $timezones ?? [];
                                                    $timezones[$company->id] = $user->getTimezone($company);
                                                @endphp
                                                <select value="{{ $timezones[$company->id] }}" name="timezone" id="timezone_{{ $company->id }}" required class="form-control" data-plugin="select2">
                                                    <option disabled {{ $timezones[$company->id] == '' ? 'selected' : '' }}>Choose Timezone...
                                                    </option>
                                                    @foreach (App\Models\User::getPossibleTimezonesForUser() as $timezone)
                                                        <option {{ $timezones[$company->id] == $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <a href="javascript:;" class="btn btn-sm btn-primary btn-round mb-5 btn-edit-timezone" data-company="{{ $company->id }}">
                                                    Save
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('scriptTags')
    <script src="{{ secure_url('js/Plugin/material.js') }}"></script>
    <script src="{{ secure_url('js/Plugin/formatter.js') }}"></script>
    <script src="{{ secure_url('vendor/formatter/jquery.formatter.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('js/Plugin/bootstrap-select.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/bootstrap-select/bootstrap-select.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/bootstrap-sweetalert/sweetalert.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.btn-edit-timezone').on('click', function () {
                var companyId = $(this).data('company');
                var role = $('#role_' + companyId).val();
                var timezone = $('#timezone_' + companyId).val();

                $.ajax({
                    url: '{{ route('profile.update-company-data', ['user' => $user->id]) }}',
                    method: 'post',
                    data: {
                        role: role,
                        timezone: timezone,
                        company: companyId
                    },
                    success: function () {
                        swal("All Done", "Company Updated!", "success");
                    },
                    error: function (error) {
                        var errors = error.responseJSON.errors;
                        var errorMsg = '';
                        $.each(errors, function (idx1, messages) {
                            $.each(messages, function (idx2, message) {
                                errorMsg += message + '\n';
                            })
                        });
                        swal(errorMsg);
                    }
                })
            })
        });
    </script>
@endsection
