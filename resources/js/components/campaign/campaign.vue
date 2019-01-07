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
            <div class="campaign-postcard">
                <div class="campaign-postcard--image">
                    <img src="" alt="">
                </div>
                <div class="campaign-postcard--value">
                    <strong>9x12 PostCard</strong>
                    <p>Car & Buyer BB</p>
                    <p>Mailer - TXT for Value</p>
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
            <a :href="generateRoute(campaignViewUrl, {'campaignId': campaign.id})"><span class="fa fa-search"></span> Stats</a>
            <a :href="generateRoute(campaignDropIndex, {'campaignId': campaign.id})"><span class="fas fa-tint"></span> Drops</a>
            <a :href="generateRoute(campaignRecipientIndex, {'campaignId': campaign.id})"><span class="fa fa-users"></span> Recipients</a>
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
        <div class="col-4 col-md-2 campaign-postcard--image">
            <img src="" alt="">
        </div>
        <div class="col-4 col-md-2 campaign-date">
            <span class="label">End Date:</span>
            <span class="value">{{ campaign.ends_at | amDateFormat('MM.DD.YY') }}</span>
    </div>
        <div class="col-4 col-md-3 campaign-chart">
            <div class="row no-gutters">
                <div class="col-6 campaign-chart--charts">
                    <pie-chart height="70px" :colors="['#572E8D', '#e7f386', '#67A7CC']" :legend="false" :data="pieChartDataSet"></pie-chart>
                </div>
                <div class="col-6 campaign-chart--labels">
                    <span class="sms">sms</span>
                    <span class="call">call</span>
                    <span class="email">email</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import moment from 'moment';
    import {generateRoute} from './../../common/helpers'

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
                campaignClosed: true,
                campaignViewUrl: '',
                campaignDropIndex: '',
                campaignRecipientIndex: '',
                campaignResponseConsoleIndex: ''
            };
        },
        computed: {
            daysLeft: function () {
                return moment(this.campaign.starts_at, 'YYYY-MM-DD').diff(moment(this.campaign.ends_at, 'YYYY-MM-DD'), 'days');
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
            this.campaignViewUrl = window.campaignViewUrl;
            this.campaignDropIndex = window.campaignDropIndex;
            this.campaignRecipientIndex = window.campaignRecipientIndex;
            this.campaignResponseConsoleIndex = window.campaignResponseConsoleIndex;
        }
    }
</script>
