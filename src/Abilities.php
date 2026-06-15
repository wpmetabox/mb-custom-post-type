<?php
namespace MBCPT;

use MBCPT\Abilities\PostTypeAbilities;
use MBCPT\Abilities\TaxonomyAbilities;

class Abilities {

	private $post_type_abilities;
	private $taxonomy_abilities;

	public function __construct() {
		if ( ! class_exists( 'WP_Ability' ) ) {
			return;
		}

		$this->post_type_abilities = new PostTypeAbilities();
		$this->taxonomy_abilities  = new TaxonomyAbilities();

		add_action( 'wp_abilities_api_categories_init', [ $this, 'register_category' ] );
		add_action( 'wp_abilities_api_init', [ $this, 'register_abilities' ] );
	}

	public function register_category(): void {
		if ( wp_has_ability_category( 'meta-box' ) ) {
			return;
		}

		wp_register_ability_category(
			'meta-box',
			[
				'label'       => __( 'Meta Box', 'mb-custom-post-type' ),
				'description' => __( 'Abilities for Meta Box data (post types, taxonomies, fields, etc.).', 'mb-custom-post-type' ),
			]
		);
	}

	public function register_abilities(): void {
		$this->register_post_type_abilities_for_enabled();
		$this->register_taxonomy_abilities_for_enabled();
	}

	private function register_post_type_abilities_for_enabled(): void {
		$posts = get_posts( [
			'posts_per_page'         => -1,
			'post_status'            => 'publish',
			'post_type'              => 'mb-post-type',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		] );

		foreach ( $posts as $post ) {
			$settings = json_decode( $post->post_content, true );

			if ( empty( $settings['slug'] ) || empty( $settings['abilities'] ) ) {
				continue;
			}

			$post_type = get_post_type_object( $settings['slug'] );
			if ( $post_type ) {
				$this->post_type_abilities->register( $settings['slug'], $post_type );
			}
		}
	}

	private function register_taxonomy_abilities_for_enabled(): void {
		$posts = get_posts( [
			'posts_per_page'         => -1,
			'post_status'            => 'publish',
			'post_type'              => 'mb-taxonomy',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		] );

		foreach ( $posts as $post ) {
			$settings = json_decode( $post->post_content, true );

			if ( empty( $settings['slug'] ) || empty( $settings['abilities'] ) ) {
				continue;
			}

			$taxonomy = get_taxonomy( $settings['slug'] );
			if ( $taxonomy ) {
				$this->taxonomy_abilities->register( $settings['slug'], $taxonomy );
			}
		}
	}
}
