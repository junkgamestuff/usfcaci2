/* global require */
var gulp = require('gulp');
var del = require('del');
var rename = require('gulp-rename');
var eslint = require('gulp-eslint');
var gulpSass = require('gulp-sass');
var nodeSass = require('node-sass');
var sass = gulpSass(nodeSass);
var sassGlob = require('gulp-sass-glob');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var livereload = require('gulp-livereload');
var sassLint = require('gulp-sass-lint');
var autoprefixer = require('gulp-autoprefixer');
var run = require('gulp-run');

var paths = {
  scripts: [
    'source/js/**/*.js',
    '!source/js/**/*.min.js',
    '!source/js/vendor/**/*'
  ],
  sass: {
    main: 'source/scss/style.scss',
    watch: ['source/scss/**/*', 'source/_patterns/**/*.scss', 'source/_layouts/**/*.scss']
  },
  css: {
    root: 'source/css'
  },
  clean: [
    'source/css/style.css',
    'source/css/**/*.css.map',
    'source/js/**/*.min.js'
  ],
  patterns: [
    'source/_patterns/**/*.twig',
    'source/_patterns/**/*.json',
    'source/_patterns/**/*.md',
    'source/_data/**/*.json'
  ],
  js: {
    components: 'source/_patterns/05-components/**/*.js',
    vendor: [
      'node_modules/jquery/dist/jquery.min.js',
      'node_modules/jquery-once/jquery.once.min.js',
      'node_modules/imagesloaded/imagesloaded.pkgd.min.js',
      'node_modules/swiper/swiper-bundle.min.js',
      'node_modules/what-input/dist/what-input.min.js',
      'node_modules/ally.js/ally.min.js',
      'node_modules/formstone/dist/js/core.js',
      'node_modules/formstone/dist/js/mediaquery.js',
      'node_modules/lity/dist/lity.min.js',
      'node_modules/sticky-sidebar/dist/jquery.sticky-sidebar.min.js',
      'node_modules/jquery-hoverintent/jquery.hoverIntent.min.js',
      'node_modules/tinynav/dist/tinynav.min.js',
      'node_modules/lazysizes/lazysizes.min.js'
    ],
    watch: [
      'source/_patterns/05-components/**/*.js',
      'source/js/*.js'
    ],
    dist: '/source/js'
  },
  vendor_css: [
    'node_modules/swiper/swiper-bundle.min.css',
    'node_modules/lity/dist/lity.min.css'
  ],
  source: {
    css: 'source/css/**/*',
    js: 'source/js/**/*',
    images: 'source/images/**/*'
  }
};

gulp.task('clean', function () {
  'use strict';
  return del(paths.clean);
});

gulp.task('js-lint', function () {
  'use strict';
  return gulp.src(paths.scripts)
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(eslint.failAfterError());
});

gulp.task('js_components', function () {
  'use strict';
  return gulp.src(paths.js.components)
    .pipe(rename({ dirname: 'components' }))
    .pipe(gulp.dest('source/js'));
});

gulp.task('js_vendor', function () {
  'use strict';
  return gulp.src(paths.js.vendor)
    .pipe(gulp.dest('source/js/vendor'));
});

gulp.task('vendor_css', function () {
  'use strict';
  return gulp.src(paths.vendor_css)
    .pipe(gulp.dest('source/css/vendor'));
});

gulp.task('sass-lint', function () {
  'use strict';
  return gulp.src(paths.sass.watch)
    .pipe(sassLint({}))
    .pipe(sassLint.format())
    .pipe(sassLint.failOnError());
});

gulp.task('lint', gulp.series(gulp.parallel('js-lint', 'sass-lint')));

gulp.task('compress', function () {
  'use strict';
  return gulp.src(paths.scripts)
    .pipe(rename({ suffix: '.min' }))
    .pipe(uglify())
    .pipe(gulp.dest('source/js/dist'));
});

gulp.task('sass', function () {
  'use strict';
  return gulp.src(paths.sass.main)
    .pipe(sassGlob())
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: [
        'node_modules/support-for/sass'
      ]
    }).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('./maps'))
    .pipe(gulp.dest(paths.css.root));
});

gulp.task('sourcemaps', function () {
  'use strict';
  return gulp.src(paths.sass.main)
    .pipe(sassGlob())
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(paths.css.root));
});

gulp.task('generate', gulp.series(gulp.parallel('sass', 'js_components', 'js_vendor', 'vendor_css'), gulp.parallel('compress'), async function generate() {
  'use strict';

  return run('php core/console --generate')
    .exec('', function () {
      livereload.reload();
    });
}));

gulp.task('build', gulp.series(gulp.parallel('sass', 'js_components', 'js_vendor', 'vendor_css'), gulp.parallel('compress'), async function build() {
  'use strict';
  console.log('Assets built.');
}));

gulp.task('watch', gulp.series(gulp.parallel('generate'), function watch() {
  'use strict';
  livereload.listen();
  gulp.watch(paths.sass.watch, gulp.parallel('generate'));
  gulp.watch(paths.js.watch, gulp.parallel('generate'));
  gulp.watch(paths.patterns, { delay: 1000 }, gulp.parallel('generate'));
  console.log('Magic happens on: http://localhost:8080');
}));

gulp.task('serve', gulp.parallel('watch', function serve() {
  'use strict';

  return run('php core/console --server')
    .exec('');
}));

gulp.task('default', gulp.parallel('build'));
