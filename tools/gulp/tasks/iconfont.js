var gulp = require('gulp');
var iconfont = require('gulp-iconfont');
var consolidate = require("gulp-consolidate");
var rename = require("gulp-rename");
var config = require('../config');

var debug = require('gulp-debug');


gulp.task('iconfont', function(){
    var fontName = 'font';

    gulp.src(config.font.src)
        .pipe(iconfont({
            fontName: fontName
        }))
        .on('codepoints', function(codepoints){
            gulp.src(config.font.css.base)
                .pipe(debug())
                .pipe(consolidate('lodash', {
                    glyphs: codepoints,
                    fontName: fontName,
                    fontPath: '../font/',
                    className: 'font'
                }))
                .pipe(gulp.dest(config.font.css.dest));
        })
        .pipe(gulp.dest(config.font.dest));
})