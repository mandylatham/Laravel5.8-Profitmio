import './bootstrap';
import Vue from 'vue'
import { Tabs, Dropdown } from 'bootstrap-vue/es/components';
import VueGoodTablePlugin from 'vue-good-table';
import vSelect from 'vue-select/dist/vue-select';
import Datepicker from 'vue2-datepicker';
import vClickOutside from 'v-click-outside'


// Global filters
import './filters/global-filters';

// Icons
import './components/pm-icons/pm-icons';

// Plugins
Vue.use(Dropdown);
Vue.use(vClickOutside);
Vue.use(Tabs);
Vue.use(VueGoodTablePlugin);
Vue.component('v-select', vSelect);
Vue.component('datepicker', Datepicker);

// Pages
Vue.component('campaign-index', require('./page-components/campaign/index/CampaignIndexComponent'));
Vue.component('user-index', require('./page-components/user/index/user-index.component'));
Vue.component('user-view', require('./page-components/user/view/user-view.component'));
Vue.component('campaign-view', require('./page-components/campaign/view/campaign-view.component'));
Vue.component('campaign-stats', require('./page-components/campaign/stats/campaign-stats.component'));
Vue.component('campaign-drops', require('./page-components/campaign/drops/campaign-drops.component'));
Vue.component('campaign-recipients', require('./page-components/campaign/recipients/campaign-recipients.component'));
Vue.component('campaign-responses', require('./page-components/campaign/responses/campaign-responses.component'));
Vue.component('campaign-edit', require('./page-components/campaign/edit/campaign-edit.component'));
Vue.component('campaign-edit-detail', require('./page-components/campaign/edit-detail/campaign-edit-detail.component'));
Vue.component('campaign-edit-account', require('./page-components/campaign/edit-account/campaign-edit-account.component'));
Vue.component('campaign-edit-phone', require('./page-components/campaign/edit-phone/campaign-edit-phone.component'));
Vue.component('campaign-edit-setting', require('./page-components/campaign/edit-setting/campaign-edit-setting.component'));

// Components
Vue.component('top-navigation-bar', require('./components/top-navigation-bar/top-navigation-bar.component'));
Vue.component('drop-type-icon', require('./components/drop-type-icon/drop-type-icon'));
Vue.component('drop-status', require('./components/drop-status/drop-status'));
Vue.component('date-pick', require('./components/date-pick'));
Vue.component('status', require('./components/status/status'));
Vue.component('pm-responsive-table', require('./components/pm-responsive-table/pm-responsive-table'));
Vue.component('list-campaign', require('./components/list-campaign/list-campaign'));
Vue.component('list-company', require('./components/list-company/list-company.component'));
Vue.component('wrapper', require('./components/wrapper/wrapper.component'));

// Icons

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
