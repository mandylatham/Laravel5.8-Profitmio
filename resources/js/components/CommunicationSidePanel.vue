<template>
    <div>
        <p>Communication Side Panel Component</p>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log(this.someData);
        },
        data() {
            return {
                //
            }
        },
        props: ['someData'],
        computed: {
            //
        },
        watch: {
            //
        },
        methods: {
            //
        }
    }
</script>
