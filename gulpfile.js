'use strict';

var gulp = require('gulp'),
  autoprefixer = require('gulp-autoprefixer'),
  rename = require('gulp-rename'),
  sass = require('gulp-sass'),
  sourcemaps = require('gulp-sourcemaps'),
  uglify = require('gulp-uglify');

// Task to compile SCSS files to CSS files
gulp.task('styles', function() {
  return gulp.src('Resources/Private/Scss/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(rename({suffix: '.min'}))
    .pipe(sourcemaps.write('.', {includeContent: false}))
    .pipe(gulp.dest('Resources/Public/Css'));
});

// Task to compress javascript files
gulp.task('scripts', function() {
  return gulp.src('Resources/Public/JavaScript/!(*.min)*.js')
    .pipe(sourcemaps.init())
    .pipe(uglify({output: {comments: '/^!/'}}))
    .pipe(rename({suffix: '.min'}))
    .pipe(sourcemaps.write('.', {includeContent: false}))
    .pipe(gulp.dest('Resources/Public/JavaScript'));
});

gulp.task('font-awesome', function() {
  return gulp.src('node_modules/font-awesome/fonts/*').pipe(gulp.dest('Resources/Public/Fonts/FontAwesome'));
});

gulp.task('watch', function() {
  gulp.watch('Resources/Private/Scss/**/*.scss', gulp.series('styles'));
  gulp.watch('Resources/Public/JavaScript/!(*.min)*.js', gulp.series('scripts'));
});

gulp.task('default', gulp.series('styles', 'scripts', 'font-awesome', 'watch'));
