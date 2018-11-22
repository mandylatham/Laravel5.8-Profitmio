@extends('layouts.remark')

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-xxl-8 offset-xxl-2 col-lg-12">
                    <button type="button"
                            role="button"
                            data-url="{{ secure_url('/campaign/' . $campaign->id . '/response-console') }}"
                            class="btn btn-sm btn-default waves-effect float-right button-link"
                            style="background: rgba(0, 0, 20, 0.2); color: #000000; border-color: #666; box-shadow: 1px 1px 4px #888"
                            data-toggle="tooltip"
                            data-original-title="Open Console">
                        <i class="icon fa-terminal"  aria-hidden="true"></i>
                        <span class="hidden-md-down">Open Console</span>
                    </button>
                    <div style="display: flex">
                        @if(auth()->user()->isAdmin())
                        <button type="button"
                                role="button"
                                data-url="{{ secure_url('/campaigns') }}"
                                class="btn btn-sm btn-default waves-effect campaign-edit-button"
                                data-toggle="tooltip"
                                data-original-title="Go Back"
                                style="margin-right: 15px; background: rgba(255, 255, 255, 0.2); border-size: 0.5px;">
                            <i class="icon fa-angle-left" style="color: #efefef" aria-hidden="true"></i>
                        </button>
                        @endif
                        <h3 class="page-title text-default" style="
                                padding: 3px 12px;
                                margin-right: 12px;
                                box-shadow: 0px 1px 3px #aaa;
                                text-shadow: 1px 1px 4px #4a4a4a;
                                color: #ffffff;
                        ">
                            <small>#</small> {{ $campaign->id }}:
                            {{ ucwords($campaign->name) }}
                        </h3>
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
                                            <!-- Classic List -->
                                            <li id="dashboard-link" class="nav-item">
                                                <a id="dashboard-nav-link" class="nav-link" href="{{ secure_url('campaign/' . $campaign->id ) }}">
                                                    <i class="icon oi-dashboard"></i>
                                                    Dashboard
                                                </a>
                                            </li>
                                            <!-- Accordion Demo -->
                                            <li id="drops-link" class="nav-item">
                                                <a id="drops-nav-link" class="nav-link" href="{{ secure_url('campaign/' . $campaign->id . '/drops') }}">
                                                    <i class="icon icon-lg wi-raindrops" style="font-size: 28px;"></i>
                                                    Drops
                                                </a>
                                            </li>
                                            <!-- Classic Dropdown -->
                                            <li id="recipients-link" class="nav-item">
                                                <a id="recipients-nav-link" class="nav-link" href="{{ secure_url('campaign/' . $campaign->id . '/recipients') }}">
                                                    <i class="icon oi-broadcast"></i>
                                                    Recipients
                                                </a>
                                            </li>
                                            <!-- Pictures -->
                                            <li id="responses-link" class="nav-item">
                                                <a id="responses-nav-link" class="nav-link" href="{{ secure_url('campaign/' . $campaign->id . '/responses') }}">
                                                    <i class="icon fa-paper-plane"></i>
                                                    Responses
                                                </a>
                                            </li>
                                            <li id="edit-link" class="nav-item">
                                                <a id="edit-nav-link" href="{{ secure_url('/campaign/' . $campaign->id . '/edit' ) }}" class="nav-link">
                                                    <i class="icon md-edit" aria-hidden="true"></i>
                                                    Edit
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body container-fluid" style="padding-top:20px;">
                            <div class="row-fluid">
                                @yield('campaign_content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
