document.addEventListener('DOMContentLoaded', function () {
    console.log('ready');
    console.log("document.querySelector('.js-toggle-sidebar-menu')", document.querySelector('.js-toggle-sidebar-menu'));
    document.querySelector('.js-toggle-sidebar-menu').addEventListener('click', function () {
        document.getElementById('app').classList.toggle('navbar-menu-open');
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