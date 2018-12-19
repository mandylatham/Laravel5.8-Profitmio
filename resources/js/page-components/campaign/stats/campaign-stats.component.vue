<template>
    <div class="stats">
        <div class="chart--container">
            <div class="chart--title clearfix">
                <h2>EMAIL</h2>
                <small>This shows the email sent over time</small>
            </div>
            <div class="chart--chart"></div>
        </div>
        <div class="chart--container">
            <div class="chart--title clearfix">
                <h2>RESPONSES</h2>
                <small>This shows the SMSs sent over time</small>
            </div>
            <div class="chart--chart"></div>
        </div>
    </div>
</template>
<script>
    require("./campaign-stats.scss");
    export default {
        data() {
            return {};
        }
    }
</script>
