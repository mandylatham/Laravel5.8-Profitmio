<template>
    <div class="drop-status-component" :class="classesToAdd">{{ status }}</div>
</template>
<script>
    export default {
        props: {
            status: {
                type: String,
                required: true,
            }
        },
        computed: {
            classesToAdd: function () {
                if (this.status === 'Completed') {
                    return 'completed';
                } else {
                    return 'pending';
                }
            }
        },
        data() {
            return {};
        }
    }
</script>
