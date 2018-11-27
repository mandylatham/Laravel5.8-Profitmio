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
                    <button type="button" role="button"
                            data-url="{{ route('user.index') }}"
                            class="btn btn-sm float-left btn-default waves-effect campaign-edit-button"
                            data-toggle="tooltip" data-original-title="Go Back"
                            style="margin-right: 15px; background: rgba(255, 255, 255, 0.2); border-size: 0.5px;">
                        <i class="icon fa-angle-left" style="color: #efefef" aria-hidden="true"></i>
                    </button>
                    <h3 class="page-title text-default d-flex align-items-center">
                        #{{ $user->id}} {{ $user->first_name }} {{ $user->last_name }}
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
                            <form class="form" method="post" action="{{ route('user.update', ['user' => $user->id]) }}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="first_name" class="floating-label">First Name</label>
                                    <input type="text" class="form-control empty" name="first_name" placeholder="First Name"
                                           value="{{ old('first_name') ?? $user->first_name }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name" class="floating-label">Last Name</label>
                                    <input type="text" class="form-control empty" name="last_name" placeholder="Last Name"
                                           value="{{ old('last_name') ?? $user->last_name }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="username" class="floating-label">Username</label>
                                    <input type="text" class="form-control empty" name="username" placeholder="Username"
                                           value="{{ old('username') ?? $user->username }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="floating-label">Phone Number</label>
                                    <input type="text"
                                           class="form-control {{ old('phone_number') ?? $user->phone_number ?? 'empty' }}"
                                           name="phone_number" autocomplete="off"
                                           value="{{ old('phone_number') ?? $user->phone_number }}"
                                           data-plugin="formatter" data-pattern="([[999]]) [[999]]-[[9999]]"
                                           required>
                                </div>
                                <button type="submit" class="btn btn-success">Update User</button>
                            </form>
                        </div>
                    </div>
                </div>
                @if (auth()->user()->isAdmin() && !$user->isAdmin())
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
                                            <td class="v-center">
                                                <select name="role" value="{{ old('role') ?? $user->getRole($company) }}" id="role_{{ $company->id }}" class="form-control" required>
                                                    <option selected disabled>Choose role...</option>
                                                    <option value="admin" {{ $user->getRole($company) === 'admin' ? 'selected' : '' }}>@role('admin')</option>
                                                    <option value="user" {{ $user->getRole($company) === 'user' ? 'selected' : '' }}>@role('user')</option>
                                                </select>
                                            </td>
                                            <td class="v-center">
                                                @php
                                                    $timezones = $timezones ?? [];
                                                    $timezones = $timezones[$user->id] ?? [];
                                                    $timezones[$user->id][$company->id] = $user->getTimezone($company);
                                                @endphp
                                                <select value="{{ $timezones[$user->id][$company->id] }}" name="timezone" id="timezone_{{ $company->id }}" required class="form-control" data-plugin="select2">
                                                    <option disabled {{ $timezones[$user->id][$company->id] == '' ? 'selected' : '' }}>Choose Timezone...
                                                    </option>
                                                    @foreach (App\Models\User::getPossibleTimezonesForUser() as $timezone)
                                                        <option {{ $timezones[$user->id][$company->id] == $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <a href="javascript:;"class="btn btn-sm btn-primary btn-round mb-5 btn-edit-timezone" data-company="{{ $company->id }}">
                                                    Save
                                                </a>
                                                @if (auth()->user()->isAdmin() && !$user->isAdmin())
                                                    <a class="btn btn-sm btn-success btn-round mb-5"
                                                       href="{{ route('admin.impersonate', ['user' => $user->id, 'company' => $company->id]) }}">
                                                        Impersonate
                                                    </a>
                                                @endif
                                                @if (!$user->isCompanyProfileReady($company))
                                                    <a class="btn btn-sm btn-primary btn-round mb-5"
                                                       href="{{ route('admin.resend-invitation', ['user' => $user->id, 'company' => $company->id ]) }}">
                                                        Re-send Invitation
                                                    </a>
                                                @endif
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
                    url: '{{ route('user.update-company-data', ['user' => $user->id]) }}',
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
                            console.log('messages', messages);
                            $.each(messages, function (idx2, message) {
                                console.log('message', message);
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
