const gulp = require('gulp');
const babel = require('gulp-babel');
const uglify = require('gulp-uglify');
const sourcemaps = require('gulp-sourcemaps');
const eslint = require('gulp-eslint');
const restrictedGlobals = require('eslint-restricted-globals');

// Compile JS
gulp.task('scripts', function() {
  return gulp.src('./src/**/*.js')
  .pipe(sourcemaps.init())
  .pipe(babel({
    presets: ['env']
  }))
  .pipe(uglify())
  .pipe(sourcemaps.write('.'))
  .pipe(gulp.dest(function(file) {
    return file.base.replace(/\/src/, '/skin');
  }));
});

// LINT JS
gulp.task('lint', function() {
  return gulp.src(['./src/**/*.js'])
  .pipe(eslint({
    'rules': {
      'quotes': ['error', 'single'],
      'semi': ['error', 'always'],
      'indent': ["error", 2, { "SwitchCase": 1 }],
      'one-var-declaration-per-line': ['error', 'always'],
      'no-unused-vars': ['error', { vars: 'all', args: 'after-used', ignoreRestSiblings: true }],
      'no-use-before-define': ['error', { functions: true, classes: true, variables: true }],
      'no-undef': 'error',
      'no-restricted-globals': ['error', 'isFinite', 'isNaN'].concat(restrictedGlobals),
      'comma-dangle': ['error', 'always-multiline'],
      'comma-spacing': ['error', { before: false, after: true }],
      'comma-style': ['error', 'last'],
      'no-console': 'warn',
      'no-alert': 'warn',
      'eol-last': ["error", "always"],
      //CUSTOM
      'prefer-const': 'error',
      'no-empty': 'error',
      'consistent-return': 0,
    },
    'parserOptions': {
      'ecmaVersion': 6,
      'sourceType': 'module',
    },
    'globals': [
      'browser',
      'define',
      'document',
      'jQuery',
      '$',
    ],
    'envs': [
        'browser',
        'es6',
        'commonjs',
    ],
  }))
  .pipe(eslint.format())
  .pipe(eslint.results(results => {
    console.log('');
    console.log('\x1b[37m', '---------------------------------------------------');
    console.log('\x1b[37m', `Total Results: ${results.length}`);
    console.log('\x1b[33m', `Total Warnings: ${results.warningCount}`);
    console.log('\x1b[31m', `Total Errors: ${results.errorCount}`);
    console.log('\x1b[37m', '---------------------------------------------------');
    console.log('');
  }))
  .pipe(eslint.failAfterError());
});

// Watch Files For Changes
gulp.task('watch', function() {
  process.argv.push('--silent');
  gulp.start('default');
  gulp.watch('./src/**/*.js', ['scripts', 'lint']);
});

// Default Task
gulp.task('default', gulp.parallel('scripts', 'lint'));
