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
