@extends('layouts.remark')

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-xxl-10 offset-xxl-1 col-md-12">
                    <h3 class="page-title text-default">
                        <small>#</small> {{ $client->id }}:
                        {{ ucwords($client->organization) }}
                    </h3>
                    <div class="page-header-actions">
                        <button type="button"
                                role="button"
                                data-url="{{ secure_url('/client/' . $client->id . '/edit' ) }}"
                                class="btn btn-sm btn-icon btn-primary btn-round waves-effect campaign-edit-button"
                                data-toggle="tooltip"
                                data-original-title="Edit Client">
                            <i class="icon md-edit" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="row-fluid" data-plugin="matchHeight" data-by-row="true">
                <div class="col-xxl-10 offset-xxl-1 col-md-12">
                    <div class="panel">
                        <div class="panel-body container-fluid" style="padding-top:20px;">
                            <div class="row-fluid">
                                @yield('client_content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

