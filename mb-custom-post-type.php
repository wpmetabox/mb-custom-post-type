<?php
/**
 * Plugin Name: MB Custom Post Types & Custom Taxonomies
 * Plugin URI:  https://metabox.io/plugins/custom-post-type/
 * Description: Create custom post types and custom taxonomies with easy-to-use UI
 * Version:     2.3.3
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL-2.0+
 * Text Domain: mb-custom-post-type
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || die;

if ( ! function_exists( 'mb_cpt_load' ) ) {
	if ( file_exists( __DIR__ . '/vendor' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	}

	define( 'MB_CPT_VER', '2.3.3' );
	define( 'MB_CPT_URL', plugin_dir_url( __FILE__ ) );

	add_action( 'init', 'mb_cpt_load', 0 );

	function mb_cpt_load() {
		load_plugin_textdomain( 'mb-custom-post-type' );

		new MBCPT\PostTypeRegister;
		new MBCPT\TaxonomyRegister;

		if ( ! is_admin() ) {
			return;
		}

		// Show Meta Box admin menu.
		add_filter( 'rwmb_admin_menu', '__return_true' );

		new MBCPT\Edit( 'mb-post-type' );
		new MBCPT\Edit( 'mb-taxonomy' );
		new MBCPT\About;
		new MBCPT\Warning;
		if ( defined( 'CPTUI_VERSION' ) ) {
			new MBCPT\Migration;
			new MBCPT\Ajax;
		}
	}
}
