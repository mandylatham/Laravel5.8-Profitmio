<template>
    <div class="media-type-component">
        <span class="media-type-icon" :class="{ mediaClass }"></span>
        <span class="media-type-value" :class="{'d-none': hiddenValue}">{{ media_type | capitalize }}</span>
    </div>
</template>
<script>
    export default {
        props: {
            media_type: {
                type: String,
                required: true,
            }
        },
        computed: {
            mediaClass: function () {
                if (this.media_type == 'email') {
                    return 'fa fa-envelope';
                } else if (this.media_type == 'sms') {
                    return 'fa fa-message';
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
