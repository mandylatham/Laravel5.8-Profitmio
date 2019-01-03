<template>
    <div class="row no-gutters media-template-component inactive">
        <div class="col-12 col-md-5 media-template-header">
            <div class="media-template-header--status">
                <media-type no-label :media_type="media_template.type"></media-type>
            </div>
            <div class="media-template-header--title">
                <strong>Template {{ media_template.id }}</strong>
                <p>{{ media_template.name }}</p>
            </div>
        </div>
        <div class="col-4 col-md-2 media-template-postcard--image">
            <img src="" alt="">
        </div>
        <div class="col-4 col-md-2 media-template-date">
        </div>
        <div class="col-4 col-md-3 media-template-chart">
        </div>
    </div>
</template>
<script>
    import moment from 'moment';

    export default {
        components: {
            'media-type': require('./../media-type/media-type'),
        },
        props: {
            media_template: {
                type: Object,
                required: true,
                default: function () {
                    return {};
                }
            }
        },
        data() {
            return {
                mediaTemplateClosed: true
            };
        },
        computed: {
        },
        methods: {
        }
    }
</script>
