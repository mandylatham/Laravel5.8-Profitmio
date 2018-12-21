<template>
    <wrapper class="wrapper--campaign-view" :has-sidebar="true">
        <template slot="wrapper-sidebar">
            <div class="company-data">
                <div class="company-avatar"></div>
                <div class="campaign-name">CAMPAIGN NAME</div>
                <div class="company-address">711 W Harvard ST Orlando, FL 3238, US</div>
                <div class="company-email">email@profitminer.io</div>
                <div class="company-phone">333-3333-333</div>
            </div>
            <nav class="wrapper-sidebar-navigation">
                <ul>
                    <li>
                        <a :class="{'active': activeSection === 'stats'}" href="javascript:;" @click="changePage('stats')">
                            <span class="pm-font-drops-icon"></span>
                            <span>STATS</span>
                        </a>
                    </li>
                    <li>
                        <a :class="{'active': activeSection === 'drops'}" href="javascript:;" @click="changePage('drops')">
                            <span class="pm-font-drops-icon"></span>
                            <span>DROPS</span>
                        </a>
                    </li>
                    <li>
                        <a :class="{'active': activeSection === 'recipients'}" href="javascript:;" @click="changePage('recipients')">
                            <span class="pm-font-recipients-icon"></span>
                            <span>RECIPIENTS</span>
                        </a>
                    </li>
                    <li>
                        <a :class="{'active': activeSection === 'responses'}" href="javascript:;" @click="changePage('responses')">
                            <span class="pm-font-responses-icon"></span>
                            <span>RESPONSES</span>
                        </a>
                    </li>
                    <li>
                        <a :class="{'active': activeSection === 'edit'}" href="javascript:;" @click="changePage('edit')">
                            <span class="pm-font-edit-icon"></span>
                            <span>EDIT</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </template>
        <template slot="wrapper-content">
            <campaign-stats v-if="activeSection === 'stats'"></campaign-stats>
            <campaign-drops v-if="activeSection === 'drops'"></campaign-drops>
            <campaign-recipients v-if="activeSection === 'recipients'"></campaign-recipients>
            <campaign-responses v-if="activeSection === 'responses'"></campaign-responses>
            <campaign-edit v-if="activeSection === 'edit'"></campaign-edit>
        </template>
    </wrapper>
    <!--<div id="wrapper" class="wrapper&#45;&#45;campaign-view" :class="{'open': asideOpen}">-->
        <!--<div class="wrapper-aside">-->
            <!--<a href="javascript:;" class="wrapper-aside-close-control" @click="asideOpen = false;">-->
                <!--<x-icon></x-icon>-->
            <!--</a>-->
            <!--<a href="javascript:;" class="wrapper-aside-control" @click="asideOpen = !asideOpen">-->
                <!--<menu-icon></menu-icon>-->
            <!--</a>-->
            <!--<div class="campaign-avatar"></div>-->
            <!--<nav class="wrapper-aside&#45;&#45;navigation">-->
                <!--<ul>-->
                    <!--<li>-->
                        <!--<a :class="{'active': activeSection === 'stats'}" href="javascript:;" @click="changePage('stats')">STATS</a>-->
                    <!--</li>-->
                    <!--<li>-->
                        <!--<a :class="{'active': activeSection === 'drops'}" href="javascript:;" @click="changePage('drops')">DROPS</a>-->
                    <!--</li>-->
                    <!--<li>-->
                        <!--<a :class="{'active': activeSection === 'recipients'}" href="javascript:;" @click="changePage('recipients')">RECIPIENTS</a>-->
                    <!--</li>-->
                    <!--<li>-->
                        <!--<a :class="{'active': activeSection === 'responses'}" href="javascript:;" @click="changePage('responses')">RESPONSES</a>-->
                    <!--</li>-->
                    <!--<li>-->
                        <!--<a :class="{'active': activeSection === 'edit'}" href="javascript:;" @click="changePage('edit')">EDIT</a>-->
                    <!--</li>-->
                <!--</ul>-->
            <!--</nav>-->
        <!--</div>-->
        <!--<div class="wrapper-content">-->
            <!--<campaign-stats v-if="activeSection === 'stats'"></campaign-stats>-->
            <!--<campaign-drops v-if="activeSection === 'drops'"></campaign-drops>-->
            <!--<campaign-recipients v-if="activeSection === 'recipients'"></campaign-recipients>-->
            <!--<campaign-responses v-if="activeSection === 'responses'"></campaign-responses>-->
            <!--<campaign-edit v-if="activeSection === 'edit'"></campaign-edit>-->
        <!--</div>-->
    <!--</div>-->
</template>
<script>
    require("./campaign-view.scss");
    export default {
        data() {
            return {
                asideOpen: false,
                activeSection: 'stats'
            };
        },
        methods: {
            changePage: function (page) {
                this.activeSection = page;
                this.asideOpen = false;
            }
        }
    }
</script>
