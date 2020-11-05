import Vue from 'vue';
import { Navbar, Dropdown } from 'bootstrap-vue/es/components'
import './pm-icons';

Vue.use(Dropdown);
Vue.use(Navbar);

Vue.mixin({
    data: function() {
        return {
            notifications: window.notifications
        }
    }
});

new Vue({
    el: '#top-navbar-menu',
    mounted: function () {
        this.loggedUser = window.loggedUser;
    },
    data: {
        loggedUser: {}
    },
    methods: {
        signout(url) {
            localStorage.clear();
            window.location.replace(url);
        }
    }
});
