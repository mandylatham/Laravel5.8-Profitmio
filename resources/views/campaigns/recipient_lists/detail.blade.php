@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/recipients-detail.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchRecipientsUrl = @json(route('campaigns.recipient-lists.recipients.for-user-display', ['campaign' => $campaign->id, 'list' => $list->id]));
        window.deleteRecipientsUrl = @json(route('campaigns.recipients.delete', ['campaign' => $campaign->id]));
    </script>
    <script src="{{ asset('js/recipients-detail.js') }}"></script>
@endsection

@section('main-content')
    <div class="container" id="recipients-detail" v-cloak>
        <div class="row">
            <div class="col mb-3">
                <h2>List {{ $list->name }} Recipients</h2>
            </div>
        </div>
        <div class="row align-items-end mb-3">
            <div class="col-12 col-sm-5 col-lg-4">
                <button class="btn btn-danger pm-btn" :disabled="recipients.length === 0" type="button" @click="deleteRecipients()"><i class="far fa-trash-alt mr-3"></i>Delete Checked</button>
            </div>
            <div class="col-none col-sm-2 col-lg-4"></div>
            <div class="col-12 col-sm-5 col-lg-4">
                <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" @keyup.enter="fetchData()">
            </div>
        </div>
        <div>
            <div class="table-loader-spinner" v-if="loading">
                <spinner-icon></spinner-icon>
            </div>
            <div class="no-items-row" v-if="recipients.length === 0">
                No Items
            </div>
            <div class="table-container" v-if="recipients.length > 0">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" width="60px">
                                <p-check color="primary" class="p-default" name="checkAll" v-model="checkAll" @change="selectAllRecipients"></p-check>
                            </th>
                            <th>Person</th>
                            <th>Address</th>
                            <th>Vehicle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="recipient in recipients">
                            <td class="text-center">
                                <i v-if="recipient.dropped_at" class="fas fa-paper-plane"></i>
                                <p-check v-if="!recipient.dropped_at" color="primary" class="p-default" name="checkAll" v-model="recipient.checked"></p-check>
                            </td>
                            <td>
                                <div>@{{ recipient.name }}</div>
                                <div>@{{ recipient.email }}</div>
                                <div>@{{ recipient.phone }}</div>
                            </td>
                            <td>
                                <div>@{{ recipient.address1 }}</div>
                                <div>@{{ recipient.city }}, @{{ recipient.state }} @{{ recipient.zip }}</div>
                            </td>
                            <td>
                                <div>@{{ recipient.vehicle }}</div>
                                <div>@{{ recipient.vin }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <pm-pagination class="mt-3" :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
            </div>
        </div>
    </div>
@endsection

{{--@extends('layouts.remark_campaign')--}}

{{--@section('header')--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('css/sweetalert.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/octicons/octicons.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('fonts/font-awesome/font-awesome.css') }}">--}}
    {{--<link type="text/css" rel="stylesheet" href="{{ secure_url('vendor/icheck/icheck.css') }}">--}}
{{--@endsection--}}

{{--@section('manualStyle')--}}
{{--@endsection--}}

{{--@section('campaign_content')--}}
        {{--<div class="col-md-12">--}}
            {{--<div class="container mb-20">--}}
                {{--<div class="row">--}}
                    {{--<div class="col-md-6">--}}
                        {{--<p class="h3">--}}
                            {{--List {{ $list->name }} Recipients--}}
                        {{--</p>--}}
                    {{--</div>--}}
                    {{--<div class="col-md-6">--}}
                        {{--<form method="get">--}}
                            {{--<div class="input-search">--}}
                                {{--<i class="input-search-icon md-search" aria-hidden="true"></i>--}}
                                {{--<input type="text" class="form-control" name="q" placeholder="Search..." value="{{ request('q') }}">--}}
                                {{--<button type="button"--}}
                                        {{--@if (request('q'))--}}
                                        {{--onClick="window.location.href = '{{ secure_url('campaigns/?q=') }}'"--}}
                                        {{--@endif--}}
                                        {{--class="input-search-close icon md-close" aria-label="Close"></button>--}}
                            {{--</div>--}}
                        {{--</form>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--@if ($recipients->count() > 0)--}}
                {{--<form action="{{ route("recipient.delete", [$campaign->id, $list->id]) }}" method="post">--}}
                    {{--{{ csrf_field() }}--}}
                    {{--<input type="hidden" name="_method" value="delete" />--}}
                    {{--@if (!$campaign->isExpired())--}}
                        {{--<div class="">--}}
                            {{--<button class="btn btn-danger mb-3 pull-right">--}}
                                {{--<i class="fa icon fa-trash-o"></i>--}}
                                {{--Delete Checked--}}
                            {{--</button>--}}
                        {{--</div>--}}
                    {{--@endif--}}
                    {{--@if ($errors->any())--}}
                        {{--<div class="alert alert-danger">--}}
                            {{--<ul>--}}
                                {{--@foreach ($errors->all() as $error)--}}
                                    {{--<li>{{ $error }}</li>--}}
                                {{--@endforeach--}}
                            {{--</ul>--}}
                        {{--</div>--}}
                    {{--@endif--}}
                    {{--<table class="table">--}}
                        {{--<thead>--}}
                        {{--<tr>--}}
                            {{--<th><div class="checkbox">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox" id="all_or_none">--}}
                                    {{--</label>--}}
                                {{--</div></th>--}}
                            {{--<th>Person</th>--}}
                            {{--<th>Address</th>--}}
                            {{--<th>Vehicle</th>--}}
                        {{--</tr>--}}
                        {{--</thead>--}}
                        {{--<tbody>--}}
                        {{--@foreach ($recipients as $recipient)--}}
                        {{--<tr>--}}
                            {{--<td>--}}
                                {{--@if (! in_array($recipient->id, $dropped))--}}
                                {{--<div class="checkbox">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox" name="recipient_ids[]" value="{{ $recipient->id }}">--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                                {{--@else--}}
                                    {{--<i class="icon far fa-paper-plane"></i>--}}
                                {{--@endif--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--<div>{{ $recipient->name }}</div>--}}
                                {{--<div>{{ $recipient->email }}</div>--}}
                                {{--<div>{{ $recipient->phone }}</div>--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--<div>{{ $recipient->address1 }}</div>--}}
                                {{--<div>{{ $recipient->city }}, {{ $recipient->state }} {{ $recipient->zip }}</div>--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--<div>{{ $recipient->year }} {{ $recipient->make }} {{ $recipient->model }}</div>--}}
                                {{--<div>{{ $recipient->vin }}</div>--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        {{--@endforeach--}}
                        {{--</tbody>--}}
                    {{--</table>--}}
                {{--</form>--}}
                {{--<div class="links">{{ $recipients->links() }}</div>--}}
            {{--@endif--}}
        {{--</div>--}}
{{--@endsection--}}

{{--@section('scriptTags')--}}
    {{--<script type="text/javascript" src="{{ secure_url('js/Plugin/panel.js') }}"></script>--}}
    {{--<script type="text/javascript" src="{{ secure_url('js/Plugin/sweetalert.min.js') }}"></script>--}}
    {{--<script type="text/javascript" src="{{ secure_url('js/Plugin/papaparse.min.js') }}"></script>--}}
    {{--<script src="{{ secure_url('vendor/icheck/icheck.js') }}"></script>--}}
    {{--@endsection--}}

{{--@section('scripts')--}}
    {{--$("#all_or_none").on('change', function () {--}}
        {{--if ($("#all_or_none").prop('checked')) {--}}
            {{--$("input[name='recipient_ids[]']").prop('checked', true);--}}
        {{--} else {--}}
            {{--$("input[name='recipient_ids[]']").prop('checked', false);--}}
        {{--}--}}
    {{--});--}}
{{--@endsection--}}
