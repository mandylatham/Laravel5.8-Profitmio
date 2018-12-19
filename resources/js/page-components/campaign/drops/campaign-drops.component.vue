<template>
    <div id="wrapper" class="wrapper--campaign-view" :class="{'open': asideOpen}">
        <div class="wrapper-aside">
            <a href="javascript:;" class="wrapper-aside-close-control" @click="asideOpen = false;">
                <x-icon></x-icon>
            </a>
            <a href="javascript:;" class="wrapper-aside-control" @click="asideOpen = !asideOpen">
                <menu-icon></menu-icon>
            </a>
            <div class="campaign-avatar"></div>
            <nav class="wrapper-aside--navigation">
                <ul>
                    <li>
                        <a class="active" href="">STATS</a>
                    </li>
                    <li>
                        <a href="">DROPS</a>
                    </li>
                    <li>
                        <a href="">RECIPIENTS</a>
                    </li>
                    <li>
                        <a href="">RESPONSES</a>
                    </li>
                    <li>
                        <a href="">EDIT</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="wrapper-content">
            <div class="chart">
                <div class="chart--title clearfix">
                    <h2>EMAIL</h2>
                    <small>This shows the email sent over time</small>
                </div>
                <div class="chart--chart"></div>
            </div>
            <div class="chart">
                <div class="chart--title clearfix">
                    <h2>RESPONSES</h2>
                    <small>This shows the SMSs sent over time</small>
                </div>
                <div class="chart--chart"></div>
            </div>
        </div>
    </div>
</template>
<script>
    require("./campaign-drops.scss");
    export default {
        data() {
            return {
                asideOpen: false
            };
        }
    }
</script>
