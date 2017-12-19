var gulp = require('gulp');

var gutil = require('gulp-util');
var sass = require('gulp-ruby-sass');
var rename = require('gulp-rename');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var cleanCss = require('gulp-clean-css');
var wait = require('gulp-wait');
var del = require('del');
var zip = require('gulp-zip');

/**
 * =Compile SCSS and move to build
 */
gulp.task('sass', function () {

    var admin = sass('src/scss/apd-admin.scss', {style: 'compressed'})
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('../css'));

    var frontend = sass('src/scss/apd-frontend.scss', {style: 'compressed'})
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('../css'));

});

/**
 * =Concat and minify JS
 */
gulp.task('js', function () {

    var apd = [
        // 'bower_components/bootstrap-sass/assets/javascripts/bootstrap.js',
        'src/js/**/*',
        'src/js/!apd-admin.js'
    ];

    gulp.src(apd)
        .pipe(concat('apd-frontend.js'))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('../js'));

    var admin = [
        'src/js/apd-admin.js'
    ];

    gulp.src(admin)
        .pipe(concat('apd-admin.js'))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('../js'));

});


/**
 * =Copy images
 */
gulp.task('img', function () {

    var img = [
        'src/img/**/*'
    ];

    gulp.src(img, {base: 'src/img'})
        .pipe(gulp.dest('../img'));

});


/**
 * =Copy PHP
 */
gulp.task('php', function () {

    var php = [
        'src/php/**/*'
    ];

    gulp.src(php, {base: 'src/php'})
        .pipe(gulp.dest('..'));
});


/**
 * =Watcher
 */
gulp.task('watch', function () {

    gulp.watch('src/scss/**/*', ['sass']);
    gulp.watch('src/js/**/*', ['js']);
    gulp.watch('src/fonts/**/*', ['fonts']);
    gulp.watch('src/css/**/*', ['css']);
    gulp.watch('src/img/**/*', ['img']);
    gulp.watch('src/php/**/*', ['php']);

});

gulp.task('default', ['sass', 'js', 'img', 'php']);


/**
 * =Extract
 */
gulp.task('extract', function () {

    var files = ['../**/*', '!../.git*', '!../gulp/**/*', '!../gulp'];
    // var files = '**/*';
    gulp.src(files)
        .pipe(zip('affiliate-products-db.zip'))
        .pipe(gulp.dest('../..'))
});