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

mix.js('resources/js/app.js', 'public/js/new-app.js')
    .js('resources/js/campaign-index.js', 'public/js/campaign-index.js')
    .copyDirectory('resources/remark_assets', 'public/')
    .copyDirectory('resources/fonts', 'public/fonts/')
    .sass('resources/sass/app.scss', 'public/css/new-app.css')
    .copyDirectory('resources/img', 'public/img');
