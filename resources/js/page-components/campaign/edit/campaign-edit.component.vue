<template>
    <b-tabs pills card>
        <b-tab title="DETAILS" active>
            <campaign-edit-detail></campaign-edit-detail>
        </b-tab>
        <b-tab title="ACCOUNT">
            <!--<edit-campaign-account></edit-campaign-account>-->
        </b-tab>
        <b-tab title="PHONE">
            <!--<edit-campaign-phones></edit-campaign-phones>-->
        </b-tab>
        <b-tab title="SETTING">
            <!--<edit-campaign-setting></edit-campaign-setting>-->
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
