@extends('layouts.base', [
    'hasSidebar' => true
])

@section('head-styles')
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.searchFormUrl = "{{ route('campaign.for-user-display') }}";
        window.q = @json($q);
    </script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection

@section('sidebar-content')
    <div class="calendar-filters">
        <p-radio class="p-default p-round" name="radio1">Filter 1</p-radio>
        <p-radio class="p-default p-round" name="radio1">Filter 2</p-radio>
        <p-radio class="p-default p-round" name="radio1">Filter 3</p-radio>
        {{--<div class="form-check">--}}
            {{--<input class="form-check-input" type="radio" value="option2" checked>--}}
            {{--<label class="form-check-label" for="exampleRadios2">--}}
                {{--Filter--}}
            {{--</label>--}}
        {{--</div>--}}
        {{--<div class="form-check">--}}
            {{--<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option1"--}}
                   {{--checked>--}}
            {{--<label class="form-check-label" for="exampleRadios2">--}}
                {{--Filter--}}
            {{--</label>--}}
        {{--</div>--}}
        {{--<div class="form-check">--}}
            {{--<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1"--}}
                   {{--checked>--}}
            {{--<label class="form-check-label" for="exampleRadios1">--}}
                {{--Filter--}}
            {{--</label>--}}
        {{--</div>--}}
    </div>
    <date-pick class="event-calendar" :parse-date="parseDate" v-model="selectedDate" :has-input-element="false"></date-pick>
    <div class="events">
        <header>
            <span class="date">@{{ selectedDate | amDateFormat('DD') }}</span>
            <span class="label">@{{ selectedDate | amDateFormat('MMMM Do, YYYY') }}</span>
        </header>
        <div class="event-list">
            <div class="event"></div>
            <div class="event"></div>
            <div class="event"></div>
            <div class="event"></div>
            <div class="event"></div>
            <div class="event"></div>
            <div class="event"></div>
            <div class="event"></div>
        </div>
    </div>
@endsection

@section('main-content')
    <div class="container" id="dashboard">
        <div class="row align-items-end no-gutters mb-md-3">
            <div class="col-12 col-sm-5 col-lg-3">
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3">
                <input type="text" v-model="searchForm.q" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" @keyup.enter="fetchData">
            </div>
        </div>
        <div class="row align-items-end no-gutters mt-3">
            <div class="col-12">
                <div class="loader-spinner" v-if="isLoading">
                    <spinner-icon></spinner-icon>
                </div>
                <div class="no-items-row" v-if="countActiveCampaigns.length === 0 && countInactiveCampaigns === 0">
                    No Items
                </div>
                <div class="campaign-group-label" v-if="countActiveCampaigns > 0">ACTIVE</div>
                <campaign v-for="campaign in campaigns" v-if="campaign.status === 'Active'" :key="campaign.id" :campaign="campaign"></campaign>
                <div class="campaign-group-label" v-if="countInactiveCampaigns > 0">INACTIVE</div>
                <campaign v-for="campaign in campaigns" v-if="campaign.status !== 'Active'" :key="campaign.id" :campaign="campaign"></campaign>
                <pm-pagination :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
            </div>
        </div>
    </div>
@endsection

{{--@extends('layouts.base')--}}

{{--@section('content')--}}
{{--<div id="wrapper">--}}
    {{--<div class="wrapper-aside" id="wrapper-aside">--}}
        {{--<div class="calendar-filters">--}}
            {{--<div class="form-check">--}}
                {{--<input class="form-check-input" type="radio" value="option2" checked>--}}
                {{--<label class="form-check-label" for="exampleRadios2">--}}
                    {{--Filter--}}
                {{--</label>--}}
            {{--</div>--}}
            {{--<div class="form-check">--}}
                {{--<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option1"--}}
                       {{--checked>--}}
                {{--<label class="form-check-label" for="exampleRadios2">--}}
                    {{--Filter--}}
                {{--</label>--}}
            {{--</div>--}}
            {{--<div class="form-check">--}}
                {{--<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1"--}}
                       {{--checked>--}}
                {{--<label class="form-check-label" for="exampleRadios1">--}}
                    {{--Filter--}}
                {{--</label>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<date-pick class="event-calendar" :parse-date="parseDate" v-model="selectedDate" :has-input-element="false"></date-pick>--}}
        {{--<div class="events">--}}
            {{--<header>--}}
                {{--<span class="date">@{{ selectedDate | format('DD') }}</span>--}}
                {{--<span class="label">@{{ selectedDate | format('MMMM Do, YYYY') }}</span>--}}
            {{--</header>--}}
            {{--<div class="event-list">--}}
                {{--<div class="event"></div>--}}
                {{--<div class="event"></div>--}}
                {{--<div class="event"></div>--}}
                {{--<div class="event"></div>--}}
                {{--<div class="event"></div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="wrapper-content" id="campaign-list">--}}
        {{--<h3>ACTIVE CAMPAIGN</h3>--}}
        {{--<div class="campaign" :class="{'open': open[1]}">--}}
            {{--<div class="row no-gutters">--}}
                {{--<div class="col-12 col-xl-6 campaign-resume">--}}
                    {{--<header @click="toggle(1)">--}}
                        {{--<span class="campaign-resume--status"></span>--}}
                        {{--<div class="campaign-resume--title">--}}
                            {{--<strong>CAMPAIGN 677</strong>--}}
                            {{--<span>HEALEY FORD LINCOLN “PM6000”</span>--}}
                        {{--</div>--}}
                    {{--</header>--}}
                    {{--<div class="campaign-resume--data d-none d-xl-block">--}}
                        {{--<div class="row no-gutters">--}}
                            {{--<div class="col-5 campaign-resume--data-assets">--}}
                                {{--<img src="/img/img.png" alt="Image">--}}
                            {{--</div>--}}
                            {{--<div class="col-7 campaign-resume--data-info">--}}
                                {{--<strong>9 x 12 Postcard</strong>--}}
                                {{--<p>Card & Buyer BB</p>--}}
                                {{--<p>Mailer - TXT for Value</p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-12 col-xl-6 campaign-collapsable">--}}
                    {{--<div class="row no-gutters">--}}
                        {{--<div class="col-12 d-xl-none">--}}
                            {{--<div class="campaign-resume--data">--}}
                                {{--<div class="row no-gutters">--}}
                                    {{--<div class="col-5 campaign-resume--data-assets">--}}
                                        {{--<img src="/img/img.png" alt="Image">--}}
                                    {{--</div>--}}
                                    {{--<div class="col-7 campaign-resume--data-info">--}}
                                        {{--<strong>9 x 12 Postcard</strong>--}}
                                        {{--<p>Card & Buyer BB</p>--}}
                                        {{--<p>Mailer - TXT for Value</p>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-12 col-md-6 campaign-date">--}}
                            {{--<div class="campaign-date--date">--}}
                                {{--<span class="campaign-date--date-label">Start Date</span>--}}
                                {{--<span class="campaign-date--date-value">11.21.18</span>--}}
                            {{--</div>--}}
                            {{--<div class="campaign-date--date">--}}
                                {{--<span class="campaign-date--date-label">Start Date</span>--}}
                                {{--<span class="campaign-date--date-value">11.21.18</span>--}}
                            {{--</div>--}}
                            {{--<div class="day-left">--}}
                                {{--<span class="date-label">Days Left:</span>--}}
                                {{--<span class="date-value">6</span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-12 col-md-6 campaign-chart campaign-button-block">--}}
                            {{--<button class="btn btn-secondary btn-block">A</button>--}}
                            {{--<button class="btn btn-secondary btn-block">B</button>--}}
                            {{--<button class="btn btn-secondary btn-block">C</button>--}}
                            {{--<button class="btn btn-secondary btn-block">D</button>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<h3>EXPIRED CAMPAIGN</h3>--}}
        {{--<div class="campaign inactive" :class="{'open': open[2]}">--}}
            {{--<div class="row no-gutters">--}}
                {{--<div class="col-12 col-xl-6 campaign-resume">--}}
                    {{--<header @click="toggle(2)">--}}
                        {{--<span class="campaign-resume--status"></span>--}}
                        {{--<div class="campaign-resume--title">--}}
                            {{--<strong>CAMPAIGN 677</strong>--}}
                            {{--<span>HEALEY FORD LINCOLN “PM6000”</span>--}}
                        {{--</div>--}}
                    {{--</header>--}}
                {{--</div>--}}
                {{--<div class="col-12 col-xl-6 campaign-collapsable">--}}
                    {{--<div class="row no-gutters">--}}
                        {{--<div class="col-12 col-lg-6 campaign-date">--}}
                            {{--<div class="row no-gutters">--}}
                                {{--<div class="col-6 campaign-date--asset">--}}
                                    {{--<img src="/img/img.png" alt="Image">--}}
                                {{--</div>--}}
                                {{--<div class="col-6 campaign-date--date">--}}
                                    {{--<span class="campaign-date--date-label">End Date:</span>--}}
                                    {{--<span class="campaign-date--date-value">11.21.18</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-12 col-lg-6 campaign-chart">--}}
                            {{--<div class="row no-gutters">--}}
                                {{--<div class="col-6 campaign-chart--charts">--}}
                                    {{--<img src="/img/pie.png" alt="Image">--}}
                                {{--</div>--}}
                                {{--<div class="col-6 campaign-chart--labels">--}}
                                    {{--<span class="sms">sms</span>--}}
                                    {{--<span class="call">call</span>--}}
                                    {{--<span class="email">email</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="campaign inactive" :class="{'open': open[3]}">--}}
            {{--<div class="row no-gutters">--}}
                {{--<div class="col-12 col-xl-6 campaign-resume">--}}
                    {{--<header @click="toggle(3)">--}}
                        {{--<span class="campaign-resume--status"></span>--}}
                        {{--<div class="campaign-resume--title">--}}
                            {{--<strong>CAMPAIGN 677</strong>--}}
                            {{--<span>HEALEY FORD LINCOLN “PM6000”</span>--}}
                        {{--</div>--}}
                    {{--</header>--}}
                {{--</div>--}}
                {{--<div class="col-12 col-xl-6 campaign-collapsable">--}}
                    {{--<div class="row no-gutters">--}}
                        {{--<div class="col-12 col-lg-6 campaign-date">--}}
                            {{--<div class="row no-gutters">--}}
                                {{--<div class="col-6 campaign-date--asset">--}}
                                    {{--<img src="/img/img.png" alt="Image">--}}
                                {{--</div>--}}
                                {{--<div class="col-6 campaign-date--date">--}}
                                    {{--<span class="campaign-date--date-label">End Date:</span>--}}
                                    {{--<span class="campaign-date--date-value">11.21.18</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-12 col-lg-6 campaign-chart">--}}
                            {{--<div class="row no-gutters">--}}
                                {{--<div class="col-6 campaign-chart--charts">--}}
                                    {{--<img src="/img/pie.png" alt="Image">--}}
                                {{--</div>--}}
                                {{--<div class="col-6 campaign-chart--labels">--}}
                                    {{--<span class="sms">sms</span>--}}
                                    {{--<span class="call">call</span>--}}
                                    {{--<span class="email">email</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
{{--@endsection--}}
