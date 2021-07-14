// load environment config
const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({
    path: '../../../.env'/*, debug: true*/
}));

// load mix
const mix = require('laravel-mix');

// Allow multiple Laravel Mix applications
require('laravel-mix-merge-manifest');

// set public path
mix.setPublicPath(public = '../../../public').mergeManifest();

// set assets root
mix.setResourceRoot( assetsRoot
    // get ASSET_URL ?? APP_URL from parameters
     = process.env.npm_config_asset_url ?? process.env.npm_config_app_url
    // get ASSET_URL ?? APP_URL from .env variables
    ?? process.env.ASSET_URL ?? process.env.APP_URL
    // fallback to localhost
    ?? 'https://localhost' );

// configure mix
mix.options({
    fileLoaderDirs: {
        images: 'inventory-module/assets/images',
        fonts: 'inventory-module/assets/fonts',
    }
});
// Autoload libraries aliases
mix.autoload({
    // autoload jQuery
    'jquery': [ '$', 'jQuery', 'window.$', 'window.jQuery' ],
});

// disable notifications in prod
if (mix.inProduction() || [ 'warn', 'silent' ].includes(process.env.npm_config_loglevel)) mix.disableNotifications();

// execute mix
mix
    /** *********************** **
     ** Sales module components **
     ** *********************** **/
    .js('resources/assets/js/app.js',       'inventory-module/assets/js')
    .sass('resources/assets/sass/app.scss', 'inventory-module/assets/css')
    .copy('resources/assets/images',        public+'/inventory-module/assets/images')

// create new version only for production
;if (mix.inProduction()) mix.version();
// enable browsersync
// else mix.browserSync( process.env.APP_URL );
