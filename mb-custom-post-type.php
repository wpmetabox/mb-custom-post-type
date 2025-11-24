<?php
/**
 * Plugin Name: MB Custom Post Types & Custom Taxonomies
 * Plugin URI:  https://metabox.io/plugins/custom-post-type/
 * Description: Create custom post types and custom taxonomies with easy-to-use UI
 * Version:     2.11.1
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL-2.0+
 * Text Domain: mb-custom-post-type
 *
 * Copyright (C) 2010-2025 Tran Ngoc Tuan Anh. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// Prevent loading this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( ! function_exists( 'mb_cpt_load' ) ) {
	if ( file_exists( __DIR__ . '/vendor' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	}

	add_action( 'init', 'mb_cpt_load', 0 );

	function mb_cpt_load() {
		define( 'MB_CPT_DIR', __DIR__ );
		define( 'MB_CPT_VER', '2.11.1' );

		if ( class_exists( 'RWMB_Loader' ) ) {
			list( , $url ) = RWMB_Loader::get_path( __DIR__ );
			define( 'MB_CPT_URL', $url );
		} else {
			define( 'MB_CPT_URL', plugin_dir_url( __FILE__ ) );
		}

		new MBCPT\Integrations\WPML\Manager();
		new MBCPT\Integrations\Polylang\Manager();

		new MBCPT\PostTypeRegister();
		new MBCPT\TaxonomyRegister();
		new MBCPT\PostTypeReorder();
		new MBCPT\TaxonomyReorder();

		if ( ! is_admin() ) {
			return;
		}

		// Create Meta Box menu if Meta Box is not installed.
		if ( ! defined( 'RWMB_VER' ) ) {
			new MBCPT\Menu();
		}

		// Show Meta Box admin menu.
		add_filter( 'rwmb_admin_menu', '__return_true' );

		new MBCPT\Edit( 'mb-post-type' );
		new MBCPT\Edit( 'mb-taxonomy' );
		new MBCPT\Warning();
		new MBCPT\Import();
		new MBCPT\Export();
		new MBCPT\PostListTable();
		new MBCPT\ToggleStatusColumn();

		if ( defined( 'CPTUI_VERSION' ) ) {
			new MBCPT\Migration();
			new MBCPT\Ajax();
		}
	}
}
