<?php
/**
 * Plugin Name: MB Custom Post Type
 * Plugin URI: https://www.metabox.io/plugins/custom-post-type/
 * Description: Create custom post types with easy-to-use UI
 * Version: 1.0.1
 * Author: Rilwis & Duc Doan
 * Author URI: https://metabox.io
 * License: GPL-2.0+
 * Text Domain: mb-custom-post-type
 * Domain Path: /lang/
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

// Plugin constants
define( 'MB_CPT_URL', plugin_dir_url( __FILE__ ) );
define( 'MB_CPT_DIR', plugin_dir_path( __FILE__ ) );

require_once MB_CPT_DIR . 'inc/class-mb-cpt-register.php';
new MB_CPT_Register;

if ( is_admin() )
{
	require_once MB_CPT_DIR . 'inc/class-mb-cpt-edit.php';
	require_once MB_CPT_DIR . 'inc/helper.php';
	new MB_CPT_Edit;

	require_once MB_CPT_DIR . 'inc/required-plugin.php';
}

add_action( 'plugins_loaded', 'mb_cpt_load_textdomain' );

/**
 * Load plugin textdomain
 * @return void
 */
function mb_cpt_load_textdomain()
{
	load_plugin_textdomain( 'mb-custom-post-type' );
}
