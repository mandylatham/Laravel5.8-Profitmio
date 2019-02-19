import Vue from 'vue';
import moment from 'moment-timezone';

Vue.filter('mDurationForHumans', function (value) {
    return moment.duration(value.diff(moment())).humanize(true);
});
