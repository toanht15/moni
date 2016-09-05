var gulp = require('gulp');
var imagemin = require('gulp-imagemin');
var changed  = require('gulp-changed');
var path = require("path");
var rename = require("gulp-rename");
var config = require('../config.js');
var debug = require('gulp-debug');

// image min
var imageminOptions = {
    optimizationLevel: 3, // png
    progressive: true
}
gulp.task('imgmin', function() {
    gulp.src(config.img.src)
        .pipe(debug({title: 'imgmin:'}))
        .pipe(changed(config.img.dest))
        .pipe(imagemin(imageminOptions))
        .pipe(gulp.dest(config.img.dest));
});