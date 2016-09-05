var path = require('path');
var ECT = require('ect');
var es = require('event-stream');
var gulp = require('gulp');
var rename = require('gulp-rename');
var prettify = require('gulp-jsbeautifier');
var replace = require('gulp-replace');
var plumber = require('gulp-plumber');
var notify  = require('gulp-notify');
var changed = require('gulp-changed');
var debug = require('gulp-debug');
var config = require('../config.js');

gulp.task('ect', function() {
    var data = {
    };

    gulp.src(config.html.src)
        .pipe(changed(config.html.dest, {extension: '.html'}))
        .pipe(plumber({
            errorHandler: notify.onError('Error: <%= error.message %>')
        }))
        .pipe(compileEct(data))
        .pipe(rename(function(item) {
            item.extname = '.html';
        }))
        .pipe(prettify({
            indentSize: 4,
            indentChar: " ",
            unformatted: ["script", "a", "span", "strong", "small", "label", "h1", "h2", "h3", "h4", "h5", "h6", "input", "br"],
            logSuccess: false
        }))
        .pipe(replace(/\s{4}\<\!\-\- \//gm, '<!-- /'))
        .pipe(replace(/([^\>]) \-\-\>\s*\</gm, '$1 --><'))
        .pipe(replace(/\> {2,}\</gm, '> <'))
        .pipe(debug({title: 'unicorn:'}))
        .pipe(gulp.dest(config.html.dest));
});

function compileEct(optdata) {
    return es.map(function(data, callback) {
        optdata.title = path.basename(data.path, '.ect');
        optdata.htmlDir = path.relative(path.join(config.root, '_html/pages/'), path.dirname(data.path));

        var renderer = ECT({
            root: path.join(config.root, '_html'),
            ext: '.ect'
        });

        try {
            renderer.render(data.path, optdata, function (error, html) {
                if(error) console.log('Error ect detail: ' + error);
                data.contents = new Buffer(html);
            });
        } catch (e) {
            console.log('Error ect: ' + e.message);
        }

        callback(null, data);
    });
};