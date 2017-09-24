var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

require('./elixir-extensions')


elixir(function(mix) {

    mix.scripts('resources/assets/js/app.js', 'public/js/app.js')
    .scripts('resources/assets/js/vendor/*.js', 'public/js/vendor.js')
    .scripts('resources/assets/js/components/*.js', 'public/js/components.js');
    //
    mix.sass('app.scss')
    .components_js()
    .vendor_js()
    .app_js()
    .version([
    		'css/app.css',
            'js/components.js',
    		'js/vendor.js',
    		'js/app.js'
    	]);
});
