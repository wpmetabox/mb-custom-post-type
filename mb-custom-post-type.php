<?php
/**
 * Plugin Name: MB Custom Post Type
 * Plugin URI: https://metabox.io/plugins/custom-post-type/
 * Description: Create custom post types and custom taxonomies with easy-to-use UI
 * Version: 1.2.1
 * Author: Rilwis & Duc Doan
 * Author URI: https://metabox.io
 * License: GPL-2.0+
 * Text Domain: mb-custom-post-type
 * Domain Path: /lang/
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

add_action( 'init', 'mb_cpt_load', 0 );

/**
 * Dependent plugin activation/deactivation
 * @link https://gist.github.com/mathetos/7161f6a88108aaede32a
 */
function mb_cpt_load()
{
	// If Meta Box is NOT active
	if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'RW_Meta_Box' ) )
	{
		add_action( 'admin_init', 'mb_cpt_deactivate' );
		add_action( 'admin_notices', 'mb_cpt_admin_notice' );
	}
	else
	{
		// Plugin constants
		define( 'MB_CPT_URL', plugin_dir_url( __FILE__ ) );

		mb_cpt_load_textdomain();

		require plugin_dir_path( __FILE__ ) . 'inc/base/register.php';
		require plugin_dir_path( __FILE__ ) . 'inc/post-type/register.php';
		require plugin_dir_path( __FILE__ ) . 'inc/taxonomy/register.php';
		new MB_CPT_Post_Type_Register;
		new MB_CPT_Taxonomy_Register;

		if ( is_admin() )
		{
			require plugin_dir_path( __FILE__ ) . 'inc/helper.php';
			require plugin_dir_path( __FILE__ ) . 'inc/base/edit.php';
			require plugin_dir_path( __FILE__ ) . 'inc/post-type/edit.php';
			require plugin_dir_path( __FILE__ ) . 'inc/taxonomy/edit.php';
			new MB_CPT_Post_Type_Edit( 'mb-post-type' );
			new MB_CPT_Taxonomy_Edit( 'mb-taxonomy' );
		}
	}
}

/**
 * Deactivate plugin
 */
function mb_cpt_deactivate()
{
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Show admin notice when Meta Box is not activated
 */
function mb_cpt_admin_notice()
{
	$child  = __( 'MB Custom Post Type', 'mb-custom-post-type' );
	$parent = __( 'Meta Box', 'mb-custom-post-type' );
	printf(
		'<div class="error"><p>' . esc_html__( '%1$s requires %2$s to function correctly. Please activate %2$s before activating %1$s. For now, the plug-in has been deactivated.', 'mb-custom-post-type' ) . '</p></div>',
		'<strong>' . $child . '</strong>',
		'<strong>' . $parent . '</strong>'
	);

	if ( isset( $_GET['activate'] ) )
	{
		unset( $_GET['activate'] );
	}
}

/**
 * Load plugin textdomain
 */
function mb_cpt_load_textdomain()
{
	load_plugin_textdomain( 'mb-custom-post-type' );
}
