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

mix.combine([
        'public/backend/vendor/jquery/jquery.min.js',
        'public/backend/vendor/bootstrap/js/bootstrap.bundle.min.js',
        'public/backend/vendor/jquery-easing/jquery.easing.min.js',
        'public/backend/js/sb-admin-2.min.js',
        'public/backend/vendor/datatables/jquery.dataTables.min.js',
        'public/backend/vendor/datatables/dataTables.bootstrap4.min.js',
        'public/backend/js/sweetalert.min.js',
        'public/backend/js/dataTables.searchBuilder.min.js'

    ],
    'public/js/all.js');
mix.minify('public/js/all.js');

mix.babel([
        'public/backend/css/sb-admin-2.min.css',
        'public/backend/css/custom.css',
        'public/backend/vendor/datatables/dataTables.bootstrap4.min.css'

    ],
    'public/css/all.css');
mix.minify('public/css/all.css');
