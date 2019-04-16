import * as Resumable from 'resumablejs/resumable.js';

export default {
    bind: function(el, binding, vnode) {
        const value = binding.value;
        const resumable = new Resumable({
            chunkSize: 1 * 1024 * 1024, // 1MB
            fileType: value.fileType || [],
            simultaneousUploads: 3,
            testChunks: false,
            query: value.data || [],
            throttleProgressCallbacks: 1,
            target: value.targetUrl,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        setTimeout(() => {
            resumable.assignDrop(el);
            resumable.assignBrowse(el.querySelector(value.browseSelector));
            resumable.on('fileAdded', file => {
                el.dispatchEvent(new CustomEvent('file-added', {
                    detail: {
                        file,
                        resumable
                    }
                }));
            });
            resumable.on('fileSuccess', (file, message) => {
                el.dispatchEvent(new CustomEvent('file-success', {
                    detail: {
                        file,
                        message: JSON.parse(message),
                        resumable
                    }
                }));
            });
        }, 100);
    }
};
