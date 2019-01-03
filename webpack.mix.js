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

mix.js('resources/js/new/pages/campaign/index.js', 'public/js/campaign-index.js')
    .sass('resources/sass/new/campaign/index.scss', 'public/css/campaign-index.css')
    .js('resources/js/new/pages/company/index.js', 'public/js/company-index.js')
    .sass('resources/sass/new/company/index.scss', 'public/css/company-index.css')
    .js('resources/js/new/pages/user/index.js', 'public/js/user-index.js')
    .sass('resources/sass/new/user/index.scss', 'public/css/user-index.css')
    .sass('resources/sass/new/media-template/index.scss', 'public/css/media-template-index.css')
    .js('resources/js/new/pages/media-template/index.js', 'public/js/media-template-index.js')
    .copyDirectory('resources/fonts', 'public/fonts/')
    // .sass('resources/sass/app.scss', 'public/css/new-app.css')
    .copyDirectory('resources/img', 'public/img');
