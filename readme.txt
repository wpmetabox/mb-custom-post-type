=== MB Custom Post Type ===
Contributors: metabox, rilwis, duc88b, fitwp, truongwp
Donate link: https://metabox.io/pricing/
Tags: cms, custom, custom post types, custom post type, cpt, post, post types, post type, types
Requires at least: 4.3
Tested up to: 5.2.3
Stable tag: 1.9.1
License: GPLv2 or later

Create and manage custom post types and custom taxonomies with an easy-to-use interface in WordPress.

== Description ==

**MB Custom Post Type** helps you to create and manage custom post types and custom taxonomies easily in WordPress by providing an easy-to-use interface in the admin area.

The plugin allows you to handle all post type's arguments and taxonomy's arguments such as menu labels, admin bar label, exclude from search, disable archive page, etc. just in minutes. You don't need to write custom PHP code to register custom post types anymore (using function `register_post_type()` and `register_taxonomy()`).

Using **MB Custom Post Type**, you will be able to craft the WordPress admin and turn it into a professional Content Management Systems.

**Note:** Since version 1.1.0 MB Custom Post Type has integrated [MB Custom Taxonomy](https://wordpress.org/plugins/mb-custom-taxonomy/) to create and manage custom taxonomies with similar interface.

**Video demo**:

https://www.youtube.com/watch?v=9c4w5zdeTJI

### Features

* Supports all arguments for creating custom post types (like `register_post_type()`)
* Supports all arguments for creating custom taxonomies (like `register_taxonomy()`)
* Supports **live editing mode**, which auto fill in all necessary labels for you!
* **Export to PHP Code** (since 1.4)
* Clean code
* Registered custom post types can be exported/imported using default WordPress functionality (no more plugins!)

### Plugin Links

- [Project Page](https://metabox.io/plugins/custom-post-type/)
- [Github Repo](https://github.com/rilwis/mb-custom-post-type/)

This plugin is a free extension of [Meta Box](https://metabox.io) plugin, which is a powerful, professional solution to create custom meta boxes and custom fields for WordPress websites. Using **MB Custom Post Type** in combination with [other extensions](https://metabox.io/plugins/) will help you manage any content types in WordPress easily and make your website more professional.

== Installation ==

You need to install [Meta Box](https://metabox.io) plugin first

- Go to Plugins | Add New and search for Meta Box
- Click **Install Now** button to install the plugin
- After installing, click **Activate Plugin** to activate the plugin

Install **MB Custom Post Type** extension

- Go to **Plugins | Add New** and search for **MB Custom Post Type**
- Click **Install Now** button to install the plugin
- After installing, click **Activate Plugin** to activate the plugin

== Frequently Asked Questions ==

== Screenshots ==
1. All registered custom post types
2. Edit custom post type

== Changelog ==

= 1.9.1 - 2019-09-06 =

**Fixed**

- Fix menu icon not working

= 1.9.0 - 2019-08-29 =

**Added**

- Add support for custom archive slug

**Fixed**

- Fix style in dashboard
- Do not show upgrade message for premium users

= 1.8.6 - 2019-07-17 =

- Hide the meta box for taxonomy if set `meta_box_cb` = false in Gutenberg.

= 1.8.5 - 2019-06-27 =

**Fixed**

- Fix quotes in plural and singular names not working.

= 1.8.4 - 2019-06-01 =

**Changed**

- Update the page layout to make it more friendly.
- Enabled REST API by default for taxonomies to make they work with Gutenberg.
- Make the plugin safe to include in Meta Box AIO.


= 1.8.3 - 2019-03-21 =

**Added**

- Set `'map_meta_cap' => true` for custom capabilities

= 1.8.2 - 2019-03-06 =

**Changed**

- Enabled REST API by default for post types to make they work with Gutenberg.
- Added "custom" for capability type, allowing developers to set custom capabilities.
- Auto truncated post type slug to 20 characters.

= 1.8.1 - 2018-12-10 =

**Fixed**

- Fix typos and reformat code.

= 1.8.0 - 2018-06-08 =

**Improved**

- Used the shared menu from Meta Box to keep the admin menu clean.
- Added tabs to the About page
- Remove redirection after activation.

= 1.7.0 - 2018-06-02 =

**Improved**

- Added support for move the custom post type menu to a sub-menu of an existing top-level menu.
- Updated some text strings for better description

**Fixed**

- Fixed button "Advanced" not working
- Fixed REST API base not a text input for taxonomy

= 1.6.0 - 2018-05-28 =

**Improved**

- Changed menu position from a text field to a select field, so users just select the position they want without knowing the position number.
- Hide the ads for premium users. You need to enter correct license key to hide it.

= 1.5.0 =

**Added**

- Added support for rewrite options for taxonomies.
- Added "Copy to Clipboard" for generated code.

= 1.4.3 =

**Fixed**

- Fixed undefined index when registering a new taxonomy.

= 1.4.2 =

**Fixed**

- Fixed error in generated code for taxonomy.
- Fixed translation and logo URL.

= 1.4.1 =

**Added**

- Added "About" page to help new users use the plugin.

= 1.4 =

**Added**

- Added export to PHP code, so you can just copy and paste into your theme or plugin.
- Added option to enable/disable "Custom Fields" meta box.


**Fixed**

- Fixed "menu_position" doesn't work.

= 1.3.1 =

**Added**

- Added option to hide taxonomy meta box in the edit page.

= 1.3 =

**Added**

- Added support for showing post types/taxonomies in REST API

= 1.2.5 =

**Fixed**

- Custom post types not shown when edit custom taxonomy.

= 1.2.4 =

**Fixed**

- Removed undefined index notice.

= 1.2.3 =

**Added**

- Added new options for custom rewrite slug and with_front.

= 1.2.2 =

**Fixed**

- Custom taxonomies now can be added to 'post', 'page', 'attachment'

= 1.2.1 =

**Fixed**

- Unable to assign only category or tags to custom post type.

= 1.2.0 =

**Added**

- Allow custom post types to have default taxonomies: category and tags.

= 1.1.0 =

**Added**

- Integrates with [MB Custom Taxonomy](https://wordpress.org/plugins/mb-custom-taxonomy/) to create and manage custom taxonomies with similar interface.

= 1.0.2 =

**Added**

- Added custom code to load plugin dependency for smaller footprint.

= 1.0 =

- First version.

== Upgrade Notice ==
