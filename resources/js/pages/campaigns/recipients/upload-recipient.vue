<template>
    <div class="upload-recipient-component">
        <form-wizard ref="wizard" :title="''" :subtitle="''" :step-size="'sm'" :color="'#572E8D'">
            <tab-content title="Upload File" icon="fas fa-list-ul">
                <resumable :target-url="this.targetUrl" ref="resumable" @file-added="onFileAdded"
                           @file-success="onFileSuccess"></resumable>
            </tab-content>
            <tab-content title="Recipient List" icon="fas fa-list-ul">
                <div class="form-row">
                    <div class="col-8">
                        <div class="form-group">
                            <label for="name">List Name</label>
                            <input type="text" name="name" id="name" class="form-control">
                            <!--<input-errors :error-bag="campaignForm.errors" :field="'agency'"></input-errors>-->
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-6">
                        <label>Type</label>
                        <div>
                            <p-radio color="primary" class="p-default display-block" name="type" v-model="a">All
                                Coquest
                            </p-radio>
                        </div>
                        <div>
                            <p-radio color="primary" class="p-default display-block" name="type" v-model="a">All
                                Database
                            </p-radio>
                        </div>
                        <div>
                            <p-radio color="primary" class="p-default display-block" name="type" v-model="a">Mix - Use
                                CSV Field
                            </p-radio>
                        </div>
                    </div>
                </div>
            </tab-content>
            <tab-content title="Map Your Fields" icon="fas fa-list-ul">
            </tab-content>
            <!--<template slot="finish">-->
            <!--<button type="button" class="wizard-btn" :disabled="loading">-->
            <!--<span v-if="!loading">Finish</span>-->
            <!--<spinner-icon :size="'sm'" class="white" v-if="loading"></spinner-icon>-->
            <!--</button>-->
            <!--</template>-->
        </form-wizard>
    </div>
</template>
<script>
    import Vue from 'vue';
    import Form from './../../../common/form';
    import VueFormWizard from 'vue-form-wizard';

    Vue.use(VueFormWizard);

    export default {
        components: {
            'resumable': require('./../../../components/resumable/resumable'),
        },
        data() {
            return {
                a: null,
                fileForm: new Form({
                    name: ''
                }),
                fileHeaders: []
            };
        },
        props: ['targetUrl'],
        methods: {
            onFileAdded() {
                this.$refs.resumable.startUpload();
            },
            onFileSuccess(event) {
                const response = JSON.parse(event.message);
                this.fileHeaders = response.headers;
                this.$refs.wizard.nextTab();
            },
        }
    };
</script>
