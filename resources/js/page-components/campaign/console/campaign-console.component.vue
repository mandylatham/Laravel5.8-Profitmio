<template>
    <div id="wrapper" class="wrapper--campaign-console" :class="{'open': asideOpen}">
        <div class="wrapper-aside">
            <a href="javascript:;" class="wrapper-aside-close-control" @click="asideOpen = false;">
                <x-icon></x-icon>
            </a>
            <a href="javascript:;" class="wrapper-aside-control" @click="asideOpen = !asideOpen">
                <menu-icon></menu-icon>
            </a>

            <nav class="wrapper-aside--navigation">
                <ul class="filter">
                    <li class="all">
                        <a :class="{'active': activeFilterSection === 'all'}" href="javascript:;"
                           @click="changeFilter('all')">All
                            <span class="counter">25</span></a>
                    </li>
                    <li class="unread">
                        <a :class="{'active': activeFilterSection === 'unread'}" href="javascript:;"
                           @click="changeFilter('unread')">Unread
                            <span class="counter">0</span></a>
                    </li>
                    <li class="idle">
                        <a :class="{'active': activeFilterSection === 'idle'}" href="javascript:;"
                           @click="changeFilter('idle')">Idle
                            <span class="counter">28</span></a>
                    </li>
                </ul>

                <hr>
                <h4>Media</h4>

                <ul class="media-type">
                    <li class="calls">
                        <a :class="{'active': activeMediaTypeSection === 'calls'}" href="javascript:;"
                           @click="changeMediaType('calls')">Calls
                            <span class="counter">28</span></a>
                    </li>
                    <li class="email">
                        <a :class="{'active': activeMediaTypeSection === 'email'}" href="javascript:;"
                           @click="changeMediaType('email')">Email
                            <span class="counter">0</span></a>
                    </li>
                    <li class="sms">
                        <a :class="{'active': activeMediaTypeSection === 'sms'}" href="javascript:;"
                           @click="changeMediaType('sms')">SMS
                            <span class="counter">25</span></a>
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
        </div>
        <div class="wrapper-content">
            <campaign-console-responses></campaign-console-responses>
        </div>
    </div>
</template>
<script>
    require("./campaign-console.scss");
    export default {
        data() {
            return {
                asideOpen: false,
                activeFilterSection: 'all',
                activeMediaTypeSection: '',
                activeLabelSection: '',
            };
        },
        methods: {
            changeFilter: function (item) {
                this.activeFilterSection = item;
                this.asideOpen = false;
            },
            changeMediaType: function (item) {
                this.activeMediaTypeSection = item;
                this.asideOpen = false;
            },
            changeLabel: function (item) {
                this.activeLabelSection = item;
                this.asideOpen = false;
            }
        }
    }
</script>
