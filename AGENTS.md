# Repository Guidelines

## Project Structure & Module Organization

This repository contains the Boxzilla WordPress plugin. The plugin entry point is `boxzilla.php`; reusable PHP code lives in `src/`, with admin code in `src/admin/`, licensing code in `src/licensing/`, and dependency-injection helpers in `src/di/`. JavaScript source files live in `assets/js/src/` and are bundled by webpack into `assets/js/`. CSS and images are stored in `assets/css/` and `assets/img/`. Tests live in `tests/`, with PHPUnit tests such as `tests/admin/MigrationsTest.php` and functional HTML fixtures in `tests/functional/`. WordPress.org metadata is maintained in `readme.txt`; release notes are in `CHANGELOG.md`.

## Build, Test, and Development Commands

Install dependencies from the plugin root:

```sh
composer install
npm install
```

Use `npm run build` to create production JavaScript bundles and `npm run watch` during local frontend development. Run `composer test` for PHPUnit, `composer check-codestyle` for PHPCS, `composer static-analysis` for PHPStan, and `composer check-syntax` for PHP linting. `composer all-checks` runs syntax, coding standards, static analysis, and tests together. `composer check-plugin` runs the WordPress Plugin Check command against `boxzilla`; it requires a working WordPress/WP-CLI environment.

## Coding Style & Naming Conventions

Follow `.editorconfig`: UTF-8, LF endings, final newline, spaces for indentation, four spaces for PHP and two spaces for JavaScript/YAML. PHP coding standards are defined in `phpcs.xml.dist`: PSR-12 with local exclusions and short array syntax required. Keep WordPress-facing function, hook, option, and migration names consistent with existing `boxzilla` naming. JavaScript modules in `assets/js/src/boxzilla/` use lowercase filenames and ES module imports.

## Testing Guidelines

PHPUnit 10 is configured by `phpunit.xml.dist` with `tests/bootstrap.php`. Add new PHPUnit tests under `tests/`, matching the feature area where possible, for example `tests/admin/*Test.php`. The configuration is strict about warnings, risky tests, output during tests, and deprecations, so keep tests explicit and quiet.

## Commit & Pull Request Guidelines

Recent commits use short imperative messages, often with a scope-like prefix when useful, for example `chore: fix cs, short array syntax` or `prevent notices on new installations`. Keep commits focused and include generated asset changes when source JavaScript changes. Pull requests should describe the user-facing change, list verification commands run, link related issues, and include screenshots or screen recordings for admin UI or frontend display changes.

## Security & Configuration Tips

Do not commit local logs, secrets, generated caches, `vendor/`, or `node_modules/`. Treat licensing and update code in `src/licensing/` carefully: avoid exposing keys, tokens, or raw remote responses in notices, logs, or tests.
