/* jslint esversion:6 */

let ContextReplacementPlugin = require('webpack/lib/ContextReplacementPlugin');
let Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    // Stop moment.js loading a tremendous amount of locales we don't need
    // https://github.com/moment/moment/issues/2416
    // https://github.com/symfony/webpack-encore/issues/197
    .addPlugin(new ContextReplacementPlugin(/moment[\/\\]locale$/, /(en)$/))

    .enableSassLoader(function (options) {
        options.includePaths = [
            'node_modules/bootstrap-sass/assets/stylesheets'
        ];
    })
    .enablePostCssLoader()

    // https://github.com/symfony/webpack-encore/commit/afe3797f4c42e83f2c764b4dd36d49cdc3302c57
    // https://github.com/babel/babel/issues/9751#issuecomment-480682705
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    // https://symfony.com/doc/current/frontend/encore/copy-files.html
    .copyFiles([
        {
            from: './assets/images',
            to: 'images/[path][name].[hash:8].[ext]'
        }
    ])

    .enableSingleRuntimeChunk()
    .splitEntryChunks()
    .autoProvidejQuery()

    .enableIntegrityHashes()

    .addEntry('app', './assets/js/app.js')
;

module.exports = Encore.getWebpackConfig();
