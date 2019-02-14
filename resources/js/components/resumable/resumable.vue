<template>
    <div class="resumable-component">
        <div class="resumable-drop" v-if="!fileSelected"></div>
        <button class="btn pm-btn pm-btn-purple pm-btn-md resumable-browse" v-if="!fileSelected">
            Browse for the file
        </button>
        <div class="resumable-file" v-if="fileSelected">
            <span>{{ fileSelected.fileName }} (<span class="mr-2" v-if="uploadingFile">Cargando</span>{{ uploadingProgress * 100 }}%)</span>
        </div>
        <div class="resumable-loader" :style="{'width': uploadingProgress * 100 }" v-if="uploadingFile"></div>
    </div>
</template>
<script>
    import * as Resumable from 'resumablejs/resumable.js';

    export default {
        data() {
            return {
                resumable: {},
                fileSelected: null,
                uploadingProgress: 0.1,
                uploadingFile: false
            };
        },
        props: {
            targetUrl: {
                required: true,
                type: String
            }
        },
        mounted() {
            this.bootstrapResumable();
        },
        methods: {
            bootstrapResumable() {
                this.resumable = new Resumable({
                    chunkSize: 1 * 1024 * 1024, // 1MB
                    simultaneousUploads: 3,
                    testChunks: false,
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
