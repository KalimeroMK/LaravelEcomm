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
/* backend js files*/
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
/* end backend js files*/
/* backend css files*/
mix.babel([
        'public/backend/css/sb-admin-2.min.css',
        'public/backend/css/custom.css',
        'public/backend/vendor/datatables/dataTables.bootstrap4.min.css'

    ],
    'public/css/all.css');
mix.minify('public/css/all.css');
/* end backend css files*/
/* frontend js files*/
mix.combine([

        'public/frontend/js/jquery.min.js',
        'public/frontend/js/jquery-migrate-3.0.0.js',
        'public/frontend/js/popper.min.js',
        'public/frontend/js/bootstrap.min.js',
        'public/frontend/js/slicknav.min.js',
        'public/frontend/js/owl-carousel.js',
        'public/frontend/js/magnific-popup.js',
        'public/frontend/js/waypoints.min.js',
        'public/frontend/js/finalcountdown.min.js',
        'public/frontend/js/nicesellect.js',
        'public/frontend/js/flex-slider.js',
        'public/frontend/js/scrollup.js',
        'public/frontend/js/onepage-nav.min.js',
        'public/frontend/js/isotope/isotope.pkgd.min.js',
        'public/frontend/js/easing.js',
        'public/frontend/js/active.js'

    ],
    'public/js/all_front.js');
mix.minify('public/js/all_front.js');
/* end backend js files*/

/* frontend css files*/
mix.babel([
        'public/frontend/css/bootstrap.css',
        'public/frontend/css/magnific-popup.min.css',
        'public/frontend/css/font-awesome.css',
        'public/frontend/css/jquery.fancybox.min.css',
        'public/frontend/css/niceselect.css',
        'public/frontend/css/animate.css',
        'public/frontend/css/flex-slider.min.css',
        'public/frontend/css/owl-carousel.css',
        'public/frontend/css/slicknav.min.css',
        'public/frontend/css/jquery-ui.css',
        'public/frontend/css/reset.css',
        'public/frontend/css/reset.css',
        'public/frontend/css/style.css',
        'public/frontend/css/responsive.css'

    ],
    'public/css/all_front.css');
mix.minify('public/css/all_front.css');
/* end backend css files*/

