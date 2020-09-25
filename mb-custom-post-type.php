<?php
/**
 * Plugin Name: MB Custom Post Type
 * Plugin URI:  https://metabox.io/plugins/custom-post-type/
 * Description: Create custom post types and custom taxonomies with easy-to-use UI
 * Version:     1.9.5
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL-2.0+
 * Text Domain: mb-custom-post-type
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || die;

if ( ! function_exists( 'mb_cpt_load' ) ) {
	define( 'MB_CPT_VER', '1.9.5' );
	define( 'MB_CPT_URL', plugin_dir_url( __FILE__ ) );

	add_action( 'init', 'mb_cpt_load', 0 );

	function mb_cpt_load() {
		load_plugin_textdomain( 'mb-custom-post-type' );

		require __DIR__ . '/inc/helper.php';
		require __DIR__ . '/inc/base/register.php';
		require __DIR__ . '/inc/post-type/register.php';
		require __DIR__ . '/inc/taxonomy/register.php';

		new MB_CPT_Post_Type_Register;
		new MB_CPT_Taxonomy_Register;

		if ( ! is_admin() ) {
			return;
		}

		// Show Meta Box admin menu.
		add_filter( 'rwmb_admin_menu', '__return_true' );

		require __DIR__ . '/inc/base/edit.php';
		require __DIR__ . '/inc/about/about.php';

		new MB_CPT_Base_Edit( 'mb-post-type' );
		new MB_CPT_Base_Edit( 'mb-taxonomy' );
		new MB_CPT_About_Page;
	}
}
