document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.sidebar-toggle').addEventListener('click', () => {
        document.body.classList.toggle('open');
    });
});

window.breakpoints = {
    isXs() {
        return document.documentElement.clientWidth < 576;
    },
    isSm() {
        const w = document.documentElement.clientWidth;
        return w >= 576 && w < 768;
    },
    isMd() {
        const w = document.documentElement.clientWidth;
        return w >= 768 && w < 992;
    },
    isLg() {
        const w = document.documentElement.clientWidth;
        return w >= 992 && w < 1200;
    },
    isXlg() {
        const w = document.documentElement.clientWidth;
        return w >= 1200;
    }
};

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}