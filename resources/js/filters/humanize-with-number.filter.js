import Vue from 'vue';

const SECONDS_PER_DAY = 86400;
const SECONDS_PER_HOUR = 3600;
const SECONDS_PER_MINUTE = 60;

Vue.filter('humanizeWithNumber', function (value) {
    const result = [];
    value = parseInt(value, 10);
    if (value >= SECONDS_PER_DAY) {
        const days = parseInt(value / SECONDS_PER_DAY);
        value = value % SECONDS_PER_DAY;
        result.push(days + 'd');
    }
    if (value >= SECONDS_PER_HOUR) {
        const hours = parseInt(value / SECONDS_PER_HOUR);
        value = value % SECONDS_PER_HOUR;
        result.push(hours + 'h');
    }
    if (value >= SECONDS_PER_MINUTE) {
        const minutes = parseInt(value / SECONDS_PER_MINUTE);
        value = value % SECONDS_PER_MINUTE;
        result.push(minutes + 'm');
    }
    if (value > 0) {
        result.push(value + 's');
    }
    return result.length > 0 ? result.join(' ') : '0s';
});
