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
const babelify = require('babelify');

gulp.task('css', function () {
    var files = './assets/css/*[^\.min].css';

    return gulp.src(files)
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

gulp.task('uglify', gulp.series('browserify', function() {
    return gulp.src(['./assets/js/**/*.js','!./assets/js/**/*.min.js'])
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(streamify(uglify().on('error', console.log)))
        .pipe(rename({extname: '.min.js'}))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./assets/js'));
}));

gulp.task('languages', function () {
    const domain = 'boxzilla';
    return gulp.src('src/**/**/*.php')
        .pipe(wpPot({ domain: domain}))
        .pipe(gulp.dest(`languages/${domain}.pot`));
});

gulp.task('watch', function () {
    gulp.watch('./*/assets/css/*.css', gulp.series('css'));
    gulp.watch('./*/assets/browserify/**/*.js', gulp.series('browserify'));
});

gulp.task('default', gulp.series('css', 'browserify', 'uglify', 'languages' ));
