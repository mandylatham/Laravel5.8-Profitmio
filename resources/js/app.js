import './bootstrap';
import Vue from 'vue'
import { Dropdown } from 'bootstrap-vue/es/components';
import VueGoodTablePlugin from 'vue-good-table';
import vSelect from 'vue-select/dist/vue-select';
import { HomeIcon, EditIcon, PhoneIcon, MailIcon, MessageCircleIcon, ToggleLeftIcon, UserIcon } from 'vue-feather-icons'

// Plugins
Vue.use(Dropdown);
Vue.use(VueGoodTablePlugin);
Vue.component('v-select', vSelect);

// Pages
Vue.component('campaign-index', require('./page-components/campaign/index/CampaignIndexComponent'));
Vue.component('user-index', require('./page-components/user/index/UserIndexComponent'));

// Components
Vue.component('date-pick', require('./components/date-pick'));
Vue.component('status', require('./components/status/status'));
Vue.component('pm-responsive-table', require('./components/pm-responsive-table/pm-responsive-table'));

// Icons
Vue.component('home-icon', HomeIcon);
Vue.component('edit-icon', EditIcon);
Vue.component('phone-icon', PhoneIcon);
Vue.component('mail-icon', MailIcon);
Vue.component('message-circle-icon', MessageCircleIcon);
Vue.component('toggle-left-icon', ToggleLeftIcon);
Vue.component('user-icon', UserIcon);

// Vue.filter('format', function (value, format) {
//     return moment(value, 'YYYY-MM-DD').format(format);

// });

// Main App
new Vue({
    el: '#app'
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
