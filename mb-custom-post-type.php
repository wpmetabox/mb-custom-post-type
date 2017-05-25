<?php
/**
 * Plugin Name: MB Custom Post Type
 * Plugin URI: https://metabox.io/plugins/custom-post-type/
 * Description: Create custom post types and custom taxonomies with easy-to-use UI
 * Version: 1.4
 * Author: MetaBox.io
 * Author URI: https://metabox.io
 * License: GPL-2.0+
 * Text Domain: mb-custom-post-type
 *
 * @package Meta Box
 * @subpackage MB Custom Post Type
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || exit;

add_action( 'init', 'mb_cpt_load', 0 );

/**
 * Dependent plugin activation/deactivation
 *
 * @link https://gist.github.com/mathetos/7161f6a88108aaede32a
 */
function mb_cpt_load() {
	// If Meta Box is NOT active.
	if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'RW_Meta_Box' ) ) {
		add_action( 'admin_init', 'mb_cpt_deactivate' );
		add_action( 'admin_notices', 'mb_cpt_admin_notice' );

		return;
	}

	// Plugin constants.
	define( 'MB_CPT_URL', plugin_dir_url( __FILE__ ) );

	load_plugin_textdomain( 'mb-custom-post-type' );

	require plugin_dir_path( __FILE__ ) . 'inc/base/register.php';
	require plugin_dir_path( __FILE__ ) . 'inc/post-type/register.php';
	require plugin_dir_path( __FILE__ ) . 'inc/taxonomy/register.php';

	$cpt_register = new MB_CPT_Post_Type_Register;
	$tax_register = new MB_CPT_Taxonomy_Register;

	if ( is_admin() ) {
		require plugin_dir_path( __FILE__ ) . 'inc/helper.php';
		require plugin_dir_path( __FILE__ ) . 'inc/base/edit.php';
		require plugin_dir_path( __FILE__ ) . 'inc/post-type/edit.php';
		require plugin_dir_path( __FILE__ ) . 'inc/taxonomy/edit.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/interfaces/encoder.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/encoders/post-type-encoder.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/encoders/taxonomy-encoder.php';

		$post_type_encoder = new MB_CPT_Post_Type_Encoder();
		new MB_CPT_Post_Type_Edit( 'mb-post-type', $cpt_register, $post_type_encoder );

		$tax_encoder = new MB_CPT_Taxonomy_Encoder();
		new MB_CPT_Taxonomy_Edit( 'mb-taxonomy', $tax_register, $tax_encoder );
	}
}

/**
 * Deactivate plugin
 */
function mb_cpt_deactivate() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Show admin notice when Meta Box is not activated
 */
function mb_cpt_admin_notice() {
	$child  = __( 'MB Custom Post Type', 'mb-custom-post-type' );
	$parent = __( 'Meta Box', 'mb-custom-post-type' );
	printf(
		// translators: %1$s is the plugin name, %2$s is the Meta Box plugin name.
		'<div class="error"><p>' . esc_html__( '%1$s requires %2$s to function correctly. Please activate %2$s before activating %1$s. For now, the plug-in has been deactivated.', 'mb-custom-post-type' ) . '</p></div>',
		'<strong>' . esc_html( $child ) . '</strong>',
		'<strong>' . esc_html( $parent ) . '</strong>'
	);

	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
}
