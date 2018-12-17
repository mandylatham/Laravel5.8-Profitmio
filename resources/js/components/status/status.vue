<template>
    <div class="status-component">
        <span class="status-icon" :class="{'active': active, 'inactive': !active}"></span>
        <span class="status-value">{{ activeValue }}</span>
    </div>
</template>
<script>
    require("./status.scss");

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
            }
        },
        data() {
            return {};
        }
    }
</script>
