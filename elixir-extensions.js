var gulp = require('gulp');
var shell = require('gulp-shell');
var Elixir = require('laravel-elixir');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var gutil = require('gulp-util');
var uglify = require('gulp-uglify');
var Task = Elixir.Task;

Elixir.extend('vendor_js', function() {
    new Task('vendor_js', function() {
		return gulp.src('resources/assets/js/vendor/*.js')
			.pipe(sourcemaps.init())
			.pipe(concat('vendor.js'))
			//only uglify if gulp is ran with '--type production'
			.pipe(gutil.env.type === 'production' ? uglify() : gutil.noop())
			.pipe(sourcemaps.write('../js'))
			.pipe(gulp.dest('public/js'));
    });
});
Elixir.extend('components_js', function() {
    new Task('components_js', function() {
		return gulp.src('resources/assets/js/components/*.js')
			.pipe(sourcemaps.init())
			.pipe(concat('components.js'))
			//only uglify if gulp is ran with '--type production'
			.pipe(gutil.env.type === 'production' ? uglify() : gutil.noop())
			.pipe(sourcemaps.write('../js'))
			.pipe(gulp.dest('public/js'));
    });
});

Elixir.extend('app_js', function() {
    new Task('app_js', function() {
		return gulp.src('resources/assets/js/app.js')
			.pipe(sourcemaps.init())
			//only uglify if gulp is ran with '--type production'
			.pipe(gutil.env.type === 'production' ? uglify() : gutil.noop())
			.pipe(sourcemaps.write('../js'))
			.pipe(gulp.dest('public/js'));
    });
});
