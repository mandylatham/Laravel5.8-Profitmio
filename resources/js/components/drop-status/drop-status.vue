<template>
    <div class="drop-status-component">{{ statusValue }}</div>
</template>
<script>
    require("./drop-status.scss");

    export default {
        props: {
            status: {
                type: String,
                required: true,
            }
        },
        computed: {
            statusValue: function () {
                return this.status.toUpperCase();
            }
        },
        data() {
            return {};
        }
    }
</script>
