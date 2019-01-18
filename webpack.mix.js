const mix = require('laravel-mix');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.js('resources/js/pages/campaign/index.js', 'public/js/campaign-index.js')
    .sass('resources/sass/campaign/index.scss', 'public/css/campaign-index.css')
    .js('resources/js/pages/dashboard/dashboard.js', 'public/js/dashboard.js')
    .sass('resources/sass/dashboard/dashboard.scss', 'public/css/dashboard.css')
    .js('resources/js/pages/company/index.js', 'public/js/company-index.js')
    .sass('resources/sass/company/index.scss', 'public/css/company-index.css')
    .js('resources/js/pages/user/site-admin-index.js', 'public/js/site-admin-user-index.js')
    .sass('resources/sass/user/site-admin-index.scss', 'public/css/site-admin-user-index.css')
    .js('resources/js/pages/user/company-admin-index.js', 'public/js/company-admin-user-index.js')
    .sass('resources/sass/user/company-admin-index.scss', 'public/css/company-admin-user-index.css')
    .js('resources/js/pages/user/detail.js', 'public/js/user-detail.js')
    .sass('resources/sass/user/detail.scss', 'public/css/user-detail.css')
    .js('resources/js/pages/user/create.js', 'public/js/user-create.js')
    .sass('resources/sass/user/create.scss', 'public/css/user-create.css')
    .js('resources/js/pages/auth/login.js', 'public/js/login.js')
    .sass('resources/sass/auth/login.scss', 'public/css/login.css')
    .js('resources/js/pages/auth/registration.js', 'public/js/registration.js')
    .sass('resources/sass/auth/registration.scss', 'public/css/registration.css')
    .js('resources/js/pages/auth/registration-full.js', 'public/js/registration-full.js')
    .sass('resources/sass/auth/registration-full.scss', 'public/css/registration-full.css')
    .sass('resources/sass/media-template/create.scss', 'public/css/media-template-create.css')
    .js('resources/js/pages/media-template/create.js', 'public/js/media-template-create.js')
    .sass('resources/sass/media-template/index.scss', 'public/css/media-template-index.css')
    .js('resources/js/pages/media-template/index.js', 'public/js/media-template-index.js')
    .sass('resources/sass/media-template/details.scss', 'public/css/media-template-details.css')
    .js('resources/js/pages/media-template/details.js', 'public/js/media-template-details.js')
    .js('resources/js/pages/user/view.js', 'public/js/user-view.js')
    .sass('resources/sass/user/view.scss', 'public/css/user-view.css')
    .js('resources/js/pages/campaign/console.js', 'public/js/console.js')
    .sass('resources/sass/campaign/console.scss', 'public/css/console.css')
    .copyDirectory('resources/remark_assets', 'public/')
    .copyDirectory('resources/fonts', 'public/fonts/')
    .copyDirectory('resources/img', 'public/img');
