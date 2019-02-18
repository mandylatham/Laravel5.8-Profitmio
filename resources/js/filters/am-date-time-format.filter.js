import Vue from 'vue';
import moment from 'moment';

Vue.filter('amDateTimeFormat', function (value, format) {
    return moment(value, 'YYYY-MM-DD HH:mm:ss').format(format);
});