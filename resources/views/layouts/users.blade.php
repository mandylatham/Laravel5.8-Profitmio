@extends('layouts.remark')

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-xxl-10 offset-xxl-1 col-md-12">
                    <div style="display: flex">
                        <button type="button"
                                role="button"
                                data-url="{{ secure_url('/users') }}"
                                class="btn btn-sm btn-default waves-effect campaign-edit-button"
                                data-toggle="tooltip"
                                data-original-title="Go Back"
                                style="margin-right: 15px; background: rgba(255, 255, 255, 0.1); border: 0;">
                            <i class="icon fa-angle-left" style="color: #efefef" aria-hidden="true"></i>
                        </button>
                        <h3 class="page-title text-default">
                            <small>#</small>{{ $user->id }}:
                            {{ ucwords(strtolower($user->organization)) }} <small>|</small>
                            {{ ucwords(strtolower($user->name)) }}
                        </h3>
                    </div>
                    <div class="page-header-actions">
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="row-fluid" data-plugin="matchHeight" data-by-row="true">
                <div class="col-xxl-10 offset-xxl-1 col-md-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <nav class="navbar navbar-default navbar-mega">
                                <div class="container-fluid">
                                    <div class="navbar-header">
                                        <button type="button" class="navbar-toggler hamburger hamburger-close collapsed" data-toggle="collapse" data-target="#navbar-collapse-2">
                                            <span class="sr-only">Toggle navigation</span>
                                            <span class="hamburger-bar"></span>
                                        </button>
                                    </div>
                                    <div class="navbar-collapse collapse" id="navbar-collapse-2">
                                        <ul class="nav navbar-nav">
                                            <li class="nav-item dropdown dropdown-mega">
                                                <a class="nav-link" href="{{ secure_url('/user/' . $user->id) }}">Campaigns</a>
                                            </li>
                                        </ul>
                                        <ul class="nav navbar-nav navbar-right">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ secure_url('/user/' . $user->id . '/edit') }}">Edit</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                        </div>
                        <div class="panel-body container-fluid" style="padding-top:20px;">
                            <div class="row-fluid">

                                @yield('user_content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

