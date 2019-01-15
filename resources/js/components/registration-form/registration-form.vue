<template>
</template>
<script>
    import moment from 'moment';
    import {generateRoute} from './../../common/helpers'

    export default {
        components: {
            'status': require('./../status/status'),
        },
        props: {
            form_data: {
                type: Object,
                required: true,
                default: function () {
                    return {};
                }
            }
        },
        data: function () {
            return {
                registrerForm = new Form({
                    company_id: 0,
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    timezone: '',
                    password: '',
                    password_confirmation: '',
                }),
            
            };
        },
        methods: function () {

        }
    };
</script>