var gulp = require("gulp");
var uglify = require('gulp-uglify');
var gulpIf = require('gulp-if');
var useref = require('gulp-useref');
var sass = require('gulp-sass');

gulp.task('sass', function(){
  return gulp.src('scss/style.scss')
    .pipe(sass())
    .pipe(gulp.dest('css/'))
});

gulp.task('vendor', function(){
  return gulp.src('vendor.html')
    .pipe(useref())
    .pipe(gulp.dest('vendor/'))
});

gulp.task('default', ['sass', 'vendor'] , function() {
	console.log('Building files');
});

gulp.task('watch', function(){
  gulp.watch('scss/*.scss', ['sass']); 
  gulp.watch('vendor.html', ['vendor']);
});
