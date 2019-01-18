<!-- TODO: check this file and remove it if needed -->
<template>
    <div id="wrapper" class="wrapper--campaign-console" :class="{'open': asideOpen}">
        <div class="wrapper-content">

        </div>
    </div>
</template>
<script>
    export default {
        mounted() {
            //
        },
        data() {
            return {
                asideOpen: false,
            };
        },
        computed: {
            //
        },
        components: {
            'campaign-console-responses': require('./../console-responses/campaign-console-responses.component'),
        },
        props: ['campaign', 'recipients']
    }
</script>
