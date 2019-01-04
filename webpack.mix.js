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
    .copyDirectory('resources/remark_assets', 'public/')
    .copyDirectory('resources/fonts', 'public/fonts/')
    .copyDirectory('resources/img', 'public/img');
