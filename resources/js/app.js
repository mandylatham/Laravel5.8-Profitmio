import './bootstrap';
import Vue from 'vue'
// import moment from 'moment';
import { Dropdown } from 'bootstrap-vue/es/components';
import VueGoodTablePlugin from 'vue-good-table';
import vSelect from 'vue-select/dist/vue-select';
// import DatePick from './date-pick.vue';

// Plugins
Vue.use(Dropdown);
Vue.use(VueGoodTablePlugin);
Vue.component('v-select', vSelect);

// Page Component
Vue.component('campaign-index', require('./components/CampaignIndexComponent'));

// Vue.filter('format', function (value, format) {
//     return moment(value, 'YYYY-MM-DD').format(format);
// });

// Main App
new Vue({
    el: '#wrapper'
});

// Main header
new Vue({
    el: '#main-header'
});

// new Vue({
//     el: '#campaign-list',
//     data: {
//         open: {}
//     },
//     methods: {
//         toggle: function (idx) {
//             Vue.set(this.open, idx, !this.open[idx]);
//         }
//     }
// });

// new Vue({
//     el: '#wrapper-aside',
//     components: {DatePick},
//     methods: {
//         parseDate: function (date, format) {
//             return moment(date, format).toDate();
//         }
//     },
//     data: {
//         selectedDate: moment().format('YYYY-MM-DD')
//     }
// });
