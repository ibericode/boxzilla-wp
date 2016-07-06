'use strict';

var gulp = require('gulp');
var uglify = require('gulp-uglify');
var rename = require("gulp-rename");
var cssmin = require('gulp-cssmin');
var source = require('vinyl-source-stream');
var browserify = require('browserify');
var merge = require('merge-stream');
var streamify = require('gulp-streamify');
var globby = require('globby');
var buffer = require('vinyl-buffer');
var through = require('through2');
var sourcemaps = require('gulp-sourcemaps');
var wrap = require('gulp-wrap');
var wpPot = require('gulp-wp-pot');
var sort = require('gulp-sort');
var sass = require('gulp-sass');

gulp.task('default', ['sass', 'browserify', 'uglify', 'languages' ]);

gulp.task('sass', function () {
    var files = './assets/scss/[^_]*.scss';

    return gulp.src(files)
        // create .css file
        .pipe(sass())
        .pipe(rename({ extname: '.css' }))
        .pipe(gulp.dest('./assets/css'))

        // create .min.css
        .pipe(cssmin())
        .pipe(rename({extname: '.min.css'}))
        .pipe(gulp.dest("./assets/css"));
});

gulp.task('browserify', function () {
    var bundledStream = through()
        .pipe(buffer());

    globby("./assets/browserify/[^_]*.js").then(function(entries) {
        merge(entries.map(function(entry) {
            var filename = entry.split('/').pop();
            return browserify({entries: [entry]})
                .bundle()
                .on('error', console.log)
                .pipe(source(filename))
                .pipe(wrap('(function () { var require = undefined; var module = undefined; var exports = undefined; var define = undefined; <%=contents%>; })();'))

                // create .js file
                .pipe(rename({ extname: '.js' }))
                .pipe(gulp.dest('./assets/js'));
        })).pipe(bundledStream);
    }).catch(function(err) {
        console.log(err);
    });

    return bundledStream;
});

gulp.task('uglify', ['browserify'], function() {
    return gulp.src(['./assets/js/**/*.js','!./assets/js/**/*.min.js'])
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(streamify(uglify().on('error', console.log)))
        .pipe(rename({extname: '.min.js'}))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./assets/js'));
});

gulp.task('languages', function () {
    return gulp.src('src/**/*.php')
        .pipe(sort())
        .pipe(wpPot( {
            domain: 'boxzilla',
            destFile:'boxzilla.pot'
        } ))
        .pipe(gulp.dest('languages'));
});