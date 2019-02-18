import Vue from 'vue';
import './../../../common';
import axios from 'axios';
import VueFormWizard from 'vue-form-wizard';
import {generateRoute} from "../../../common/helpers";
Vue.use(VueFormWizard);
import DatePicker from 'vue2-datepicker';
import moment from 'moment';


window['app'] = new Vue({
    el: '#deployments-edit',
    components: {
        'spinner-icon': require('./../../../components/spinner-icon/spinner-icon'),
        DatePicker,
        'editor': require('vue2-ace-editor'),
    },
    data: {
        loading: false,
        drop: {}
    },
    mounted() {
        console.log('window.drop', window.drop);
        if (window.drop.send_at) {
            window.drop.send_at_date = moment(window.drop.send_at, 'YYYY-MM-DD HH:mm:ss').toDate();
            window.drop.send_at_time = moment(window.drop.send_at, 'YYYY-MM-DD HH:mm:ss').toDate();
        }
        this.drop = window.drop;
    },
    methods: {
        initEditor: function (editor) {
            require('brace/mode/html');
            require('brace/theme/chrome');
        },
        save() {
            this.loading = true;
            const data = {...this.drop};
            if (data.send_at_time) {
                data.send_at_time = moment(data.send_at_time).format('HH:mm:ss');
            }
            if (data.send_at_date) {
                data.send_at_date = moment(data.send_at_date).format('YYYY-MM-DD');
            }
            axios
                .post(window.updateDropUrl, data)
                .then(response => {
                    this.loading = false;
                    this.$swal({
                        title: 'Drop Update!',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.replace(window.dropsIndexUrl);
                    });
                }, error => {
                    this.loading = false;
                });
        }
    }
});
