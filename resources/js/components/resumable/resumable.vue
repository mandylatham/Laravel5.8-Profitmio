<template>
    <div class="resumable-component" v-if="!resetDomElements">
        <div class="resumable-drop" v-if="!fileSelected"></div>
        <button type="button" class="btn pm-btn pm-btn-purple pm-btn-md resumable-browse" v-if="!fileSelected">
            <slot name="message">Browse for the file</slot>
        </button>
        <div class="resumable-file" v-if="fileSelected">
            <span v-if="uploadingProgress < 1">{{ fileSelected.fileName }} <span v-if="!hideProgress">(<span class="mr-2" v-if="uploadingFile">Loading</span>{{ uploadingProgress * 100 }}%)</span></span>
            <span v-if="uploadingProgress >= 1">{{ fileSelected.fileName }} <span v-if="!hideProgress">(<span class="mr-2" v-if="uploadingFile">Loaded</span> 100%)</span></span>
        </div>
        <div class="resumable-loader" :style="{'width': uploadingProgress * 100 }" v-if="uploadingFile"></div>
    </div>
</template>
<script>
    import * as Resumable from 'resumablejs/resumable.js';

    export default {
        data() {
            return {
                bootstraping: false,
                resumable: null,
                fileSelected: null,
                uploadingProgress: 0.1,
                uploadingFile: false,
                resetDomElements: false,
                data: {}
            };
        },
        props: {
            targetUrl: {
                required: true,
                type: String
            },
            fileType: {
                required: false,
                type: Array,
                default() {
                    return [];
                }
            },
            hideProgress: {
                type: Boolean,
                required: false,
                default: false
            }
        },
        mounted() {
            this.bootstrapResumable();
        },
        methods: {
            addData(property, value) {
                this.data[property] = value;
            },
            bootstrapResumable() {
                if (this.bootstraping) return;
                this.bootstraping = true;
                this.resetDom()
                    .then(() => {
                        this.resumable = new Resumable({
                            chunkSize: 1 * 1024 * 1024, // 1MB
                            fileType: this.fileType,
                            simultaneousUploads: 3,
                            testChunks: false,
                            query: this.data,
                            throttleProgressCallbacks: 1,
                            target: this.targetUrl,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        this.resumable.assignDrop(this.$el.querySelector('.resumable-drop'));
                        this.resumable.assignBrowse(this.$el.querySelector('.resumable-drop'));
                        this.resumable.assignBrowse(this.$el.querySelector('.resumable-browse'));

                        this.resumable.on('fileAdded', file => {
                            this.fileSelected = file;
                            this.$emit('file-added', {file});
                        });
                        this.resumable.on('fileSuccess', (file, message) => {
                            this.$emit('file-success', {file, message});
                        });
                        this.resumable.on('fileError', (file, message) => {
                            this.uploadingFile = false;
                            this.$emit('file-error', {file, message});
                        });
                        this.resumable.on('fileProgress', (file,) => {
                            this.uploadingProgress = this.resumable.progress();
                            this.$emit('file-progress', {file});
                        });
                        this.bootstraping = false;
                    });
            },
            resetDom: function () {
                return new Promise((resolve, reject) => {
                    // Check if resumable instance already exists
                    if (this.resumable) {
                        this.resumable.cancel();
                        delete this.resumable;
                        this.resetDomElements = true;
                        setTimeout(() => {
                            this.resetDomElements = false;
                            resolve();
                        });
                    } else {
                        resolve();
                    }
                });
            },
            startUpload() {
                this.uploadingFile = true;
                this.resumable.upload();
            }
        },
        watch: {
            targetUrl: function () {
                this.bootstrapResumable();
            }
        }
    };
</script>
