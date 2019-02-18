import Vue from 'vue';
import moment from 'moment-timezone';

Vue.filter('mUtcParse', function (value, format) {
    return moment.utc(value, format);
});
