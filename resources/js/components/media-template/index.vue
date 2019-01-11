<template>
    <div class="row no-gutters media-template-component inactive">
        <div class="col-12 col-md-5 media-template-header">
            <div class="media-template-header--title">
                <p>Template {{ media_template.id }}</p>
                <strong>{{ media_template.name }}</strong>
            </div>
        </div>
        <div class="col-4 col-md-2 media-template-postcard">
            <media-type no-label :media_type="media_template.type"></media-type>
        </div>
        <div class="col-4 col-md-2 media-template-date">
            <span>Created On</span>
            <span>{{ media_template.created_at | amDateFormat('MM.DD.YY') }}</span>
        </div>
        <div class="col-4 col-md-3 media-template-links">
            <a :href="generateRoute(templateEdit, {'templateId': media_template.id})"><span class="fa fa-edit"></span> Edit</a>
            <a :href="generateRoute(templateDelete, {'templateId': media_template.id})"><span class="fa fa-trash"></span> Delete</a>
        </div>
    </div>
</template>
<script>
    import moment from 'moment';
    import {generateRoute} from './../../common/helpers'

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
                mediaTemplateClosed: true,
                templateEdit: '',
                templateDelete: ''
            };
        },
        mounted: function () {
            this.templateEdit = window.templateEdit;
            this.templateDelete = window.templateDelete;
        },
        computed: {
            template_text: function () {
                if (this.media_template.type == 'sms') {
                    return this.media_template.text_message;
                }
                if (this.media_template.type == 'email') {
                    return this.media_template.email_text;
                }

                return;
            }
        },
        methods: {
            generateRoute
        }
    }
</script>
