<template>
    <b-tabs pills card class="campaign-edit--tabs">
        <b-tab title="DETAILS" active>
            <campaign-edit-detail></campaign-edit-detail>
        </b-tab>
        <b-tab title="ACCOUNT">
            <campaign-edit-account></campaign-edit-account>
        </b-tab>
        <b-tab title="PHONE">
            <campaign-edit-phone></campaign-edit-phone>
        </b-tab>
        <b-tab title="SETTING">
            <campaign-edit-setting></campaign-edit-setting>
        </b-tab>
    </b-tabs>
</template>
<script>
    require('./campaign-edit.scss');
    export default {
        data() {
            return {
            }
        }
    }
</script>
