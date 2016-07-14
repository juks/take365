'use strict';

const babelify = require('babelify');
const browserify = require('browserify');
const concat = require('gulp-concat');
const es2015 = require('babel-preset-es2015');
const gulp = require('gulp');
const gutil = require('gulp-util');
const react = require('babel-preset-react');
const source = require('vinyl-source-stream');
const modulesify = require('css-modulesify')

gulp.task('default', ['jsx']);

function getBundler() {
  const bundler = browserify('./web/blocks/comment-list/comment-list.jsx', {
    debug: process.env.NODE_ENV !== 'production', // add sourcemap
    cache: {},
    packageCache: {},
  })
  .plugin(modulesify, {
    rootDir: __dirname,
    output: './web/css/react.css',
    generateScopedName: process.env.NODE_ENV === 'production' ? modulesify.generateShortName : undefined,
  })
  .transform(babelify.configure({
    presets: [es2015, react],
  }));

  bundler.on('log', gutil.log);

  return bundler;
}

gulp.task('jsx', bundle.bind(null, getBundler()));

function bundle(pkg) {
  gutil.log('Compiling JS...');
  // wait rollup in Babel
  // https://github.com/babel/babel/issues/1681
  return pkg.bundle()
    .on('error', gutil.log.bind(gutil, 'Browserify Error'))
    .pipe(source('react.js'))
    .pipe(gulp.dest('web/js'));
}

gulp.task('watch', function() {
  const batch = require('gulp-batch');
  const watch = require('gulp-watch');
  const watchify = require('watchify');

  function start(task) {
    return batch(function(events, done) {
      gulp.start(task, done);
    });
  }

  //watch('client/**/*.js', start('js'));
  //watch('client/**/*.html', start('html'));
  watch('web/blocks/**/*.styl', start('styl'));
  //watch('client/**/*.{ico,svg,png,jpg}', start('img'));

  const b = getBundler();
  b.plugin(watchify);
  b.on('update', bundle.bind(null, b));
  return bundle(b);
});

gulp.task('serve', ['watch'], () => {
  const browserSync = require('browser-sync').create();
  browserSync.init({
    files: [{
      match: 'web',
      options: {
        ignored: 'web/blocks',
      },
    }],
    open: false,
    proxy: 'http://localhost:' + (process.env.PORT || 8081),
    port: 4000,
  });
});
