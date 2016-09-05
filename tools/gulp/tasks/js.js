var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var gzip = require('gulp-gzip');
var config = require('../config.js');
var notify    = require('gulp-notify');
var plumber = require('gulp-plumber');
var jsPath = config.js.dest;
var cssPath = config.css.dest;
var destPath = jsPath+'/brandco/dest';
var topPath = jsPath+'/brandco';

gulp.task('jsConcat', function() {
    gulp.src([
            jsPath+'/brandco/Brandco.js',
            jsPath+'/brandco/Brandco.net.js',
            jsPath+'/brandco/Brandco.api.js',
            jsPath+'/brandco/Brandco.helper.js',
            jsPath+'/brandco/Brandco.message.js',
            jsPath+'/brandco/Brandco.paging.js'
        ])
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(concat('lib-all.js', {newLine: ''}))
        .pipe(uglify())
        .pipe(gulp.dest(destPath));

    gulp.src([
            jsPath+'/farbtastic_unit.js',
            jsPath+'/farbtastic.js'
        ])
        .pipe(plumber({
        errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(concat('farbtastic-all.js', {newLine: ''}))
        .pipe(gulp.dest(jsPath));
    return;
});

gulp.task('jsMinify', function() {
    gulp.src([
            jsPath+'/cmt_plugin.js',
            jsPath+'/admin_unit.js',
            jsPath+'/unit.js',
            jsPath+'/unit_sp.js',
            jsPath+'/jquery.blockUI.js',
            jsPath+'/farbtastic-all.js',
            jsPath+'/html5shiv-printshiv.js'
        ])
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(jsPath+'/min'));

    gulp.src(topPath+'/services/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest'));

    gulp.src(topPath+'/services/admin-cp/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest/admin-cp'));

    gulp.src(topPath+'/services/admin-blog/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest/admin-blog'));

    gulp.src(topPath+'/services/admin-settings/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest/admin-settings'));

    gulp.src(topPath+'/services/admin-code_auth/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest/admin-code_auth'));

    gulp.src(topPath+'/services/admin-coupon/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest/admin-coupon'));

    gulp.src(topPath+'/services/admin-dashboard/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest/admin-dashboard'));

    gulp.src(topPath+'/services/admin-fan/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest/admin-fan'));

    gulp.src(topPath+'/services/user/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest/user'));

    gulp.src(topPath+'/services/admin-segment/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest/admin-segment'));

    gulp.src(topPath+'/services/admin-comment/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest/admin-comment'));

    gulp.src(topPath+'/services/auth/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest/auth'));

    gulp.src(topPath+'/services/plugin/*.js')
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(topPath+'/dest/plugin'));

    return;
});

gulp.task('jsGzip', function() {
    gulp.src(jsPath+'/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(jsPath));

    gulp.src(jsPath+'/flexslider/*-min.js')
        .pipe(gzip())
        .pipe(gulp.dest(jsPath+'/flexslider'));

    gulp.src(jsPath+'/infinitescroll/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(jsPath+'/infinitescroll'));

    gulp.src(jsPath+'/min/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(jsPath+'/min'));

    gulp.src(jsPath+'/zeroclipboard/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(jsPath+'/zeroclipboard'));

    gulp.src(destPath+'/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(destPath));

    gulp.src(destPath+'/admin-blog/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(destPath+'/admin-blog'));

    gulp.src(destPath+'/admin-code_auth/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(destPath+'/admin-code_auth'));

    gulp.src(destPath+'/admin-coupon/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(destPath+'/admin-coupon'));

    gulp.src(destPath+'/admin-fan/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(destPath+'/admin-fan'));

    gulp.src(destPath+'/admin-dashboard/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(destPath+'/admin-dashboard'));

    gulp.src(destPath+'/admin-settings/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(destPath+'/admin-settings'));

    gulp.src(destPath+'/user/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(destPath+'/user'));

    gulp.src(destPath+'/admin-segment/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(destPath+'/admin-segment'));

    gulp.src(destPath+'/admin-comment/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(destPath+'/admin-comment'));

    gulp.src(destPath+'/plugin/*.min.js')
        .pipe(gzip())
        .pipe(gulp.dest(destPath+'/plugin'));

    return;
});

gulp.task('cssGzip', function() {
    gulp.src(cssPath+'/*.css')
        .pipe(gzip())
        .pipe(gulp.dest(cssPath));

    gulp.src(cssPath+'/flexslider/*.css')
        .pipe(gzip())
        .pipe(gulp.dest(cssPath+'/flexslider'));
    return;
});