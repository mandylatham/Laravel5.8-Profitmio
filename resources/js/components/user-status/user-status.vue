<template>
    <span class="user-status-component badge" :class="classesToAdd">{{ status }}</span>
</template>
<script>
    export default {
        props: {
            isActive: {
                type: Boolean,
                required: true,
            }
        },
        computed: {
            status: function () {
                return this.isActive ? 'ACTIVE' : 'INACTIVE';
            },
            classesToAdd: function () {
                if (this.isActive) {
                    return ['badge-success'];
                } else {
                    return ['badge-danger'];
                }
            }
        },
        data() {
            return {};
        }
    }
</script>
