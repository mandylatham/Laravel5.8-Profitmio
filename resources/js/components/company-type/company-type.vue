<template>
    <span class="company-icon" :class="this.companyClass"></span>
</template>
<script>
    export default {
        props: {
            company_type: {
                type: String,
                required: true,
                default: function () {
                    return '';
                }
            }
        },
        computed: {
            companyClass: function () {
                if (this.company_type == 'agency') {
                    return 'fa fa-handshake';
                } else if (this.company_type == 'dealership') {
                    return 'fa fa-car';
                } else {
                    return 'fa fa-question';
                }
            },
            hiddenValue: function () {
                return this.$attrs.hasOwnProperty('no-label');
            }
        },
        filters: {
            capitalize: function (value) {
                if (!value) return '';
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            }
        },
        data() {
            return {};
        }
    }
</script>
