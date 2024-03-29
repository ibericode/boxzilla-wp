'use strict'

const gulp = require('gulp')
const uglify = require('gulp-uglify')
const rename = require('gulp-rename')
const cssmin = require('gulp-cssmin')
const source = require('vinyl-source-stream')
const browserify = require('browserify')
const merge = require('merge-stream')
const streamify = require('gulp-streamify')
const globby = require('globby')
const buffer = require('vinyl-buffer')
const through = require('through2')
const sourcemaps = require('gulp-sourcemaps')
const wpPot = require('gulp-wp-pot')
const insert = require('gulp-insert')

gulp.task('js-styles', function () {
  return gulp.src('./assets/browserify/boxzilla/styles.css')
    .pipe(cssmin())
    .pipe(insert.wrap('const styles = `', '`; \nmodule.exports = styles;'))
    .pipe(rename({ extname: '.js' }))
    .pipe(gulp.dest('./assets/browserify/boxzilla'))
})

gulp.task('css', function () {
  const files = './assets/css/*[^.min].css'

  return gulp.src(files)
    .pipe(cssmin())
    .pipe(rename({ extname: '.min.css' }))
    .pipe(gulp.dest('./assets/css'))
})

gulp.task('browserify', function () {
  const bundledStream = through()
    .pipe(buffer())

  globby('./assets/browserify/[^_]*.js').then(function (entries) {
    merge(entries.map(function (entry) {
      const filename = entry.split('/').pop()

      return browserify(entry)
        .transform('babelify', {
          presets: ['@babel/preset-env'],
          global: true
        })
        .bundle()
        .pipe(source(filename))
        .pipe(buffer())
      // create .js file
        .pipe(rename({ extname: '.js' }))
        .pipe(gulp.dest('./assets/js'))
    })).pipe(bundledStream)
  }).catch(function (err) {
    console.log(err)
  })

  return bundledStream
})

gulp.task('boxzilla.js', function () {
  return browserify({
    entries: 'assets/browserify/boxzilla/boxzilla.js'
  }).transform('babelify', {
    presets: ['@babel/preset-env'],
    global: true
  }).bundle()
    .pipe(source('boxzilla.js'))
    .pipe(buffer())
    .pipe(gulp.dest('./tests/functional'))
})

gulp.task('uglify', gulp.series('browserify', function () {
  return gulp.src(['./assets/js/**/*.js', '!./assets/js/**/*.min.js'])
    .pipe(sourcemaps.init({ loadMaps: true }))
    .pipe(streamify(uglify().on('error', console.log)))
    .pipe(rename({ extname: '.min.js' }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('./assets/js'))
}))

gulp.task('languages', function () {
  const domain = 'boxzilla'
  return gulp.src('src/**/**/*.php')
    .pipe(wpPot({ domain: domain }))
    .pipe(gulp.dest(`languages/${domain}.pot`))
})

gulp.task('watch', function () {
  gulp.watch('./assets/css/*.css', gulp.series('css'))
  gulp.watch('./assets/browserify/**/*.js', gulp.series('browserify', 'boxzilla.js'))
})

gulp.task('default', gulp.series('js-styles', 'css', 'browserify', 'uglify', 'languages', 'boxzilla.js'))
