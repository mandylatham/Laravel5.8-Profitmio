<template>
    <div class="row no-gutters campaign-component active" :id="'campaign-component-' + campaign.id" :class="{'closed': campaignClosed}" v-if="campaignActive">
        <div class="col-12 col-md-5">
            <div class="campaign-header" @click="toggleCampaign">
                <div class="campaign-header--status">
                    <status no-label :active="campaignActive"></status>
                </div>
                <div class="campaign-header--title">
                    <p><b>{{ campaign.id }}</b> - {{ campaign.dealership.name }}</p>
                    <p>{{ campaign.name }}</p>
                </div>

                <div class="campaign-header--dates" v-if="campaign.is_legacy === false">
                    <div>
                        <span class="label">Start Date:</span>
                        <span class="value">{{ campaign.starts_at | amDateFormat('MM.DD.YY') }}</span>
                    </div>
                    <div>
                        <span class="label">End Date:</span>
                        <span class="value">{{ campaign.ends_at | amDateFormat('MM.DD.YY') }}</span>
                    </div>
                </div>
            </div>
            <div class="campaign-postcard" v-if="campaign.text_responses_count > 0 || campaign.phone_responses_count > 0 || campaign.email_responses_count > 0">
                <div class="campaign-postcard--image">
                    <pie-chart height="70px" :colors="['#572E8D', '#e7f386', '#67A7CC']" :legend="false" :data="pieChartDataSet"></pie-chart>
                </div>
                <div class="campaign-postcard--value campaign-chart--labels">
                    <span class="sms">{{ campaign.text_responses_count }} sms</span>
                    <span class="call">{{ campaign.phone_responses_count }} call</span>
                    <span class="email">{{ campaign.email_responses_count }} email</span>
                </div>
            </div>
            <div class="campaign-postcard" v-if="campaign.text_responses_count === 0 && campaign.phone_responses_count === 0 && campaign.email_responses_count === 0">
                <div class="no-responses">
                    <i class="fas fa-chart-pie"></i>
                    <span class="no-responses-label">No Responses</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 campaign-date" v-if="campaign.is_legacy === true">
           <p>
               <span class="label">Start Date:</span>
               <span class="value">{{ campaign.starts_at | amDateFormat('MM.DD.YY') }}</span>
           </p>
           <p>
               <span class="label">End Date:</span>
               <span class="value">{{ campaign.ends_at | amDateFormat('MM.DD.YY') }}</span>
           </p>
           <div class="campaign-date--left">
               <small>Days Left:</small>
               <strong>{{ daysLeft | zeroIfNegative }}</strong>
           </div>
        </div>
        <div class="col-6 col-md-4 campaign-count" v-if="campaign.is_legacy === false">
            <div class="campaign-count--top">
                <div class="campaign-count--stat">
                    <span class="label">New:</span>
                    <span class="value">{{ campaign.counters.new }}</span>
                </div>
                <div class="campaign-count--stat">
                    <span class="label">Open:</span>
                    <span class="value">{{ campaign.counters.open }}</span>
                </div>
                <div class="campaign-count--stat">
                    <span class="label">Closed:</span>
                    <span class="value">{{ campaign.counters.closed }}</span>
                </div>
            </div>
            <div class="campaign-count--bottom">
                <span class="label">Unfinished Leads / Total Leads:</span>
                <span class="value">{{ campaign.counters.total - campaign.counters.closed }} / {{ campaign.counters.total }}</span>
            </div>
        </div>
        <div class="col-6 col-md-3 campaign-links" v-if="isAdmin">
            <a :href="generateRoute(campaignStatsUrl, {'campaignId': campaign.id})" v-if="campaign.is_legacy === false"><span class="far fa-chart-bar"></span> Stats</a>
            <a class="drop-link" :href="generateRoute(campaignDropIndex, {'campaignId': campaign.id})"><span class="fas fa-tint"></span> Drops</a>
            <a class="recipient-list-link" :href="generateRoute(campaignRecipientIndex, {'campaignId': campaign.id})"><span class="fa fa-users"></span> Recipients</a>
            <a :href="generateRoute(campaignResponseConsoleIndex, {'campaignId': campaign.id})"><span class="fa fa-terminal"></span> Console</a>
            <a :href="generateRoute(campaignEditUrl, {'campaignId': campaign.id})"><span class="fas fa-edit"></span> Edit</a>
        </div>
        <div class="col-6 col-md-3 campaign-links" v-else>
            <div class="campaign-apointment-totals">
                <i class="far fa-thumbs-up"></i>
                <div class="total"><div class="m-0 p-0">{{ campaign.interested_counts }}</div><div class="label">Interested</div></div>
            </div>
            <a :href="generateRoute(campaignResponseConsoleIndex, {'campaignId': campaign.id})" class="btn btn-console-outline pm-btn-outline-purple">
                <span class="fa fa-terminal"></span> Console
            </a>
        </div>
    </div>
    <div class="row no-gutters campaign-component inactive" v-else-if="!campaignActive">
        <div class="col-12 col-md-5 campaign-header">
            <div class="campaign-header--status">
                <status no-label :active="campaignActive"></status>
            </div>
            <div class="campaign-header--title">
                <strong>Campaign {{ campaign.id }}</strong>
                <p>{{ campaign.name }}</p>
            </div>
        </div>
        <div class="col-6 col-md-2 campaign-links">
            <div class="campaign-apointment-totals-inactive">
                <i class="far fa-calendar-check"></i>
                <div class="total"><div class="m-0 p-0">{{ campaign.appointment_counts }}</div><div class="label">Appointments</div></div>
            </div>
        </div>
        <div class="col-6 col-md-2 campaign-postcard--image campaign-links">
            <a :href="generateRoute(campaignStatsUrl, {'campaignId': campaign.id})" v-if="campaign.is_legacy === false"><span class="far fa-chart-bar"></span> Stats</a>
            <a :href="generateRoute(campaignEditUrl, {'campaignId': campaign.id})" v-if="isAdmin"><span class="fas fa-edit"></span> Edit</a>
            <a :href="generateRoute(campaignResponseConsoleIndex, {'campaignId': campaign.id})"><span class="fa fa-terminal"></span> Console</a>
        </div>
        <div class="col-12 col-md-3 campaign-chart">
            <div class="row no-gutters h-100" v-if="campaign.text_responses_count > 0 || campaign.phone_responses_count > 0 || campaign.email_responses_count > 0">
                <div class="col-7 campaign-chart--charts">
                    <pie-chart height="70px" :colors="['#572E8D', '#e7f386', '#67A7CC']" :legend="false" :data="pieChartDataSet"></pie-chart>
                </div>
                <div class="col-5 campaign-chart--labels">

                    <span class="sms">{{ campaign.text_responses_count }} sms</span>
                    <span class="call">{{ campaign.phone_responses_count }} call</span>
                    <span class="email">{{ campaign.email_responses_count }} email</span>
                </div>
            </div>
            <div class="no-responses" v-if="campaign.text_responses_count === 0 && campaign.phone_responses_count === 0 && campaign.email_responses_count === 0">
               <i class="fas fa-chart-pie"></i>
               <span class="no-responses-label">No Responses</span>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue';
    import moment from 'moment';
    import {generateRoute} from './../../common/helpers'

    // Chart Library
    import VueChartkick from 'vue-chartkick'
    import Chart from 'chart.js'
    Vue.use(VueChartkick, {adapter: Chart});

    export default {
        components: {
            'b-popover': require('bootstrap-vue/src/components/popover/popover').default,
            'status': require('./../status/status').default,
        },
        props: {
            campaign: {
                type: Object,
                required: true,
                default: function () {
                    return {};
                }
            }
        },
        data() {
            return {
                isAdmin: false,
                campaignClosed: true,
                campaignEditUrl: '',
                campaignStatsUrl: '',
                campaignDropIndex: '',
                campaignRecipientIndex: '',
                campaignResponseConsoleIndex: '',
                campaignStatsUrl: ''
            };
        },
        computed: {
            daysLeft: function () {
                return moment(this.campaign.ends_at, 'YYYY-MM-DD').add(1, 'd').diff(moment.utc(), 'days');
            },
            campaignActive: function () {
                return this.campaign.status === 'Active';
            },
            pieChartDataSet: function () {
                return [['sms', this.campaign.text_responses_count], ['call', this.campaign.phone_responses_count], ['email', this.campaign.email_responses_count]];
            }
        },
        filters: {
            zeroIfNegative: function (value) {
                return value < 0 ? 0 : value;
            }
        },
        methods: {
            toggleCampaign: function () {
                this.campaignClosed = !this.campaignClosed;
            },
            generateRoute
        },
        mounted: function () {
            this.isAdmin = window.isAdmin;
            this.campaignEditUrl = window.campaignEditUrl;
            this.campaignStatsUrl = window.campaignStatsUrl;
            this.campaignDropIndex = window.campaignDropIndex;
            this.campaignRecipientIndex = window.campaignRecipientIndex;
            this.campaignResponseConsoleIndex = window.campaignResponseConsoleIndex;
            this.campaignStatsUrl = window.campaignStatsUrl;
        }
    }
</script>
