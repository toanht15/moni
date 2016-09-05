var gulp = require('gulp');
var webserver = require('gulp-webserver');
var config = require('../config.js');

//webserver
gulp.task('webserver', function() {
  var path = config.root;
  gulp.src(path)
    .pipe(webserver({
      livereload: true,
      port: 9001,
      directoryListing: {
        enable: true,
        path: path
      },
      open: true
    }));
});