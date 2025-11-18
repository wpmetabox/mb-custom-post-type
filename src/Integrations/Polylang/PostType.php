<?php
namespace MBCPT\Integrations\Polylang;

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

		add_filter( 'mbcpt_post_type', [ $this, 'register_strings' ], 10 );
		add_filter( 'mbcpt_post_type', [ $this, 'use_translations' ], 20 );
	}

	public function register_strings( array $settings ): array {
		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return $settings;
		}

		$context = $this->get_context( $settings );

		// Register labels.
		foreach ( $this->keys as $key => $label ) {
			if ( ! empty( $settings['labels'][ $key ] ) ) {
				pll_register_string( 'label_' . $key, $settings['labels'][ $key ], $context );
			}
		}

		// Register description.
		if ( ! empty( $settings['description'] ) ) {
			pll_register_string( 'description', $settings['description'], $context );
		}

		return $settings;
	}

	public function use_translations( array $settings ): array {
		// Translate labels.
		foreach ( $this->keys as $key => $label ) {
			if ( ! empty( $settings['labels'][ $key ] ) ) {
				$settings['labels'][ $key ] = pll__( $settings['labels'][ $key ] );
			}
		}

		// Translate description.
		if ( ! empty( $settings['description'] ) ) {
			$settings['description'] = pll__( $settings['description'] );
		}

		return $settings;
	}

	private function get_context( array $settings ): string {
		// translators: %s is the name of the post type.
		return sprintf( __( 'Meta Box Post Type: %s', 'mb-custom-post-type' ), $settings['labels']['name'] ?? '' );
	}
}
