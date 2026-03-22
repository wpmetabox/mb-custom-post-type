# AGENTS.md - Agent Guidelines for MB Custom Post Type

This file provides guidelines for agents working on this WordPress plugin codebase.

## Project Overview

MB Custom Post Types & Custom Taxonomies is a WordPress plugin that allows users to create and manage custom post types and custom taxonomies through an easy-to-use admin UI.

- **Language**: PHP (primary), JavaScript, SCSS
- **Architecture**: WordPress plugin using PSR-4 autoloading
- **Namespace**: `MBCPT`
- **Source directory**: `src/`
- **Autoload path**: `src/` maps to `MBCPT\`

---

## Build & Development Commands

### PHP

```bash
# Auto-fix code style issues
composer phpcbf

# Run PHP CodeSniffer (requires meta-box-aio phpcs.xml in parent directory)
composer phpcs
```

### JavaScript/SCSS

```bash
# Install dependencies
pnpm install

# Build assets (CSS + JS)
pnpm run build

# Build only CSS
pnpm run build:css

# Build only JS
pnpm run build:js

# Watch CSS for changes (development)
pnpm run watch:css

# Start development mode (JS hot reload)
pnpm run start
```

### Single Test Commands

This is a WordPress plugin and does not have a formal test suite. To test changes:
1. Activate the plugin in a WordPress installation
2. Create/update post types and taxonomies via the admin UI
3. Verify frontend/backend behavior

---

## Code Style Guidelines

### PHP

**General**
- Follow WordPress Coding Standards (https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- Use PSR-4 autoloading with namespace `MBCPT`
- Classes go in `src/` directory
- File naming: `ClassName.php` (PascalCase)

**Formatting**
- Use tabs for indentation (not spaces)
- Opening brace on same line for classes/functions
- Space after control structure keywords (`if`, `for`, `foreach`, `while`)
- No space before function call parentheses
- Trailing comma after last element in arrays (multi-line)

**Naming Conventions**
- Classes: PascalCase (`PostTypeRegister`, `TaxonomyRegister`)
- Methods: snake_case (`register()`, `updated_message()`)
- Properties: snake_case with optional underscore prefix (`$menu_positions`)
- Constants: UPPER_CASE with underscores

**Imports**
- Use `use` statements at top of file
- Group imports: internal first, then external
- Sort alphabetically within groups
- Example:
  ```php
  use MetaBox\Support\Arr;
  use WP_Post;
  ```

**Error Handling**
- Use WordPress functions for checking (`defined()`, `function_exists()`)
- Return early with meaningful messages
- Use `__return_true` / `__return_false` for simple filters

**Security**
- Always use `ABSPATH` check at top of files: `defined( 'ABSPATH' ) || die`
- Sanitize all user inputs with `sanitize_text_field()`, `esc_attr()`, etc.
- Escape output with `esc_html()`, `esc_url()`, `esc_attr()`
- Use nonces for form submissions and AJAX calls

**Documentation**
- Use DocBlock comments for classes and complex methods
- Use type hints for function parameters and return value. Include `@param` and `@return` for methods only when needed
- Add inline comments for complex logic

**Example Class Structure**:
```php
<?php
namespace MBCPT;

use MetaBox\Support\Arr;

class PostTypeRegister extends Register {
    private $menu_positions = [];

    public function register() {
        // Implementation
    }

    protected function normalize_checkbox( &$value ) {
        // Implementation
    }
}
```

### JavaScript

**Formatting**
- Use ES6+ syntax
- Use const/let instead of var
- Use arrow functions where appropriate, prefer one-line arrow function
- Don't use brackets if arrow functions have only one parameter
- Use template literals for string interpolation
- Use semicolons

**Naming**
- Variables/functions: camelCase
- Constants: UPPER_CASE
- File naming: kebab-case (`edit.js`, `post-type-order.js`)

**Code Style**
- WordPress JavaScript Coding Standards
- Use wp-scripts for building (uses webpack under the hood)

### SCSS/CSS

**Formatting**
- Use SCSS with variables and nesting
- Follow WordPress CSS coding standards
- Use tabs for indentation

---

## Important Notes

1. **Dependencies**: This plugin requires Meta Box core plugin to function. It uses `wpmetabox/support` package for shared utilities.

2. **Composer Autoload**: The plugin checks for `vendor/autoload.php` and loads it if exists. PSR-4 autoloading is configured in composer.json.

3. **Integration Classes**: The plugin includes integrations for WPML and Polylang in `src/Integrations/`.

4. **Constants**: Define plugin constants (`MB_CPT_DIR`, `MB_CPT_VER`, `MB_CPT_URL`) in the main plugin file.

5. **WordPress Hooks**: Use priority `0` for the main loading action hook (`add_action( 'init', 'mb_cpt_load', 0 );`) to ensure early loading.

---

## Common Tasks

**Creating a new class**:
1. Create file in `src/` with PascalCase name
2. Add namespace `MBCPT`
3. Add appropriate use statements
4. Implement required methods

**Adding a new hook**:
```php
add_action( 'init', [ $this, 'my_method' ], 0 );
add_filter( 'some_filter', [ $this, 'filter_callback' ] );
```

**Adding admin UI**:
- Classes that need admin-only functionality should check `is_admin()` before initializing
