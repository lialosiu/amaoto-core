var gulp = require('gulp');
var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

gulp.task("copyfiles", function () {
    gulp.src("bower_components/jquery/dist/jquery.js")
        .pipe(gulp.dest("resources/assets/js/vendor/"));

    gulp.src("bower_components/bootstrap/dist/css/bootstrap.css")
        .pipe(gulp.dest("resources/assets/css/vendor/"));
    gulp.src("bower_components/bootstrap/dist/js/bootstrap.js")
        .pipe(gulp.dest("resources/assets/js/vendor/"));

    gulp.src("bower_components/bootstrap/dist/fonts/**")
        .pipe(gulp.dest("public/fonts"));

    gulp.src("bower_components/bootstrap-material-design/dist/css/material-fullpalette.css")
        .pipe(gulp.dest("resources/assets/css/vendor/"));
    gulp.src("bower_components/bootstrap-material-design/dist/css/ripples.css")
        .pipe(gulp.dest("resources/assets/css/vendor/"));
    gulp.src("bower_components/bootstrap-material-design/dist/js/material.js")
        .pipe(gulp.dest("resources/assets/js/vendor/"));
    gulp.src("bower_components/bootstrap-material-design/dist/js/ripples.js")
        .pipe(gulp.dest("resources/assets/js/vendor/"));

    gulp.src("bower_components/bootstrap3-dialog/dist/css/bootstrap-dialog.css")
        .pipe(gulp.dest("resources/assets/css/vendor/"));
    gulp.src("bower_components/bootstrap3-dialog/dist/js/bootstrap-dialog.js")
        .pipe(gulp.dest("resources/assets/js/vendor/"));

    gulp.src("bower_components/animate.css/animate.css")
        .pipe(gulp.dest("resources/assets/css/vendor/"));

    gulp.src("bower_components/noty/js/noty/packaged/jquery.noty.packaged.js")
        .pipe(gulp.dest("resources/assets/js/vendor/"));

    gulp.src("bower_components/bootstrap-sweetalert/lib/sweet-alert.css")
        .pipe(gulp.dest("resources/assets/css/vendor/"));
    gulp.src("bower_components/bootstrap-sweetalert/lib/sweet-alert.js")
        .pipe(gulp.dest("resources/assets/js/vendor/"));

    gulp.src("bower_components/plupload/js/plupload.full.min.js")
        .pipe(gulp.dest("resources/assets/js/vendor/"));
    gulp.src("bower_components/plupload/js/Moxie.swf")
        .pipe(gulp.dest("public/swf/vendor/"));
    gulp.src("bower_components/plupload/js/Moxie.xap")
        .pipe(gulp.dest("public/xap/vendor/"));

    gulp.src("bower_components/handlebars/handlebars.js")
        .pipe(gulp.dest("resources/assets/js/vendor/"));

    gulp.src("bower_components/html5shiv/dist/html5shiv.js")
        .pipe(gulp.dest("resources/assets/js/vendor/"));
    gulp.src("bower_components/respond/dest/respond.min.js")
        .pipe(gulp.dest("resources/assets/js/vendor/"));
});

elixir(function (mix) {
    mix.less('common.less', 'resources/assets/css/vendor/common.css');

    mix.less('bootstrap-dialog-md-fix.less', 'resources/assets/css/vendor/bootstrap-dialog-md-fix.css');

    mix.babel('kurano.js', 'resources/assets/js/vendor/kurano.js');

    mix.styles([
        'vendor/bootstrap.css',
        'vendor/bootstrap-dialog.css',
        'vendor/animate.css',
        'vendor/sweet-alert.css',
        'vendor/common.css'
    ], 'public/css/app-bootstrap.css');

    mix.scripts([
        'vendor/jquery.js',
        'vendor/bootstrap.js',
        'vendor/bootstrap-dialog.js',
        'vendor/jquery.noty.packaged.js',
        'jquery.noty.config.js',
        'vendor/sweet-alert.js',
        'vendor/plupload.full.min.js',
        'vendor/handlebars.js',
        'vendor/kurano.js',
        'app.js'
    ], 'public/js/app-bootstrap.js');

    mix.styles([
        'vendor/bootstrap.css',
        'vendor/bootstrap-dialog.css',
        'vendor/bootstrap-dialog-md-fix.css',
        'vendor/material-fullpalette.css',
        'vendor/ripples.css',
        'vendor/animate.css',
        'vendor/sweet-alert.css',
        'vendor/common.css'
    ], 'public/css/app-bootstrap-md.css');

    mix.scripts([
        'vendor/jquery.js',
        'vendor/bootstrap.js',
        'vendor/bootstrap-dialog.js',
        'vendor/material.js',
        'vendor/ripples.js',
        'vendor/jquery.noty.packaged.js',
        'jquery.noty.config.js',
        'vendor/sweet-alert.js',
        'vendor/plupload.full.min.js',
        'vendor/handlebars.js',
        'vendor/kurano.js',
        'app.js'
    ], 'public/js/app-bootstrap-md.js');

    mix.scripts([
        'vendor/html5shiv.js',
        'vendor/respond.min.js'
    ], 'public/js/lt_ie9.js');

    mix.version([
        'css/app-bootstrap.css',
        'js/app-bootstrap.js',
        'css/app-bootstrap-md.css',
        'js/app-bootstrap-md.js',
        'js/lt_ie9.js'
    ]);
});