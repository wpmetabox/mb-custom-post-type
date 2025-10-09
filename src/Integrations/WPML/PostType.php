<?php
namespace MBCPT\Integrations\WPML;

use WP_Post;

class PostType {
	private $keys = [];

	public function __construct() {
		$this->keys = [
			'name'                     => __( 'Label', 'mb-custom-post-type' ),
			'singular_name'            => __( 'Singular name', 'mb-custom-post-type' ),
			'add_new'                  => __( 'Add new', 'mb-custom-post-type' ),
			'add_new_item'             => __( 'Add new item', 'mb-custom-post-type' ),
			'edit_item'                => __( 'Edit item', 'mb-custom-post-type' ),
			'new_item'                 => __( 'New item', 'mb-custom-post-type' ),
			'view_item'                => __( 'View item', 'mb-custom-post-type' ),
			'view_items'               => __( 'View items', 'mb-custom-post-type' ),
			'search_items'             => __( 'Search items', 'mb-custom-post-type' ),
			'not_found'                => __( 'Not found', 'mb-custom-post-type' ),
			'not_found_in_trash'       => __( 'Not found in trash', 'mb-custom-post-type' ),
			'parent_item_colon'        => __( 'Parent item colon', 'mb-custom-post-type' ),
			'all_items'                => __( 'All items', 'mb-custom-post-type' ),
			'archives'                 => __( 'Archives', 'mb-custom-post-type' ),
			'attributes'               => __( 'Attributes', 'mb-custom-post-type' ),
			'insert_into_item'         => __( 'Insert into item', 'mb-custom-post-type' ),
			'uploaded_to_this_item'    => __( 'Uploaded to this item', 'mb-custom-post-type' ),
			'featured_image'           => __( 'Featured image', 'mb-custom-post-type' ),
			'set_featured_image'       => __( 'Set featured image', 'mb-custom-post-type' ),
			'remove_featured_image'    => __( 'Remove featured image', 'mb-custom-post-type' ),
			'use_featured_image'       => __( 'Use as featured image', 'mb-custom-post-type' ),
			'menu_name'                => __( 'Menu name', 'mb-custom-post-type' ),
			'filter_items_list'        => __( 'Filter items list', 'mb-custom-post-type' ),
			'filter_by_date'           => __( 'Filter by date', 'mb-custom-post-type' ),
			'items_list_navigation'    => __( 'Items list navigation', 'mb-custom-post-type' ),
			'items_list'               => __( 'Items list', 'mb-custom-post-type' ),
			'item_published'           => __( 'Item published', 'mb-custom-post-type' ),
			'item_published_privately' => __( 'Item published privately', 'mb-custom-post-type' ),
			'item_reverted_to_draft'   => __( 'Item reverted to draft', 'mb-custom-post-type' ),
			'item_scheduled'           => __( 'Item scheduled', 'mb-custom-post-type' ),
			'item_updated'             => __( 'Item updated', 'mb-custom-post-type' ),
			'name_admin_bar'           => __( 'Admin bar name', 'mb-custom-post-type' ),
		];

		add_action( 'save_post_mb-post-type', [ $this, 'register_package' ], 20, 2 );
		add_filter( 'mbcpt_post_type', [ $this, 'use_translations' ], 10, 2 );
		add_action( 'deleted_post_mb-post-type', [ $this, 'delete_package' ], 10, 2 );
	}

	public function register_package( int $post_id, WP_Post $post ): void {
		$settings = json_decode( $post->post_content, true );
		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return;
		}

		$package = $this->get_package( $post );

		do_action( 'wpml_start_string_package_registration', $package );

		$this->register_strings( $settings, $post, $package );

		do_action( 'wpml_delete_unused_package_strings', $package );
	}

	private function register_strings( array $settings, WP_Post $post, array $package ): void {
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
			$settings['description'] ?? '',
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
			'kind'      => 'Meta Box: Post Type',
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
