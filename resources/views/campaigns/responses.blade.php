@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/campaigns-responses.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script src="{{ asset('js/campaigns-responses.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="campaign-responses" v-cloak>
        <div class="row">
            <div class="col-12 col-sm-10 col-md-8">
                <a href="{{ url('/campaign/' . $campaign->id . '/responses/export-responders') }}" class="download-response mb-3">
            <span class="icon">
                <download-icon></download-icon>
            </span>
                    <span class="description">
                <strong>RESPONDERS</strong>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid amet at autem culpa distinctio ex excepturi, fugiat fugit illo inventore labore nemo quidem ratione recusandae ut, veniam vero voluptatem voluptatum.</p>
            </span>
                </a>

                <a href="{{ url('/campaign/' . $campaign->id . '/responses/export-nonresponders') }}" class="download-response mb-3">
            <span class="icon">
                <download-icon></download-icon>
            </span>
                    <span class="description">
                <strong>NON-RESPONDERS</strong>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid amet at autem culpa distinctio ex excepturi, fugiat fugit illo inventore labore nemo quidem ratione recusandae ut, veniam vero voluptatem voluptatum.</p>
            </span>
                </a>
            </div>
        </div>
    </div>
@endsection

{{--@extends('layouts.remark_campaign')--}}

{{--@section('header')--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">--}}
    {{--<style type="text/css">--}}
        {{--h3.page-title {--}}
        {{--}--}}
    {{--</style>--}}
{{--@endsection--}}

{{--@section('campaign_content')--}}
    {{--<div class="col-md-12">--}}
        {{--@if (count($responses) > 0)--}}
        {{--<div class="pull-right">--}}
            {{--<a href="{{ url('/campaign/' . $campaign->id . '/responses/export-responders') }}"--}}
               {{--id="export-responders"--}}
               {{--class="btn btn-primary">--}}
                {{--<i class="icon md-download"></i>--}}
                {{--Responders--}}
            {{--</a>--}}

            {{--<a href="{{ url('/campaign/' . $campaign->id . '/responses/export-nonresponders') }}"--}}
               {{--id="export-nonresponders"--}}
               {{--class="btn btn-primary">--}}
                {{--<i class="icon md-download"></i>--}}
                {{--Non-Responders--}}
            {{--</a>--}}
        {{--</div>--}}
        {{--@endif--}}
        {{--<h3>Responses <small>({{ count($responses) }})</small></h3>--}}
        {{--<div class="table-responsive">--}}
            {{--@if (count($responses) > 0)--}}
                {{--<table class="table table-striped table-hover table-bordered">--}}
                    {{--<thead>--}}
                    {{--<tr>--}}
                        {{--<th>ID</th>--}}
                        {{--<th>Name</th>--}}
                        {{--<th>Phone</th>--}}
                        {{--<th>Email</th>--}}
                        {{--<th>Year</th>--}}
                        {{--<th>Make</th>--}}
                        {{--<th>Model</th>--}}
                    {{--</tr>--}}
                    {{--</thead>--}}
                    {{--<tbody>--}}
                    {{--@foreach ($responses as $response)--}}
                        {{--<tr>--}}
                            {{--<td>{{ $response['meta']->id }}</td>--}}
                            {{--<td>{{ $response['meta']->name }} ({{ $response['meta']->recipient_id }})</td>--}}
                            {{--<td>{{ $response['meta']->phone }}</td>--}}
                            {{--<td>{{ $response['meta']->email }}</td>--}}
                            {{--<td>{{ $response['meta']->year }}</td>--}}
                            {{--<td>{{ $response['meta']->make }}</td>--}}
                            {{--<td>{{ $response['meta']->model }}</td>--}}
                        {{--</tr>--}}
                    {{--@endforeach--}}
                    {{--</tbody>--}}
                {{--</table>--}}
            {{--@endif--}}
        {{--</div>--}}
    {{--</div>--}}
{{--@endsection--}}

{{--@section('scriptTags')--}}
    {{--<script type="text/javascript" src="{{ secure_url('js/Plugin/panel.js') }}"></script>--}}
    {{--<script type="text/javascript" src="{{ secure_url('/vendor/chart-js/Chart.js') }}"></script>--}}
{{--@endsection--}}

{{--@section('scripts')--}}
{{--@endsection--}}
