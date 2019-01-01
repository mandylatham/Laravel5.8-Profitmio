<template>
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="campaign-header">
                <div class="campaign-header--status">
                    <status :hidden-value="true" :active="campaign.status"></status>
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
        <div class="col-12 col-sm-6 col-md-3 campaign-date">
            <p>
                <span class="label">Start Date</span>
                <span class="value">11.21.18</span>
            </p>
            <p>
                <span class="label">End Date</span>
                <span class="value">11.21.18</span>
            </p>
            <div class="campaign-date--left">
                <small>Days Left</small>
                <strong>6</strong>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <button class="btn pm-btn">A</button>
            <button class="btn pm-btn">B</button>
            <button class="btn pm-btn">C</button>
            <button class="btn pm-btn">D</button>
        </div>
    </div>
</template>
<script>
    require("./campaign.scss");

    export default {
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
            return {};
        }
    }
</script>
