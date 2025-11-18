<?php
namespace MBCPT\Integrations\Polylang;

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

		add_filter( 'mbcpt_taxonomy', [ $this, 'register_strings' ], 10 );
		add_filter( 'mbcpt_taxonomy', [ $this, 'use_translations' ], 20 );
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
		// translators: %s is the name of the taxonomy.
		return sprintf( __( 'Meta Box Taxonomy: %s', 'mb-custom-post-type' ), $settings['labels']['name'] ?? '' );
	}
}
