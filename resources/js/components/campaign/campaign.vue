<template>
    <div class="row no-gutters campaign-component active" :class="{'closed': campaignClosed}" v-if="campaignActive">
        <div class="col-12 col-md-5">
            <div class="campaign-header" @click="toggleCampaign">
                <div class="campaign-header--status">
                    <status no-label :active="campaignActive"></status>
                </div>
                <div class="campaign-header--title">
                    <strong>Campaign {{ campaign.id }}</strong>
                    <p>{{ campaign.name }}</p>
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
        <div class="col-6 col-md-4 campaign-date">
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
                <strong>{{ daysLeft }}</strong>
            </div>
        </div>
        <div class="col-6 col-md-3 campaign-links">
            <a v-if="isAdmin" :href="generateRoute(campaignDropIndex, {'campaignId': campaign.id})"><span class="fas fa-tint"></span> Drops</a>
            <a v-if="isAdmin" :href="generateRoute(campaignRecipientIndex, {'campaignId': campaign.id})"><span class="fa fa-users"></span> Recipients</a>
            <a :href="generateRoute(campaignResponseConsoleIndex, {'campaignId': campaign.id})"><span class="fa fa-terminal"></span> Console</a>
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
        <div class="col-6 col-md-2 campaign-postcard--image" v-if="!isAdmin">
            <img src="" alt="">
        </div>
        <div class="col-6 col-md-2 campaign-postcard--image campaign-links" v-if="isAdmin">
            <a :href="generateRoute(campaignResponseConsoleIndex, {'campaignId': campaign.id})"><span class="fa fa-terminal"></span> Console</a>
        </div>
        <div class="col-6 col-md-2 campaign-date">
            <span class="label">End Date:</span>
            <span class="value">{{ campaign.ends_at | amDateFormat('MM.DD.YY') }}</span>
    </div>
        <div class="col-12 col-md-3 campaign-chart">
            <div class="row no-gutters" v-if="campaign.text_responses_count > 0 || campaign.phone_responses_count > 0 || campaign.email_responses_count > 0">
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
            'status': require('./../status/status'),
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
                campaignStatsUrl: '',
                campaignDropIndex: '',
                campaignRecipientIndex: '',
                campaignResponseConsoleIndex: ''
            };
        },
        computed: {
            daysLeft: function () {
                return moment(this.campaign.ends_at, 'YYYY-MM-DD').diff(moment(this.campaign.starts_at, 'YYYY-MM-DD'), 'days');
            },
            campaignActive: function () {
                return this.campaign.status === 'Active';
            },
            pieChartDataSet: function () {
                return [['sms', this.campaign.text_responses_count], ['call', this.campaign.phone_responses_count], ['email', this.campaign.email_responses_count]];
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
            this.campaignStatsUrl = window.campaignStatsUrl;
            this.campaignDropIndex = window.campaignDropIndex;
            this.campaignRecipientIndex = window.campaignRecipientIndex;
            this.campaignResponseConsoleIndex = window.campaignResponseConsoleIndex;
        }
    }
</script>
