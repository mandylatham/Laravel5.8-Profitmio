import Vue from 'vue';
import Form from './../../common/form';
import axios from 'axios';
import { PackageIcon } from 'vue-feather-icons';
import VueFormWizard from 'vue-form-wizard'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'
Vue.use(VueFormWizard);


window['app'] = new Vue({
    el: '#registration',
    components: {
    },
    data: function () {
        return {

        };
    },
    methods: {
        onComplete: function () {
            this.$swal.success("All done");
        }
    }
});