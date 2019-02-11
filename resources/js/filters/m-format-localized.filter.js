import Vue from 'vue';
import moment from 'moment-timezone';

Vue.filter('mFormatLocalized', function (value, format) {
    return value.tz(window.timezone).format(format);
});
