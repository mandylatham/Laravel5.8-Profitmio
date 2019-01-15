import Vue from 'vue';
import { Navbar, Dropdown } from 'bootstrap-vue/es/components'
import './pm-icons';

Vue.use(Dropdown);
Vue.use(Navbar);

new Vue({
    el: '#top-navbar-menu',
    mounted: function () {
        this.loggedUser = window.loggedUser;
    },
    data: {
        loggedUser: {}
    }
});
