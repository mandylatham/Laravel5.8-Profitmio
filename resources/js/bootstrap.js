document.addEventListener('DOMContentLoaded', function () {

    document.querySelector('.js-toggle-navbar-menu').addEventListener('click', function () {
        const app = document.getElementById('app');
        let timeout = 0;
        if (app.classList.contains('side-menu-open')) {
            collapseSideMenu();
            timeout = 300;
        }
        setTimeout(() => {
            document.getElementById('app').classList.toggle('navbar-menu-open');
        }, timeout);
    });

    document.querySelector('.js-toggle-side-menu').addEventListener('click', function () {
        const app = document.getElementById('app');
        // Move navbar-menu
        if (app.classList.contains('side-menu-open')) {
            collapseSideMenu();
        } else {
            app.classList.add('side-menu-open');
        }
    });

    function collapseSideMenu() {
        app.classList.add('navbar-side-menu-fix');
        app.classList.remove('side-menu-open');
        setTimeout(() => {
            app.classList.remove('navbar-side-menu-fix');
        }, 300);
    }
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