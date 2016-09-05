var gulp = require('gulp');
var rubySass = require('gulp-ruby-sass');
var notify  = require('gulp-notify');
var minifyCSS = require('gulp-minify-css');
var plumber = require('gulp-plumber');
var csscomb = require('gulp-csscomb');
var path = require("path");
var config = require('../config.js');

// sass
gulp.task('compileSass', function() {
  gulp.src(config.css.src)
    .pipe(plumber({
        errorHandler: notify.onError('Error: <%= error.message %>')
    }))
    .pipe(rubySass({
      'sourcemap=none': true,
      style: 'compact'
    }))
    .pipe(minifyCSS({
      advanced: false,
      aggressiveMerging: false,
      keepBreaks: true,
      keepSpecialComments: true,
    }))
    .pipe(csscomb(path.join(process.cwd(), 'tools/gulp/csscomb.json')))
    .pipe(gulp.dest(config.css.dest));
});