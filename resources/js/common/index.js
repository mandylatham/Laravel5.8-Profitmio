// Import vanilla javascript code
import './vanilla';
// Import Vue and dependencies
import Vue from 'vue';
// Global event
import './global-event-bus';
// Top NavBar Menu
import './top-navbar-menu';
// Vue Component
import vSelect from 'vue-select/dist/vue-select';
Vue.component('v-select', vSelect);
// Pretty Checkbox
import PrettyCheckbox from 'pretty-checkbox-vue';
Vue.use(PrettyCheckbox);
// Vue Bootstrap
import { Navbar, Card, Tabs, Dropdown } from 'bootstrap-vue/es/components'
Vue.use(Dropdown);
Vue.use(Card);
Vue.use(Tabs);
Vue.use(Navbar);
// Icons
import './pm-icons';
// Filters
import './../filters/global-filters';
// Sweet Alert
import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);
// Toastr Library
import VueToastr2 from 'vue-toastr-2'
window.toastr = require('toastr');
Vue.use(VueToastr2);
