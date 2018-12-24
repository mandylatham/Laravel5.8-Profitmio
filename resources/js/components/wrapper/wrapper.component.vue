<template>
    <div class="wrapper-component" :class="{'open': sidebarOpen, 'has-sidebar': hasSidebar}">
        <div class="wrapper-sidebar-controls">
            <a href="javascript:;" class="wrapper-sidebar-controls-toggle" @click="sidebarOpen = !sidebarOpen">
                <slot name="sidebar-toggle">
                    <menu-icon></menu-icon>
                </slot>
            </a>
        </div>
        <div class="wrapper-sidebar">
            <div class="wrapper-sidebar-close-control" @click="sidebarOpen = !sidebarOpen">
                <x-icon></x-icon>
            </div>
            <slot name="wrapper-sidebar"></slot>
        </div>
        <div class="wrapper-content">
            <slot name="wrapper-content"></slot>
        </div>
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
