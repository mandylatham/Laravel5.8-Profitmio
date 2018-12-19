<template>
    <div class="campaign-drop-type-icon">
        <mail-icon v-if="type === 'email'"></mail-icon>
        <message-square-icon v-if="type === 'message'"></message-square-icon>
        <map-icon v-if="type === 'map'"></map-icon>
    </div>
</template>
<script>
    require("./drop-type-icon.scss");

    export default {
        props: {
            type: {
                type: String,
                required: true,
            }
        },
        data() {
            return {};
        }
    }
</script>
