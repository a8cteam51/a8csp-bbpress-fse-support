# bbPress FSE Support

bbPress FSE Support is a WordPress plugin that helps bbPress forum screens render inside block themes. It replaces the bbPress template output with block template content that includes the active theme's header and footer template parts and a constrained `<main>` area for the selected bbPress template part.

## What is in this repository

- `a8csp-bbpress-fse-support.php` contains the plugin header and the complete runtime implementation.
- `index.php` is the standard empty guard file.
- `composer.json` and `composer.lock` define the Composer-managed PHP QA toolchain.
- `.phpcs.xml`, `.phpmd.xml`, and `.phpstan.neon` extend the shared Team51 QA configuration from Composer dependencies.
- `.github/workflows/` contains GitHub Actions workflows for PHP QA, PHP syntax checks, and Codeception test execution.

There are no custom blocks, block metadata files, theme files, REST routes, shortcodes, custom post types, taxonomies, options, or WP-CLI commands in this repository.

## Runtime requirements

- WordPress 6.7 or later, from the plugin header.
- PHP 8.3 or later, from the plugin header and Composer requirement.
- bbPress must be installed and active. The plugin exits during `init` when the bbPress `is_bbpress()` function is unavailable.
- A block theme is expected for the Full Site Editing behavior to have an effect.

## Plugin behavior

On `init`, the plugin registers `a8csp_bbpress_fse_support_theme_support()` on the `bbp_template_include_theme_supports` filter when bbPress is available.

For bbPress pages that are not BuddyPress pages, the filter builds block template content in WordPress's `$_wp_current_template_content` global:

- `<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->`
- a full-width constrained `main.wp-block-group` wrapper
- `<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->`

Inside the main wrapper, `a8csp_bbpress_fse_support_template_include()` delegates to bbPress template parts for user profiles, favorites, subscriptions, custom views, search, forum screens, topic screens, reply screens, and topic tag archives. Single forum, topic, and reply screens preserve bbPress forum visibility checks and render `feedback/no-access` for private forums the current user cannot view. Reply edit screens also call `bbp_set_post_lock()`.

If BuddyPress is active and the current request is a BuddyPress page, the original bbPress template path is returned unchanged.

## Local development

Install Composer dependencies before running QA commands:

```sh
composer run-script packages-install
```

Available Composer scripts:

```sh
composer run-script lint:php
composer run-script lint:php:phpcs
composer run-script lint:php:phpmd
composer run-script lint:php:phpstan
composer run-script format:php
```

For manual WordPress testing, place this repository at `wp-content/plugins/a8csp-bbpress-fse-support`, activate bbPress, activate this plugin, and use a block theme with the Site Editor available.

## CI and tests

The tracked GitHub Actions workflows include:

- `php-quality-assurance.yml` runs on pushes to `trunk`, validates Composer metadata, installs Composer dependencies, and runs `composer run-script lint:php` on PHP 8.3.
- `php-syntax-errors.yml` runs on `trunk` and `develop`, with `php -l` syntax checks for PHP 8.3 and newer and a bootstrap-file-only fallback check for older PHP versions.
- `codeception-tests.yml` runs on `trunk` and defines a WordPress/browser test matrix, but the supporting files it references, including `package.json`, `tests/.dist.env`, npm scripts, and wp-env configuration, are not tracked in this repository. Treat that workflow as incomplete until those files are added or restored.

## Maintenance notes

- The runtime code is currently a single bootstrap file. Keep new behavior prefixed with `a8csp_` or `a8csp_bbpress_fse_support_` to match `.phpcs.xml`.
- Composer dependencies are installed into `vendor/`, which is ignored and should not be committed.
- Node artifacts such as `node_modules/`, build output, caches, and coverage directories are ignored by `.gitignore`.
- The tracked `LICENSE` file is GPL v3, and the plugin header declares GPL v3 or later. `composer.json` currently declares `GPL-2.0-or-later`; reconcile that metadata before using Composer package metadata for distribution.

## License

GPL v3 or later, per the plugin header. The repository also includes the GPL v3 license text in `LICENSE`.
