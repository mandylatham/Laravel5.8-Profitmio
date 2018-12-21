document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.sidebar-toggle').addEventListener('click', () => {
        document.querySelector('.top-navigation-bar').classList.toggle('open');
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