import Vue from 'vue';
import './../../common';
import Form from './../../common/form';
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
import axios from 'axios';
// Toastr Library
import VueToastr2 from 'vue-toastr-2'
window.toastr = require('toastr');
Vue.use(VueToastr2);
// Chart Library
import {filter} from 'lodash';
import Button from 'bootstrap-vue';
import InputGroup from 'bootstrap-vue';
import FormInput from 'bootstrap-vue';
Vue.use(Button);
Vue.use(InputGroup);
Vue.use(FormInput);

window['app'] = new Vue({
    el: '#template-create',
    components: {
        'media-template-create': require('./../../components/media-template/create'),
    },
    computed: {
    },
    data: {
        createUrl: '',
    },
    mounted() {
        this.createUrl = window.createUrl;
    },
    methods: {
        onSubmit() {
            this.form.submit();
        }
    }
});
