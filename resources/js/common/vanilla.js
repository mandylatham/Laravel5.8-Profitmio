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

    let toggleSideMenu = document.querySelector('.js-toggle-side-menu');
    if (toggleSideMenu) {
        toggleSideMenu.addEventListener('click', function () {
            const app = document.getElementById('app');
            let timeout = 0;
            if (app.classList.contains('navbar-menu-open')) {
                collapseNavbarMenu();
                timeout = 300;
            }
            setTimeout(() => {
                if (app.classList.contains('side-menu-open') && document.documentElement.clientWidth < 768) {
                    app.classList.add('navbar-side-menu-fix');
                    setTimeout(() => {
                        app.classList.remove('navbar-side-menu-fix');
                    }, 300);
                }
                document.getElementById('app').classList.toggle('side-menu-open');
            }, timeout);
        });
    }

    let closeSideMenuButton = document.querySelector('.js-close-side-menu');
    if (closeSideMenuButton) {
        closeSideMenuButton.addEventListener('click', function () {
            collapseSideMenu();
        });
    }

    function collapseNavbarMenu() {
        const app = document.getElementById('app');
        // app.classList.add('navbar-side-menu-fix');
        app.classList.remove('navbar-menu-open');
        // setTimeout(() => {
        //     app.classList.remove('navbar-side-menu-fix');
        // }, 300);
    }

    function collapseSideMenu() {
        const app = document.getElementById('app');
        app.classList.add('navbar-side-menu-fix');
        app.classList.remove('side-menu-open');
        setTimeout(() => {
            app.classList.remove('navbar-side-menu-fix');
        }, 300);
    }
});
