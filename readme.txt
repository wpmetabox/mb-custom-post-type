=== MB Custom Post Types & Custom Taxonomies ===
Contributors: elightup, metabox, rilwis, duc88b, truongwp, barcavn2
Donate link: https://metabox.io/pricing/
Tags: custom post types, custom taxonomies, meta box, content types, cpt, post type, taxonomy, cms, post, post types, types, taxonomies, tax
Requires at least: 5.9
Tested up to: 6.2.2
Requires PHP: 7.0
Stable tag: 2.5.5
License: GPLv2 or later

Create and manage custom post types and custom taxonomies with an easy-to-use UI in WordPress.

== Description ==

**MB Custom Post Types & Custom Taxonomies** helps you to register and edit custom post types and custom taxonomies easily in WordPress by providing an easy-to-use UI in the admin area.

The plugin allows you to handle all post type's arguments and taxonomy's arguments such as menu labels, admin bar label, exclude from search, disable archive page, etc. just in minutes. You don't need to write custom PHP code to register custom post types anymore (using function `register_post_type()` and `register_taxonomy()`).

Using **MB Custom Post Types & Custom Taxonomies**, you will be able to craft the WordPress content types and turn it into a professional CMS (Content Management Systems).

### Features

* Supports all arguments for creating custom post types (like `register_post_type()`)
* Supports all arguments for creating custom taxonomies (like `register_taxonomy()`)
* Supports **live editing mode**, which auto fill in all necessary labels for you!
* **Export to PHP Code**
* Clean code
* Registered custom post types can be exported/imported using default WordPress functionality (no more plugins!)

### Plugin Links

- [Project Page](https://metabox.io/plugins/custom-post-type/)
- [Github Repo](https://github.com/rilwis/mb-custom-post-type/)

This plugin is a free extension of [Meta Box](https://metabox.io) plugin, which is a powerful, professional solution to create custom meta boxes and custom fields for WordPress. Using **MB Custom Post Types & Custom Taxonomies** in combination with [other Meta Box extensions](https://metabox.io/plugins/) will help you manage any content types in WordPress easily and make your website more professional.

### You might also like

If you like this plugin, you might also like our other WordPress products:

- [Slim SEO](https://wpslimseo.com): A fast, lightweight and full-featured SEO plugin for WordPress with minimal configuration.
- [Slim SEO Schema](https://wpslimseo.com/products/slim-seo-schema/): The best plugin to add schemas (structured data, rich snippets) to WordPress.
- [Slim SEO Link Manager](https://wpslimseo.com/products/slim-seo-link-manager/): Build internal link easier in WordPress with real-time reports.
- [GretaThemes](https://gretathemes.com): Free and premium WordPress themes that clean, simple and just work.
- [Auto Listings](https://wpautolistings.com): The car sale and dealership plugin for WordPress

== Installation ==

You need to install [Meta Box](https://metabox.io) plugin first

- Go to Plugins | Add New and search for Meta Box
- Click **Install Now** button to install the plugin
- After installing, click **Activate Plugin** to activate the plugin

Install **MB Custom Post Types & Custom Taxonomies** extension

- Go to **Plugins | Add New** and search for **MB Custom Post Types & Custom Taxonomies**
- Click **Install Now** button to install the plugin
- After installing, click **Activate Plugin** to activate the plugin

== Frequently Asked Questions ==

== Screenshots ==
1. All registered custom post types
1. Edit custom post type - General tab
1. Edit custom post type - Labels tab
1. Edit custom post type - Advanced tab
1. Edit custom post type - Supports tab
1. Edit custom post type - Taxonomies tab

== Changelog ==

= 2.5.5 - 2023-07-18 =
- Fix error when Meta Box is not available

= 2.5.4 - 2023-03-23 =
- Fix error when not using with Meta Box

= 2.5.3 - 2023-03-23 =
- Fix URL when installing with Composer

= 2.5.2 - 2023-02-27 =
- Enqueue Font Awesome only when a post type use it (#55)

= 2.5.1 - 2023-01-31 =
- Fix CPT export wrong file name
- Update description for FontAwesome icon select

= 2.5.0 - 2023-01-13 =
- Add support for Font Awesome in admin menu
- Add icon label and allow to search icons by label
- Fix import on Windows

= 2.4.0 - 2022-12-24 =
- Add import export feature for post types and taxonomies

= 2.3.5 - 2022-12-13 =
- Fix error with Meta Box 5.6.14 when edit post types
- Fix typo

= 2.3.4 - 2022-11-11 =
- Fix `meta_box_cb` settings not working properly

= 2.3.3 - 2022-07-27 =
- Improve post type and taxonomy label capitalization

= 2.3.2 - 2022-05-17 =
- Fix `meta_box_cb` parameter not working property for tags and categories

= 2.3.1 - 2022-01-13 =
- Fix the menu icon not showing from v2.2.6

= 2.3.0 - 2021-12-13 =
- Add migration tool from Custom Post Type UI

= 2.2.6 - 2021-10-28 =
- Fix changing menu icon not working

= 2.2.5 - 2021-10-20 =
- Fix generated code if labels contain single quotes
- Update list of Dashicons

= 2.2.4 - 2021-09-16 =
- Fix missing comma in the generated code.

= 2.2.2 - 2021-09-10 =
- Fix generated code
- Restrict access to admin menu for admins only

= 2.2.1 - 2021-07-26 =
- Fix custom archive slug not working

= 2.2.0 - 2021-07-05 =
- Add a warning if permalink is default.
- Add options for menu icon type, which supports SVG and custom URL.

= 2.1.2 - 2021-04-29 =
- Update Youtube video and documentation link in the welcome page.

= 2.1.1 - 2021-04-23 =
- Fix supports = false when no supports features are selected
- Fix custom capabilities when set plural and singular names are similar

= 2.1.0 - 2021-04-13 =
- Add tooltips to all settings
- Show slug in lists of post types and taxonomies
- Show error and disable publish button if slug is one of WordPress reserved terms
- Set full custom capabilities for post types and add guidance message for custom capabilities
- Add missing labels for posts table headings

= 2.0.11 - 2021-04-08 =
- Fix menu position not working

= 2.0.10 - 2021-03-13 =
- Fix checkbox cannot be checked
- Fix default value for menu_position

= 2.0.9 - 2021-03-06 =
- Fix number in plural name.

= 2.0.8 - 2021-01-25 =
- Increase priority to register post type to make the submenu Post types & Taxonomies appear first in the main Meta Box menu

= 2.0.7 - 2020-12-11 =
- Fix missing options for show_in_menu and menu_position
- Fix label reverts to English
- Fix icon styling in WordPress 5.6
- Make JS text translatable after bundling with Webpack

= 2.0.6 - 2020-11-05 =
- Fix menu icon not showing
- Improve the code using React context properly

= 2.0.5 - 2020-11-04 =
- Fix supports tab not rendering

= 2.0.4 - 2020-11-02 =
- Fix [object Object] when register a custom taxonomy.

= 2.0.3 - 2020-10-07 =
- Fix migrating data

= 2.0.2 - 2020-10-05 =
- Fix encoding characters. For users who already upgraded, please add `?mbcpt-force=1` to your website URL to fix the problem.
- Fix empty page when clicking Taxonomies/Get PHP Code tab

= 2.0.1 - 2020-10-01 =
- Fix menu position not working

= 2.0.0 - 2020-09-30 =
- Rewrite the UI with React.
- Update PHP code to use Composer.

= 1.9.5 - 2020-07-28 =
- Use WordPress's built-in clipboard script

= 1.9.3 - 2020-04-16 =
- Fix notice: Undefined index `meta_box_cb`
- Fix warning for `supports` parameter in WordPress 5.3
- Add filter for advanced settings manipulation
- Improve toggle buttons

= 1.9.2 - 2019-11-28 =
- Fix warning for 'supports' parameter in WordPress 5.3.

= 1.9.1 - 2019-09-06 =
- Fix menu icon not working

= 1.9.0 - 2019-08-29 =
- Add support for custom archive slug
- Fix style in dashboard
- Do not show upgrade message for premium users

= 1.8.6 - 2019-07-17 =
- Hide the meta box for taxonomy if set `meta_box_cb` = false in Gutenberg.

= 1.8.5 - 2019-06-27 =
- Fix quotes in plural and singular names not working.

= 1.8.4 - 2019-06-01 =
- Update the page layout to make it more friendly.
- Enabled REST API by default for taxonomies to make they work with Gutenberg.
- Make the plugin safe to include in Meta Box AIO.

= 1.8.3 - 2019-03-21 =
- Set `'map_meta_cap' => true` for custom capabilities

= 1.8.2 - 2019-03-06 =
- Enabled REST API by default for post types to make they work with Gutenberg.
- Added "custom" for capability type, allowing developers to set custom capabilities.
- Auto truncated post type slug to 20 characters.

= 1.8.1 - 2018-12-10 =
- Fix typos and reformat code.

= 1.8.0 - 2018-06-08 =

- Used the shared menu from Meta Box to keep the admin menu clean.
- Added tabs to the About page
- Remove redirection after activation.

= 1.7.0 - 2018-06-02 =
- Added support for move the custom post type menu to a sub-menu of an existing top-level menu.
- Updated some text strings for better description
- Fixed button "Advanced" not working
- Fixed REST API base not a text input for taxonomy

= 1.6.0 - 2018-05-28 =
- Changed menu position from a text field to a select field, so users just select the position they want without knowing the position number.
- Hide the ads for premium users. You need to enter correct license key to hide it.

= 1.5.0 =
- Added support for rewrite options for taxonomies.
- Added "Copy to Clipboard" for generated code.

= 1.4.3 =
- Fixed undefined index when registering a new taxonomy.

= 1.4.2 =
- Fixed error in generated code for taxonomy.
- Fixed translation and logo URL.

= 1.4.1 =
- Added "About" page to help new users use the plugin.

= 1.4 =
- Added export to PHP code, so you can just copy and paste into your theme or plugin.
- Added option to enable/disable "Custom Fields" meta box.
- Fixed "menu_position" doesn't work.

= 1.3.1 =
- Added option to hide taxonomy meta box in the edit page.

= 1.3 =
- Added support for showing post types/taxonomies in REST API

= 1.2.5 =
- Custom post types not shown when edit custom taxonomy.

= 1.2.4 =
- Removed undefined index notice.

= 1.2.3 =
- Added new options for custom rewrite slug and with_front.

= 1.2.2 =
- Custom taxonomies now can be added to 'post', 'page', 'attachment'

= 1.2.1 =
- Unable to assign only category or tags to custom post type.

= 1.2.0 =
- Allow custom post types to have default taxonomies: category and tags.

= 1.1.0 =
- Integrates with [MB Custom Taxonomy](https://wordpress.org/plugins/mb-custom-taxonomy/) to create and manage custom taxonomies with similar interface.

= 1.0.2 =
- Added custom code to load plugin dependency for smaller footprint.

= 1.0 =
- First version.

== Upgrade Notice ==
