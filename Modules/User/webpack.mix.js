const dotenvExpand = require('dotenv-expand');
const fs = require('fs');
const path = require('path');
const envPath = path.resolve(__dirname, '../../.env');
if (fs.existsSync(envPath)) {
    dotenvExpand(require('dotenv').config({path: envPath, silent: true}));
}

const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

mix.js(__dirname + '/Resources/assets/js/app.js', 'js/user.js')
    .sass(__dirname + '/Resources/assets/sass/app.scss', 'css/user.css');

if (mix.inProduction()) {
    mix.version();
}
