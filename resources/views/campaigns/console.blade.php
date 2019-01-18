@extends('layouts.base', [
    'hasSidebar' => true
])

@section('head-styles')
    <link href="{{ asset('css/console.css') }}" rel="stylesheet">
@endsection
@section('body-script')
    <script>
        window.filter = @json($filter);
        window.label = @json($label);
        window.counters = @json($counters);
    </script>
    <script src="{{ asset('js/console.js') }}"></script>
@endsection

@section('sidebar-content')
    <nav id="sidebar-nav-content" class="wrapper-aside--navigation">
        <ul class="filter">
            <li class="all">
                <a :class="{'active': activeFilterSection === 'all'}" href="javascript:;"
                   @click="changeFilter('all')">All
                    <span class="counter">@{{ this.counters.totalCount }}</span></a>
            </li>
            <li class="unread">
                <a :class="{'active': activeFilterSection === 'unread'}" href="javascript:;"
                   @click="changeFilter('unread')">Unread
                    <span class="counter">@{{ this.counters.unread }}</span></a>
            </li>
            <li class="idle">
                <a :class="{'active': activeFilterSection === 'idle'}" href="javascript:;"
                   @click="changeFilter('idle')">Idle
                    <span class="counter">@{{ this.counters.idle }}</span></a>
            </li>
        </ul>

        <hr>
        <h4>Media</h4>

        <ul class="media-type">
            <li class="calls">
                <a :class="{'active': activeMediaTypeSection === 'calls'}" href="javascript:;"
                   @click="changeMediaType('calls')">Calls
                    <span class="counter">@{{ this.counters.calls }}</span></a>
            </li>
            <li class="email">
                <a :class="{'active': activeMediaTypeSection === 'email'}" href="javascript:;"
                   @click="changeMediaType('email')">Email
                    <span class="counter">@{{ this.counters.email }}</span></a>
            </li>
            <li class="sms">
                <a :class="{'active': activeMediaTypeSection === 'sms'}" href="javascript:;"
                   @click="changeMediaType('sms')">SMS
                    <span class="counter">@{{ this.counters.sms }}</span></a>
            </li>
        </ul>

        <hr>
        <h4>Labels</h4>

        <ul class="labels">
            <li class="no-label">
                <a :class="{'active': activeLabelSection === 'no-label'}" href="javascript:;"
                   @click="changeLabel('no-label')">No Label</a>
            </li>
            <li class="interested">
                <a :class="{'active': activeLabelSection === 'interested'}" href="javascript:;"
                   @click="changeLabel('interested')">Interested</a>
            </li>
            <li class="appointment">
                <a :class="{'active': activeLabelSection === 'appointment'}" href="javascript:;"
                   @click="changeLabel('appointment')">Appointment</a>
            </li>
            <li class="callback">
                <a :class="{'active': activeLabelSection === 'callback'}" href="javascript:;"
                   @click="changeLabel('callback')">Callback</a>
            </li>
            <li class="service-dept">
                <a :class="{'active': activeLabelSection === 'service-dept'}" href="javascript:;"
                   @click="changeLabel('service-dept')">Service Dept</a>
            </li>
            <li class="not-interested">
                <a :class="{'active': activeLabelSection === 'not-interested'}" href="javascript:;"
                   @click="changeLabel('not-interested')">Not Interested</a>
            </li>
            <li class="wrong-tag">
                <a :class="{'active': activeLabelSection === 'wrong-tag'}" href="javascript:;"
                   @click="changeLabel('wrong-tag')">Wrong #</a>
            </li>
        </ul>
    </nav>
@endsection

@section('content')
    <div id="console">

        {{--<campaign-console :campaign="{{ $campaign->toJson() }}" :recipients="{{ $recipients->toJson() }}"
                          :filter="{{ json_encode($filter) }}" :label="{{ json_encode($label) }}"
                          :counters="{{ json_encode($counters) }}"></campaign-console>--}}

        <campaign-console-responses :campaign="{{ $campaign->toJson() }}"
                                    :recipients="{{ $recipients->toJson() }}"></campaign-console-responses>
    </div>
@endsection