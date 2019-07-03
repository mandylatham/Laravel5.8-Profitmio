import Vue from 'vue';
import moment from 'moment';

Vue.filter('formatTime', function (value, format) {
    return moment.unix(value).format(format || 'YYYY-MM-DD h:mm A');
});
