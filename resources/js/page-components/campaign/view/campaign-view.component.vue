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
                        <a :class="{'active': activeSection === 'stats'}" href="javascript:;" @click="changePage('stats')">STATS</a>
                    </li>
                    <li>
                        <a :class="{'active': activeSection === 'drops'}" href="javascript:;" @click="changePage('drops')">DROPS</a>
                    </li>
                    <li>
                        <a :class="{'active': activeSection === 'recipients'}" href="javascript:;" @click="changePage('recipients')">RECIPIENTS</a>
                    </li>
                    <li>
                        <a :class="{'active': activeSection === 'responses'}" href="javascript:;" @click="changePage('responses')">RESPONSES</a>
                    </li>
                    <li>
                        <a :class="{'active': activeSection === 'edit'}" href="javascript:;" @click="changePage('edit')">EDIT</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="wrapper-content">
            <campaign-stats v-if="activeSection === 'stats'"></campaign-stats>
            <campaign-drops v-if="activeSection === 'drops'"></campaign-drops>
            <campaign-recipients v-if="activeSection === 'recipients'"></campaign-recipients>
            <campaign-responses v-if="activeSection === 'responses'"></campaign-responses>
            <campaign-edit v-if="activeSection === 'edit'"></campaign-edit>
        </div>
    </div>
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
