let mix = require('laravel-mix');
const autoprefixer = require('autoprefixer');
mix.js('resources/js/app.js', 'assets/js')
    .autoload({
        jquery: ['$', 'window.jQuery', 'jQuery']
    })
    .sass('resources/sass/app.scss', 'assets/css', {
        sassOptions: {
            outputStyle: 'expanded'
        }
    }).options({
    postCss: [
        autoprefixer({
            overrideBrowserslist: ['last 6 versions'],
            grid: true
        }),
        require('cssnano')()
    ]
});
