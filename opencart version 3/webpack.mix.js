const mix = require('laravel-mix');
const fs = require('fs');
const { CleanWebpackPlugin } = require('clean-webpack-plugin'); // installed via npm

const ImageminPlugin = require( 'imagemin-webpack-plugin' ).default;

mix.webpackConfig({
  plugins: [
      new CleanWebpackPlugin()
  ]
});

mix.setPublicPath('./_dev/public')

mix.ts('./_dev/js/adminConfigPage.ts', '_dev/public/js/adminConfigPage.js')
mix.ts('./_dev/js/adminOrderPage.ts', '_dev/public/js/adminOrderPage.js')
mix.ts('./_dev/js/adminOrderCreatePage.ts', '_dev/public/js/adminOrderCreatePage.js')
mix.ts('./_dev/js/ifthenpaySuccessPage.ts', '_dev/public/js/ifthenpaySuccessPage.js')
mix.ts('./_dev/js/checkoutMultibancoPage.ts', '_dev/public/js/checkoutMultibancoPage.js')
mix.ts('./_dev/js/checkoutMbwayPage.ts', '_dev/public/js/checkoutMbwayPage.js')
mix.ts('./_dev/js/checkoutPayshopPage.ts', '_dev/public/js/checkoutPayshopPage.js')
mix.ts('./_dev/js/checkoutCcardPage.ts', '_dev/public/js/checkoutCcardPage.js')
mix.ts('./_dev/js/checkoutCofidisPage.ts', '_dev/public/js/checkoutCofidisPage.js')
mix.ts('./_dev/js/checkoutIfthenpaygatewayPage.ts', '_dev/public/js/checkoutIfthenpaygatewayPage.js')
mix.ts('./_dev/js/adminOrderInfoPage.ts', '_dev/public/js/adminOrderInfoPage.js')
    .webpackConfig({
        resolve: {
          extensions: ["*", ".js", ".jsx", ".ts", ".tsx"],
        },
        plugins: [
            new ImageminPlugin( {
    //            disable: process.env.NODE_ENV !== 'production', // Disable during development
                pngquant: {
                    quality: '95-100',
                },
                test: /\.(jpe?g|png|gif|svg)$/i,
            }),
        ],
      })
    .sass('_dev/scss/ifthenpayConfig.scss', '_dev/public/css/ifthenpayConfig.css')
    .sass('./_dev/scss/ifthenpayPaymentMethodSetup.scss', '_dev/public/css/ifthenpayPaymentMethodSetup.css')
    .sass('./_dev/scss/ifthenpayConfirmPage.scss', '_dev/public/css/ifthenpayConfirmPage.css')
    .sass('./_dev/scss/paymentOptions.scss', '_dev/public/css/paymentOptions.css')
    .options({
        processCssUrls: false
    });
//mix.version();
mix.babel(['_dev/public/js/ifthenpaySuccessPage.js'], '_dev/public/js/ifthenpaySuccessPage.js')
mix.babel(['_dev/public/js/adminConfigPage.js'], '_dev/public/js/adminConfigPage.js')
mix.babel(['_dev/public/js/adminOrderPage.js'], '_dev/public/js/adminOrderPage.js')
mix.babel(['_dev/public/js/adminOrderCreatePage.js'], '_dev/public/js/adminOrderCreatePage.js')
mix.babel(['_dev/public/js/checkoutMultibancoPage.js'], '_dev/public/js/checkoutMultibancoPage.js')
mix.babel(['_dev/public/js/checkoutMbwayPage.js'], '_dev/public/js/checkoutMbwayPage.js')
mix.babel(['_dev/public/js/checkoutPayshopPage.js'], '_dev/public/js/checkoutPayshopPage.js')
mix.babel(['_dev/public/js/checkoutCcardPage.js'], '_dev/public/js/checkoutCcardPage.js')
mix.babel(['_dev/public/js/checkoutCofidisPage.js'], '_dev/public/js/checkoutCofidisPage.js')
mix.babel(['_dev/public/js/checkoutIfthenpaygatewayPage.js'], '_dev/public/js/checkoutIfthenpaygatewayPage.js')
mix.babel(['_dev/public/js/adminOrderInfoPage.js'], '_dev/public/js/adminOrderInfoPage.js')
