@extends('layouts.remark')

@section('manualStyle')
    .company-image {
        width: 40px;
        background-size: cover;
        background-position: 50% 50%;
        height: 40px;
        border-radius: 50%;
        box-shadow: 1px 1px 4px #4a4a4a;
        background-color: #ddd;
    }
@endsection

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-xxl-8 offset-xxl-2 col-lg-12">
                    <div class="d-flex align-items-center">
                        <button type="button"
                                role="button"
                                data-url="{{ route('company.index') }}"
                                class="btn btn-sm btn-default waves-effect campaign-edit-button"
                                data-toggle="tooltip"
                                data-original-title="Go Back"
                                style="margin-right: 15px; background: rgba(255, 255, 255, 0.2); border-size: 0.5px;">
                            <i class="icon fa-angle-left" style="color: #efefef" aria-hidden="true"></i>
                        </button>
                        <h3 class="page-title text-default" style="
                                padding: 3px 12px;
                                margin-right: 12px;
                                box-shadow: 0px 1px 3px #aaa;
                                text-shadow: 1px 1px 4px #4a4a4a;
                                color: #ffffff;
                        ">
                            <small>#</small> {{ $company->id }}:
                            {{ ucwords($company->name) }}
                        </h3>
                        <div class="company-image ml-10" style="background-image: url({{ $company->image_url }})"></div>
                    </div>
                    <div class="page-header-actions">
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="row-fluid" data-plugin="matchHeight" data-by-row="true">
                <div class="col-xxl-8 offset-xxl-2 col-lg-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <div class="navbar navbar-default">
                                <div class="container-fluid">
                                    <div class="navbar-header">
                                        <button type="button" class="navbar-toggler hamburger hamburger-close collapsed" data-toggle="collapse" data-target="#navbar-collapse-1">
                                            <span class="sr-only">Toggle navigation</span>
                                            <span class="hamburger-bar"></span>
                                        </button>
                                    </div>
                                    <div class="navbar-collapse collapse" id="navbar-collapse-1">
                                        <ul class="nav navbar-nav">
                                            <li id="drops-link" class="nav-item {{ Route::currentRouteNamed('company.campaign.index') ? 'active' : '' }}">
                                                <a id="campaigns-nav-link" class="nav-link" href="{{ route('company.campaign.index', ['company' => $company->id]) }}">
                                                    <i class="icon icon-lg wi-raindrops"></i>
                                                    Campaigns
                                                </a>
                                            </li>
                                            <li id="dashboard-link" class="nav-item {{ Route::currentRouteNamed('company.user.index') ? 'active' : '' }}">
                                                <a id="users-nav-link" class="nav-link" href="{{ route('company.user.index', ['company' => $company->id]) }}">
                                                    <i class="icon oi-dashboard" ></i>
                                                    Users
                                                </a>
                                            </li>
                                        </ul>
                                        @yield('secondary_navbar')
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body container-fluid" style="padding-top:20px;">
                                <div class="row-fluid">
                                    @yield('company_content')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
