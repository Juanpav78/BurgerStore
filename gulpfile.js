const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));

function buildStyles() {
  return gulp.src('./src/sass/**/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./build/css'));
};

function watch(){
  return gulp.watch('./src/sass/**/*.scss', buildStyles);
}

exports.buildStyles = buildStyles;
exports.watch = gulp.series(buildStyles, watch) ;