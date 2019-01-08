<template>
    <div class="status-component">
        <span class="status-icon" :class="{'active': active, 'inactive': !active}"></span>
        <span class="status-value" :class="{'d-none': hiddenValue}">{{ activeValue }}</span>
    </div>
</template>
<script>
    export default {
        props: {
            active: {
                type: Boolean,
                required: true,
            }
        },
        computed: {
            activeValue: function () {
                return this.active ? 'ACTIVE' : 'INACTIVE';
            },
            hiddenValue: function () {
                return this.$attrs.hasOwnProperty('no-label');
            }
        },
        data() {
            return {};
        }
    }
</script>
