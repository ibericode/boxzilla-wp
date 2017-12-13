'use strict';

const gulp = require('gulp');
const uglify = require('gulp-uglify');
const rename = require("gulp-rename");
const cssmin = require('gulp-cssmin');
const source = require('vinyl-source-stream');
const browserify = require('browserify');
const merge = require('merge-stream');
const streamify = require('gulp-streamify');
const globby = require('globby');
const buffer = require('vinyl-buffer');
const through = require('through2');
const sourcemaps = require('gulp-sourcemaps');
const wrap = require('gulp-wrap');
const wpPot = require('gulp-wp-pot');
const sort = require('gulp-sort');
const sass = require('gulp-sass');
const babelify = require('babelify');
const fs = require("fs");

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
            var bundler = browserify({entries: [entry]})
                .transform(babelify, {
                    global: true,
                    ignore: /\/node_modules\/(?!boxzilla\/)/,
                    presets: ["es2015"]
                });

            var filename = entry.split('/').pop();

            return bundler
                .bundle()
                .pipe(source(filename))
                .pipe(buffer())
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
        .pipe(gulp.dest('./languages/.'));
});


gulp.task('watch', function () {
    gulp.watch('./*/assets/sass/**/*.scss', ['sass']);
    gulp.watch('./*/assets/browserify/**/*.js', ['browserify']);
});
