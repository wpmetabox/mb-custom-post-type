<?php
/**
 * Plugin Name: MB Custom Post Type
 * Plugin URI:  https://metabox.io/plugins/custom-post-type/
 * Description: Create custom post types and custom taxonomies with easy-to-use UI
 * Version:     1.9.1
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL-2.0+
 * Text Domain: mb-custom-post-type
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'mb_cpt_load' ) ) {
	// Plugin constants.
	define( 'MB_CPT_FILE', __FILE__ );
	define( 'MB_CPT_URL', plugin_dir_url( __FILE__ ) );

	add_action( 'init', 'mb_cpt_load', 0 );

	/**
	 * Dependent plugin activation/deactivation
	 *
	 * @link https://gist.github.com/mathetos/7161f6a88108aaede32a
	 */
	function mb_cpt_load() {
		// If Meta Box is NOT active.
		if ( current_user_can( 'activate_plugins' ) && ! defined( 'RWMB_VER' ) ) {
			add_action( 'admin_notices', 'mb_cpt_admin_notice' );

			return;
		}

		load_plugin_textdomain( 'mb-custom-post-type' );

		// Show Meta Box admin menu.
		add_filter( 'rwmb_admin_menu', '__return_true' );

		require dirname( __FILE__ ) . '/inc/base/register.php';
		require dirname( __FILE__ ) . '/inc/post-type/register.php';
		require dirname( __FILE__ ) . '/inc/taxonomy/register.php';

		$cpt_register = new MB_CPT_Post_Type_Register();
		$tax_register = new MB_CPT_Taxonomy_Register();

		if ( ! is_admin() ) {
			return;
		}

		require dirname( __FILE__ ) . '/inc/helper.php';
		require dirname( __FILE__ ) . '/inc/base/edit.php';
		require dirname( __FILE__ ) . '/inc/post-type/edit.php';
		require dirname( __FILE__ ) . '/inc/taxonomy/edit.php';
		require dirname( __FILE__ ) . '/inc/interfaces/encoder.php';
		require dirname( __FILE__ ) . '/inc/encoders/post-type-encoder.php';
		require dirname( __FILE__ ) . '/inc/encoders/taxonomy-encoder.php';
		require dirname( __FILE__ ) . '/inc/about/about.php';

		$cpt_encoder = new MB_CPT_Post_Type_Encoder();
		new MB_CPT_Post_Type_Edit( 'mb-post-type', $cpt_register, $cpt_encoder );

		$tax_encoder = new MB_CPT_Taxonomy_Encoder();
		new MB_CPT_Taxonomy_Edit( 'mb-taxonomy', $tax_register, $tax_encoder );

		$about_page = new MB_CPT_About_Page();
		$about_page->init();
	}

	/**
	 * Show admin notice when Meta Box is not activated
	 */
	function mb_cpt_admin_notice() {
		$plugins      = get_plugins();
		$is_installed = isset( $plugins['meta-box/meta-box.php'] );
		$install_url  = wp_nonce_url( admin_url( 'update.php?action=install-plugin&plugin=meta-box' ), 'install-plugin_meta-box' );
		$activate_url = wp_nonce_url( admin_url( 'plugins.php?action=activate&amp;plugin=meta-box/meta-box.php' ), 'activate-plugin_meta-box/meta-box.php' );
		$action_url   = $is_installed ? $activate_url : $install_url;
		$action       = $is_installed ? __( 'activate', 'mb-taxonomy' ) : __( 'install', 'mb-taxonomy' );

		$child  = __( 'MB Custom Post Type', 'mb-custom-post-type' );
		$parent = __( 'Meta Box', 'mb-custom-post-type' );
		printf(
			// Translators: %1$s is the plugin name, %2$s is the Meta Box plugin name.
			'<div class="error"><p>' . esc_html__( '%1$s requires %2$s to function correctly. %3$s to %4$s %2$s.', 'mb-custom-post-type' ) . '</p></div>',
			'<strong>' . esc_html( $child ) . '</strong>',
			'<strong>' . esc_html( $parent ) . '</strong>',
			'<a href="' . esc_url( $action_url ) . '">' . esc_html__( 'Click here', 'mb-custom-post-type' ) . '</a>',
			esc_html( $action )
		);

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}
