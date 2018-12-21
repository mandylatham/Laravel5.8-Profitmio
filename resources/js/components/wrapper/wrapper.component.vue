<template>
    <div class="wrapper-component" :class="{'open': asideOpen, 'has-sidebar': hasSidebar}">
        <div class="wrapper-sidebar-controls">
            <a href="javascript:;" class="wrapper-sidebar-controls-toggle" @click="asideOpen = !asideOpen">
                <slot name="sidebar-toggle">
                    <menu-icon></menu-icon>
                </slot>
            </a>
        </div>
        <div class="wrapper-sidebar">
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
                asideOpen: false
            };
        }
    };
</script>