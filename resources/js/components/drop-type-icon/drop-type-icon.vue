<template>
    <div class="campaign-drop-type-icon">
        <span class="pm-font-mail-icon" v-if="type === 'email'"></span>
        <span class="pm-font-sms-icon" v-if="type === 'message'"></span>
        <span class="pm-font-templates-icon" v-if="type === 'map'"></span>
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
