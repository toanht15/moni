var gulp = require('gulp');
var watch = require('gulp-watch');
var config = require('../config.js');

// file watch
gulp.task('watch', function() {
    watch(config.css.src, function(){
        gulp.start(['compileSass']);
    });
    watch(config.img.src, function(){
        gulp.start(['imgmin']);
    });
    watch(config.html.src, function(){
        gulp.start(['ect']);
    });
});