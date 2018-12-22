<template>
    <div class="wrapper-component" :class="{'open': sidebarOpen, 'has-sidebar': hasSidebar}">
        <div class="wrapper-sidebar-controls">
            <a href="javascript:;" class="wrapper-sidebar-controls-toggle" @click="sidebarOpen = !sidebarOpen">
                <slot name="sidebar-toggle">
                    <menu-icon></menu-icon>
                </slot>
            </a>
        </div>
        <VuePerfectScrollbar class="wrapper-sidebar" v-once>
            <slot name="wrapper-sidebar"></slot>
        </VuePerfectScrollbar>
        <VuePerfectScrollbar class="wrapper-content" v-once>
            <slot name="wrapper-content"></slot>
        </VuePerfectScrollbar>
    </div>
</template>
<script>
    require('./wrapper.scss');
    export default {
        name: 'wrapper',
        props: {
            hasSidebar: {
                default: false,
                type: Boolean,
                required: true
            }
        },
        data() {
            return {
                sidebarOpen: false
            };
        },
        methods: {
            closeSidebar: function () {
                this.sidebarOpen = false;
            }
        }
    };
</script>
