var gulp = require('gulp');
var requireDir = require('require-dir');
var dir = requireDir('./tools/gulp/tasks/', {recurse: true});

gulp.task('default', ['webserver', 'watch'], function() {
    console.log('run gulp!!');
});