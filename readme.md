http://collab.tastradesoft.com/public/index.php?path_info=projects/tastradesoft/notebooks/138

REQUIRED: Node.JS

#Cloning/New Laravel project (using project template)
1. Clone Repo
2. Setup .env
3. `composer install`
4. `sudo npm install`
5. `php artisan migrate` (if required)
7. `npm install --global gulp`
6. `php artisan serve`



#Styles
* Main SCSS: `resources/assets/sass/apps.scss`
* Bootstrap Vars : `resources/assets/sass/bootstrap/vars_override.scss`


#Scripts
* Vendors (3rd party scripts) JS: `resources/assets/js/vendor/`
⋅⋅⋅ order assets accordingly by adding number as file name prefix.
* Main JS: resources/assets/js/app.js


#Images
Directory: `public/images/`

#Compiling JS and CSS
* Compile: `gulp`
* Watcher: `gulp watch`
* Compile + minify: `gulp -type=production`