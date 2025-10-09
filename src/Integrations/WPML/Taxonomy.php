<?php
namespace MBCPT\Integrations\WPML;

use WP_Post;

class Taxonomy {
	private $keys = [];

	public function __construct() {
		$this->keys = [
			'name'                       => __( 'General name for the taxonomy, usually plural', 'mb-custom-post-type' ),
			'singular_name'              => __( 'Name for one object of this taxonomy', 'mb-custom-post-type' ),
			'menu_name'                  => __( 'Menu name', 'mb-custom-post-type' ),
			'search_items'               => __( 'Search items', 'mb-custom-post-type' ),
			'popular_items'              => __( 'Popular items', 'mb-custom-post-type' ),
			'all_items'                  => __( 'All items', 'mb-custom-post-type' ),
			'parent_item'                => __( 'Parent item', 'mb-custom-post-type' ),
			'parent_item_colon'          => __( 'Parent item colon', 'mb-custom-post-type' ),
			'edit_item'                  => __( 'Edit item', 'mb-custom-post-type' ),
			'view_item'                  => __( 'View item', 'mb-custom-post-type' ),
			'update_item'                => __( 'Update item', 'mb-custom-post-type' ),
			'add_new_item'               => __( 'Add new item', 'mb-custom-post-type' ),
			'new_item_name'              => __( 'New item name', 'mb-custom-post-type' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'mb-custom-post-type' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'mb-custom-post-type' ),
			'choose_from_most_used'      => __( 'Choose from the most used items', 'mb-custom-post-type' ),
			'not_found'                  => __( 'No items found', 'mb-custom-post-type' ),
			'no_terms'                   => __( 'No items', 'mb-custom-post-type' ),
			'filter_by_item'             => __( 'Filter by item', 'mb-custom-post-type' ),
			'items_list_navigation'      => __( 'Items list navigation', 'mb-custom-post-type' ),
			'items_list'                 => __( 'Items list', 'mb-custom-post-type' ),
			'most_used'                  => __( 'Most used', 'mb-custom-post-type' ),
			'back_to_items'              => __( 'Back to items', 'mb-custom-post-type' ),
			'item_link'                  => __( 'Item link', 'mb-custom-post-type' ),
			'item_link_description'      => __( 'Item link description', 'mb-custom-post-type' ),
			'name_field_description'     => __( 'Name field description', 'mb-custom-post-type' ),
			'slug_field_description'     => __( 'Slug field description', 'mb-custom-post-type' ),
			'parent_field_description'   => __( 'Parent field description', 'mb-custom-post-type' ),
			'desc_field_description'     => __( 'Description field description', 'mb-custom-post-type' ),
		];

		add_action( 'save_post_mb-taxonomy', [ $this, 'register_package' ], 20, 2 );
		add_filter( 'mbcpt_taxonomy', [ $this, 'use_translations' ], 10, 2 );
		add_action( 'deleted_post_mb-taxonomy', [ $this, 'delete_package' ], 10, 2 );
	}

	public function register_package( int $post_id, WP_Post $post ): void {
		$settings = json_decode( $post->post_content, true );
		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return;
		}

		$package = $this->get_package( $post );

		do_action( 'wpml_start_string_package_registration', $package );

		$this->register_strings( $settings, $post );

		do_action( 'wpml_delete_unused_package_strings', $package );
	}

	private function register_strings( array $settings, WP_Post $post ): void {
		$package = $this->get_package( $post );

		// Register labels.
		foreach ( $this->keys as $key => $label ) {
			do_action(
				'wpml_register_string',
				$settings['labels'][ $key ] ?? '',
				'label_' . $key,
				$package,
				sprintf( '%s: %s', $post->post_title, $label ),
				'LINE'
			);
		}

		// Register description.
		do_action(
			'wpml_register_string',
			$settings['description'],
			'description',
			$package,
			sprintf( '%s: Description', $post->post_title ),
			'LINE'
		);
	}

	public function use_translations( array $settings, WP_Post $post ): array {
		$package = $this->get_package( $post );

		// Translate labels.
		foreach ( $this->keys as $key => $label ) {
			if ( ! empty( $settings['labels'][ $key ] ) ) {
				$settings['labels'][ $key ] = apply_filters( 'wpml_translate_string', $settings['labels'][ $key ], 'label_' . $key, $package );
			}
		}

		// Translate description.
		if ( ! empty( $settings['description'] ) ) {
			$settings['description'] = apply_filters( 'wpml_translate_string', $settings['description'], 'description', $package );
		}

		return $settings;
	}

	private function get_package( WP_Post $post ): array {
		return [
			'kind'      => 'Meta Box: Taxonomy',
			'name'      => urldecode( $post->post_name ),
			'title'     => $post->post_title,
			'edit_link' => get_edit_post_link( $post ),
		];
	}

	public function delete_package( int $post_id, WP_Post $post ) {
		$package = $this->get_package( $post );
		do_action( 'wpml_delete_package', $package['name'], $package['kind'] );
	}
}
