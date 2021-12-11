<?php
namespace MBCPT;
use MetaBox\Support\Arr;

class Ajax {
	public function __construct() {
		add_action( 'wp_ajax_mbcpt_migrate_post_types', [ $this, 'migrate_post_types' ] );
		add_action( 'wp_ajax_mbcpt_migrate_taxonomies', [ $this, 'migrate_taxonomies' ] );
		add_action( 'wp_ajax_mbcpt_deactivate_plugin_cptui', [ $this, 'deactivate_plugin_cptui' ] );
	}

	public function migrate_post_types() {
		if ( session_status() !== PHP_SESSION_ACTIVE ) {
			session_start();
		}
		$data_cptui = get_option( 'cptui_post_types' );
		if ( empty( $data_cptui ) ) {
			wp_send_json_error();
		}
		foreach ( array_reverse( $data_cptui ) as $value ) {
			$plural                 = Arr::get( $value, 'label' );
			$singular               = Arr::get( $value, 'singular_label' );
			$value['slug']          = Arr::get( $value, 'name' );
			$value['menu_position'] = (int) Arr::get( $value, 'menu_position' ) ?: '';
			$value['archive_slug']  = Arr::get( $value, 'has_archive_string' );
			$menu_icon              = Arr::get( $value, 'menu_icon' );
			if ( 0 === strpos( $menu_icon, 'http' ) ) {
				$value['icon_type']   = 'custom';
				$value['icon_custom'] = $menu_icon;
			} else {
				$value['icon_type'] = 'dashicons';
				$value['icon']      = $menu_icon ?: 'dashicons-admin-post';
			}
			if ( $value['show_in_menu'] === 'true' ) {
				$value['show_in_menu'] = Arr::get( $value, 'show_in_menu_string' ) ?: 'true';
			}
			$value['rewrite'] = [
				'slug'       => Arr::get( $value, 'rewrite_slug' ),
				'with_front' => Arr::get( $value, 'rewrite_withfront' ),
			];
			unset( $value['rewrite_slug'] );
			unset( $value['rewrite_withfront'] );
			unset( $value['name'] );
			unset( $value['menu_icon'] );
			unset( $value['show_in_menu_string'] );
			$array = [
				'singular_name'            => $singular,
				'name'                     => $plural,
				'menu_name'                => Arr::get( $value, 'labels.menu_name', $plural ) ?: $plural,
				'all_items'                => Arr::get( $value, 'labels.all_items', 'All '.$plural ) ?: 'All '.$plural,
				'view_items'               => Arr::get( $value, 'labels.view_items', 'View '.$plural ) ?: 'View '.$plural,
				'search_items'             => Arr::get( $value, 'labels.search_items', 'Search '.$plural ) ?: 'Search '.$plural,
				'not_found'                => Arr::get( $value, 'labels.not_found', 'No '.$plural.' found' ) ?: 'No '.$plural.' found',
				'not_found_in_trash'       => Arr::get( $value, 'labels.not_found_in_trash', 'No '.$plural.' found in trash' ) ?: 'No '.$plural.' found in trash',
				'add_new_item'             => Arr::get( $value, 'labels.add_new_item', 'All new '.$singular ) ?: 'All new '.$singular,
				'edit_item'                => Arr::get( $value, 'labels.edit_item', 'Edit '.$singular ) ?: 'Edit '.$singular,
				'new_item'                 => Arr::get( $value, 'labels.new_item', 'New '.$singular ) ?: 'New '.$singular,
				'view_item'                => Arr::get( $value, 'labels.view_item', 'View '.$singular ) ?: 'View '.$singular,
				'add_new'                  => Arr::get( $value, 'labels.add_new', 'Add new' ) ?: 'Add new',
				'parent_item_colon'        => Arr::get( $value, 'labels.parent_item_colon', 'Parent '.$singular ) ?: 'Parent '.$singular,
				'featured_image'           => Arr::get( $value, 'labels.featured_image', 'Featured image' ) ?: 'Featured image',
				'set_featured_image'       => Arr::get( $value, 'labels.set_featured_image', 'Set featured image' ) ?: 'Set featured image',
				'remove_featured_image'    => Arr::get( $value, 'labels.remove_featured_image', 'Remove featured image' ) ?: 'Remove featured image',
				'use_featured_image'       => Arr::get( $value, 'labels.use_featured_image', 'Use as featured image' ) ?: 'Use as featured image',
				'archives'                 => Arr::get( $value, 'labels.archives', $singular.' archives' ) ?: $singular.' archives',
				'insert_into_item'         => Arr::get( $value, 'labels.insert_into_item', 'Insert into '.$singular ) ?: 'Insert into '.$singular,
				'uploaded_to_this_item'    => Arr::get( $value, 'labels.uploaded_to_this_item', 'Uploaded to this '.$singular ) ?: 'Uploaded to this '.$singular,
				'filter_items_list'        => Arr::get( $value, 'labels.filter_items_list', 'Filter '.$plural.' list' ) ?: 'Filter '.$plural.' list',
				'items_list_navigation'    => Arr::get( $value, 'labels.items_list_navigation', $plural.' list navigation' ) ?: $plural.' list navigation',
				'items_list'               => Arr::get( $value, 'labels.items_list', $plural.' list' ) ?: $plural.' list',
				'attributes'               => Arr::get( $value, 'labels.attributes', $plural.' attributes' ) ?: $plural.' attributes',
				'item_published'           => Arr::get( $value, 'labels.item_published', $singular.' published' ) ?: $singular.' published',
				'item_published_privately' => Arr::get( $value, 'labels.item_published_privately', $singular.' published privately' ) ?: $singular.' published privately',
				'item_reverted_to_draft'   => Arr::get( $value, 'labels.item_published_privately', $singular.' reverted to draft' ) ?: $singular.' reverted to draft',
				'item_scheduled'           => Arr::get( $value, 'labels.item_scheduled', $singular.' scheduled' ) ?: $singular.' scheduled',
				'item_updated'             => Arr::get( $value, 'labels.item_updated', $singular.' updated' ) ?: $singular.' updated',
			];
			$value['labels'] = array_merge( $value['labels'], $array );
			$content         = wp_json_encode( $value, JSON_UNESCAPED_UNICODE );
			$content         = str_replace( '"true"', 'true', $content );
			wp_insert_post([
				'post_content' => str_replace( '"false"', 'false', $content ),
				'post_type'    => 'mb-post-type',
				'post_title'   => $singular,
				'post_status'  => 'publish',
			]);
		}
		wp_send_json_success();
	}

	public function migrate_taxonomies() {
		if ( session_status() !== PHP_SESSION_ACTIVE ) {
			session_start();
		}
		$data_taxoui = get_option( 'cptui_taxonomies' );
		if ( empty( $data_taxoui ) ) {
			wp_send_json_error();
		}
		foreach ( $data_taxoui as $value ) {
			$plural         = Arr::get( $value, 'label' );
			$singular       = Arr::get( $value, 'singular_label' );
			$value['slug']  = Arr::get( $value, 'name' );
			$value['types'] = Arr::get( $value, 'object_types' );
			$value['rewrite'] = [
				'slug'       => Arr::get( $value, 'rewrite_slug' ),
				'with_front' => Arr::get( $value, 'rewrite_withfront' ),
			];
			unset( $value['rewrite_slug'] );
			unset( $value['rewrite_withfront'] );
			unset( $value['name'] );
			unset( $value['object_types'] );
			$array = [
				'singular_name'              => $singular,
				'name'                       => $plural,
				'menu_name'                  => Arr::get( $value, 'labels.menu_name', $plural ) ?: $plural,
				'search_items'               => Arr::get( $value, 'labels.search_items', 'Search '.$plural ) ?: 'Search '.$plural,
				'popular_items'              => Arr::get( $value, 'labels.popular_items', 'Popular '.$plural ) ?: 'Popular '.$plural,
				'all_items'                  => Arr::get( $value, 'labels.all_items', 'All '.$plural ) ?: 'All '.$plural,
				'view_item'                  => Arr::get( $value, 'labels.view_item', 'View '.$singular ) ?: 'View '.$singular,
				'parent_item'                => Arr::get( $value, 'labels.parent_item', 'Parent '.$singular ) ?: 'Parent '.$singular,
				'parent_item_colon'          => Arr::get( $value, 'labels.parent_item_colon', 'Parent '.$singular ) ?: 'Parent '.$singular,
				'edit_item'                  => Arr::get( $value, 'labels.edit_item', 'Edit '.$singular ) ?: 'Edit '.$singular,
				'update_item'                => Arr::get( $value, 'labels.update_item', 'Update '.$singular ) ?: 'Update '.$singular,
				'add_new_item'               => Arr::get( $value, 'labels.add_new_item', 'Add new '.$singular ) ?: 'Add new '.$singular,
				'new_item_name'              => Arr::get( $value, 'labels.new_item_name', 'New '.$singular.' name' ) ?: 'New '.$singular.' name',
				'filter_by_item'             => Arr::get( $value, 'labels.filter_by_item', 'Filter by '.$singular ) ?: 'Filter by '.$singular,
				'separate_items_with_commas' => Arr::get( $value, 'labels.separate_items_with_commas', 'Separate '.$plural.' with commas' ) ?: 'Separate '.$plural.' with commas',
				'add_or_remove_items'        => Arr::get( $value, 'labels.add_or_remove_items', 'Add or remove '.$plural ) ?: 'Add or remove '.$plural ,
				'choose_from_most_used'      => Arr::get( $value, 'labels.choose_from_most_used', 'Choose from the most used '.$plural ) ?: 'Choose from the most used '.$plural,
				'not_found'                  => Arr::get( $value, 'labels.not_found', 'Not '.$plural.' found' ) ?: 'Not '.$plural.' found',
				'no_terms'                   => Arr::get( $value, 'labels.no_terms', 'No '.$plural ) ?: 'No '.$plural,
				'items_list_navigation'      => Arr::get( $value, 'labels.items_list_navigation', $plural.' list navigation' ) ?: $plural.' list navigation',
				'items_list'                 => Arr::get( $value, 'labels.items_list', $plural.' list' ) ?: $plural.' list',
				'back_to_items'              => Arr::get( $value, 'labels.back_to_items', 'Back to '.$plural ) ?: 'Back to '.$plural,
			];
			$value['labels'] = array_merge( $value['labels'], $array );
			$content         = wp_json_encode( $value, JSON_UNESCAPED_UNICODE );
			$content         = str_replace( '"true"', 'true', $content );
			wp_insert_post([
				'post_content' => str_replace( '"false"', 'false', $content ),
				'post_type'    => 'mb-taxonomy',
				'post_title'   => $singular,
				'post_status'  => 'publish',
			]);
		}
		wp_send_json_success();
	}

	public function deactivate_plugin_cptui() {
		if ( session_status() !== PHP_SESSION_ACTIVE ) {
			session_start();
		}
		if ( is_plugin_active( 'custom-post-type-ui/custom-post-type-ui.php' ) ) {
			deactivate_plugins( 'custom-post-type-ui/custom-post-type-ui.php' );
		}
		wp_send_json_success();
	}

}
