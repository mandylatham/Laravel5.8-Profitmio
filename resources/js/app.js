import './bootstrap';
import Vue from 'vue'
import { Tabs, Dropdown } from 'bootstrap-vue/es/components';
import VueGoodTablePlugin from 'vue-good-table';
import vSelect from 'vue-select/dist/vue-select';
import { HomeIcon, EditIcon, PhoneIcon, MailIcon, MenuIcon, MessageCircleIcon, Trash2Icon, ToggleLeftIcon, VolumeIcon, XIcon, UserIcon } from 'vue-feather-icons'

// Global filters
import './filters/global-filters';


// Plugins
Vue.use(Dropdown);
Vue.use(Tabs);
Vue.use(VueGoodTablePlugin);
Vue.component('v-select', vSelect);

// Pages
Vue.component('campaign-index', require('./page-components/campaign/index/CampaignIndexComponent'));
Vue.component('user-index', require('./page-components/user/index/user-index.component'));
Vue.component('user-view', require('./page-components/user/view/user-view.component'));

// Components
Vue.component('date-pick', require('./components/date-pick'));
Vue.component('status', require('./components/status/status'));
Vue.component('pm-responsive-table', require('./components/pm-responsive-table/pm-responsive-table'));
Vue.component('list-campaign', require('./components/list-campaign/list-campaign'));
Vue.component('list-company', require('./components/list-company/list-company.component'));

// Icons
Vue.component('home-icon', HomeIcon);
Vue.component('edit-icon', EditIcon);
Vue.component('phone-icon', PhoneIcon);
Vue.component('mail-icon', MailIcon);
Vue.component('menu-icon', MenuIcon);
Vue.component('message-circle-icon', MessageCircleIcon);
Vue.component('trash-icon', Trash2Icon);
Vue.component('toggle-left-icon', ToggleLeftIcon);
Vue.component('user-icon', UserIcon);
Vue.component('x-icon', XIcon);
Vue.component('volume-icon', VolumeIcon);

// Vue.filter('format', function (value, format) {
//     return moment(value, 'YYYY-MM-DD').format(format);
//
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
