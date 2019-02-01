<template>
    <div class="error-bag-component invalid-feedback" v-if="errorBag.has(field)">
        <div v-for="msg in errorBag.get(field)">{{ msg }}</div>
    </div>
</template>
<script>
    export default {
        props: {
            errorBag: {
                type: Object,
                required: true
            },
            field: {
                type: String,
                required: true
            }
        },
        data() {
            return {};
        }
    }
</script>
