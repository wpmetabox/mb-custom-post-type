<?php
/**
 * Plugin Name: MB Custom Post Type
 * Plugin URI: https://www.metabox.io/plugins/mb-custom-post-type
 * Description: Create custom post types with easy-to-use UI
 * Version: 1.0.0
 * Author: Rilwis & Duc Doan
 * Author URI: https://metabox.io
 * License: GPL-2.0+
 * Text Domain: mb-custom-post-type
 * Domain Path: /lang/
 */

// Prevent loading this file directly
if ( ! defined( 'ABSPATH' ) )
{
	exit;
}

// ----------------------------------------------------------
// Define plugin URL for loading static files or doing AJAX
// ------------------------------------------------------------
define( 'MB_CPT_URL', plugin_dir_url( __FILE__ ) );
define( 'MB_CPT_CSS_URL', trailingslashit( MB_CPT_URL . 'css' ) );
define( 'MB_CPT_JS_URL', trailingslashit( MB_CPT_URL . 'js' ) );

// ------------------------------------------------------------
// Plugin paths, for including files
// ------------------------------------------------------------
define( 'MB_CPT_DIR', plugin_dir_path( __FILE__ ) );
define( 'MB_CPT_INC_DIR', trailingslashit( MB_CPT_DIR . 'inc' ) );

if ( is_admin() )
{
	require_once MB_CPT_INC_DIR . 'class-mb-cpt-register.php';
	require_once MB_CPT_INC_DIR . 'class-mb-cpt-edit.php';
	require_once MB_CPT_INC_DIR . 'helper.php';
	new MB_CPT_Register;
	new MB_CPT_Edit;
}
