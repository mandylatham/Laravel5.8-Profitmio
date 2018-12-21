// Add all global icons here
import Vue from 'vue';
import {MenuIcon, XIcon} from 'vue-feather-icons';

require('./pm-icons.scss');

Vue.component('all-icon', require('./icons/all-icon.vue'));
Vue.component('campaigns-icon', require('./icons/campaigns-icon.vue'));
Vue.component('companies-icon', require('./icons/companies-icon.vue'));
Vue.component('date-icon', require('./icons/date-icon.vue'));
Vue.component('dealer-db-icon', require('./icons/dealer-db-icon.vue'));
Vue.component('drops-icon', require('./icons/drops-icon.vue'));
Vue.component('edit-icon', require('./icons/edit-icon.vue'));
Vue.component('folder-icon', require('./icons/folder-icon.vue'));
Vue.component('help-icon', require('./icons/help-icon.vue'));
Vue.component('idle-icon', require('./icons/idle-icon.vue'));
Vue.component('menu-icon', MenuIcon);
Vue.component('mail-icon', require('./icons/mail-icon.vue'));
Vue.component('notification-icon', require('./icons/notification-icon.vue'));
Vue.component('phone-icon', require('./icons/phone-icon.vue'));
Vue.component('recipients-icon', require('./icons/recipients-icon.vue'));
Vue.component('responses-icon', require('./icons/responses-icon.vue'));
Vue.component('sms-icon', require('./icons/sms-icon.vue'));
Vue.component('system-icon', require('./icons/system-icon.vue'));
Vue.component('stats-icon', require('./icons/stats-icon.vue'));
Vue.component('tag-icon', require('./icons/tag-icon.vue'));
Vue.component('templates-icon', require('./icons/templates-icon.vue'));
Vue.component('unread-icon', require('./icons/unread-icon.vue'));
Vue.component('user-icon', require('./icons/user-icon.vue'));
Vue.component('x-icon', XIcon);
