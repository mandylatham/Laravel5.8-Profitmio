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

Vue.directive('template-variable-swap', function (el, binding) {
    let values = {
        first_name: 'John',
        last_name: 'Doe',
        email: 'test@example.com',
        phone: '555-555-5555',
        year: '2016',
        make: 'Mazda',
        model: 'Miata'
    };
    for (var key in values) {
        var reggie = new RegExp('\{\{\s*(' + key + '\s*\}\}');
        el.innerHTML = el.innerHTML.replace(reggie, values.key);
    }
    return;
});

window['app'] = new Vue({
    el: '#template-details',
    components: {
        'media-template-detail': require('./../../components/media-template-detail/media-template-detail'),
    },
    computed: {
    },
    data: {
        template: {},
        toggleInputs: false,
        toggleNameInput: false,
        updateUrl: null,
        deleteUrl: null,
        updateForm: new Form({
            name: null,
            text_message: null,
            email_subject: null,
            email_text: null,
            email_html: null
        })
    },
    mounted() {
        this.updateUrl = window.updateUrl;
        this.deleteUrl = window.deleteUrl;
        this.template = window.template;
    },
    methods: {
        onSubmit() {
            this.form.submit();
        }
    }
});
