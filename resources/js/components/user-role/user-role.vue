<template>
    <span class="user-role-component badge" :class="classesToAdd" :title="title">{{ role | userRole(shortVersion) }}</span>
</template>
<script>
    export default {
        props: {
            role: {
                type: String,
                required: true,
            },
            shortVersion: {
                type: Boolean,
                required: false,
                default: true
            }
        },
        computed: {
            title: function () {
                return this.$options.filters.userRole(this.role);
            },
            classesToAdd: function () {
                if (this.role === 'admin') {
                    return ['badge-success'];
                } else if (this.role === 'user') {
                    return ['badge-dark'];
                } else if (this.role === 'site_admin') {
                    return ['badge-primary'];
                }
            }
        },
        data() {
            return {};
        }
    }
</script>
