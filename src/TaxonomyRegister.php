<?php
namespace MBCPT;

use WP_Post;

class TaxonomyRegister extends Register {
	public function __construct() {
		parent::__construct();

		add_filter( 'rest_prepare_taxonomy', [ $this, 'hide_taxonomy_meta_box' ], 10, 3 );
	}

	/**
	 * Hide the meta box for taxonomy if set 'meta_box_cb' = false in Gutenberg.
	 *
	 * @param  object $response REST response object.
	 * @param  object $taxonomy Taxonomy object.
	 * @param  object $request  REST request object.
	 *
	 * @return object
	 */
	public function hide_taxonomy_meta_box( $response, $taxonomy, $request ) {
		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';

		// Context is edit in the editor.
		if ( 'edit' === $context && false === $taxonomy->meta_box_cb ) {
			$data_response                          = $response->get_data();
			$data_response['visibility']['show_ui'] = false;
			$response->set_data( $data_response );
		}

		return $response;
	}

	public function register() {
		// Register post type of the plugin 'mb-taxonomy'.
		$labels = [
			'name'               => _x( 'Taxonomies', 'Taxonomy General Name', 'mb-custom-post-type' ),
			'singular_name'      => _x( 'Taxonomy', 'Taxonomy Singular Name', 'mb-custom-post-type' ),
			'menu_name'          => __( 'Taxonomies', 'mb-custom-post-type' ),
			'name_admin_bar'     => __( 'Taxonomy', 'mb-custom-post-type' ),
			'parent_item_colon'  => __( 'Parent Taxonomy:', 'mb-custom-post-type' ),
			'all_items'          => __( 'Taxonomies', 'mb-custom-post-type' ),
			'add_new_item'       => __( 'Add New Taxonomy', 'mb-custom-post-type' ),
			'add_new'            => __( 'New Taxonomy', 'mb-custom-post-type' ),
			'new_item'           => __( 'New Taxonomy', 'mb-custom-post-type' ),
			'edit_item'          => __( 'Edit Taxonomy', 'mb-custom-post-type' ),
			'update_item'        => __( 'Update Taxonomy', 'mb-custom-post-type' ),
			'view_item'          => __( 'View Taxonomy', 'mb-custom-post-type' ),
			'search_items'       => __( 'Search Taxonomy', 'mb-custom-post-type' ),
			'not_found'          => __( 'Not found', 'mb-custom-post-type' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'mb-custom-post-type' ),
		];
		$args   = [
			'label'        => __( 'Taxonomies', 'mb-custom-post-type' ),
			'labels'       => $labels,
			'supports'     => false,
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => defined( 'RWMB_VER' ) ? 'meta-box' : 'edit.php?post_type=mb-post-type',
			'menu_icon'    => 'dashicons-exerpt-view',
			'can_export'   => true,
			'rewrite'      => false,
			'query_var'    => false,
			'map_meta_cap' => true,
			'capabilities' => [
				// Meta capabilities.
				'edit_post'              => 'edit_mb_taxonomy',
				'read_post'              => 'read_mb_taxonomy',
				'delete_post'            => 'delete_mb_taxonomy',

				// Primitive capabilities used outside of map_meta_cap():
				'edit_posts'             => 'manage_options',
				'edit_others_posts'      => 'manage_options',
				'publish_posts'          => 'manage_options',
				'read_private_posts'     => 'manage_options',

				// Primitive capabilities used within map_meta_cap():
				'read'                   => 'read',
				'delete_posts'           => 'manage_options',
				'delete_private_posts'   => 'manage_options',
				'delete_published_posts' => 'manage_options',
				'delete_others_posts'    => 'manage_options',
				'edit_private_posts'     => 'manage_options',
				'edit_published_posts'   => 'manage_options',
				'create_posts'           => 'manage_options',
			],
		];

			register_post_type( 'mb-taxonomy', $args );

		// Get all registered custom taxonomies.
		$taxonomies = $this->get_taxonomies();
		foreach ( $taxonomies as $slug => $args ) {
			if ( isset( $args['meta_box_cb'] ) && false !== $args['meta_box_cb'] ) {
				unset( $args['meta_box_cb'] );
			}
			$types = empty( $args['types'] ) ? [] : $args['types'];
			register_taxonomy( $slug, $types, $args );
		}
	}

	public function get_taxonomies() {
		$taxonomies = [];

		$posts = get_posts( [
			'posts_per_page'         => -1,
			'post_status'            => 'publish',
			'post_type'              => 'mb-taxonomy',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		] );

		foreach ( $posts as $post ) {
			$data = $this->get_taxonomy_data( $post );
			if ( empty( $data ) ) {
				continue;
			}

			$taxonomies[ $data['slug'] ] = $data;
		}

		return $taxonomies;
	}

	public function get_taxonomy_data( WP_Post $post ) {
		// phpcs:ignore
		$settings = empty( $post->post_content ) || isset( $_GET['mbcpt-force'] ) ? $this->migrate_data( $post ) : json_decode( $post->post_content, true );

		$this->sanitize_labels( $settings );

		return $settings;
	}

	public function migrate_data( WP_Post $post ) {
		$args      = [ 'labels' => [] ];
		$post_meta = get_post_meta( $post->ID );

		foreach ( $post_meta as $key => $value ) {
			if ( 0 !== strpos( $key, 'label_' ) && 0 !== strpos( $key, 'args_' ) ) {
				continue;
			}
			$this->unarray( $value, $key );
			$this->normalize_checkbox( $value );

			if ( 0 === strpos( $key, 'label_' ) ) {
				$key                    = str_replace( 'label_', '', $key );
				$args['labels'][ $key ] = $value;
			} else {
				$key          = str_replace( 'args_', '', $key );
				$args[ $key ] = $value;
			}
		}
		$this->change_key( $args, 'taxonomy', 'slug' );
		$this->change_key( $args, 'post_types', 'types' );

		// Bypass new post types.
		if ( isset( $_GET['mbcpt-force'] ) && empty( $args['slug'] ) ) { // phpcs:ignore
			return json_decode( $post->post_content, true );
		}

		// Rewrite.
		$rewrite = [];
		if ( isset( $args['rewrite_slug'] ) ) {
			$rewrite['slug'] = $args['rewrite_slug'];
		}
		$rewrite['with_front']   = isset( $args['rewrite_no_front'] ) ? ! $args['rewrite_no_front'] : true;
		$rewrite['hierarchical'] = isset( $args['rewrite_hierarchical'] ) ? (bool) $args['rewrite_hierarchical'] : false;
		$args['rewrite']         = $rewrite;
		unset( $args['rewrite_slug'], $args['rewrite_no_front'], $args['rewrite_hierarchical'] );

		wp_update_post( [
			'ID'           => $post->ID,
			'post_content' => wp_json_encode( $args, JSON_UNESCAPED_UNICODE ),
		] );
		return $args;
	}

	public function updated_message( $messages ) {
		$post     = get_post();
		$revision = (int) filter_input( INPUT_GET, 'revision' );

		$add_fields_link = '';
		$settings        = json_decode( $post->post_content, true );
		if ( defined( 'MBB_VER' ) && is_array( $settings ) && ! empty( $settings['slug'] ) ) {
			$link            = sprintf( admin_url( 'post-new.php?post_type=meta-box&post_title=%s' ), get_the_title() . ' Fields' );
			$link            = add_query_arg( [
				'post_type'              => 'meta-box',
				// Translators: %s - taxonomy singular label.
				'post_title'             => sprintf( __( '%s Fields', 'mb-custom-post-type' ), $post->post_title ),
				'settings[object_type]'  => 'term',
				'settings[taxonomies][]' => $settings['slug'],
			], admin_url( 'post-new.php' ) );
			$add_fields_link = '<a href=' . esc_url( $link ) . '>' . __( 'Add custom fields to this taxonomy', 'mb-custom-post-type' ) . ' &rarr;</a>';
		}

		$messages['mb-taxonomy'] = [
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Taxonomy updated.', 'mb-custom-post-type' ),
			2  => __( 'Custom field updated.', 'mb-custom-post-type' ),
			3  => __( 'Custom field deleted.', 'mb-custom-post-type' ),
			4  => __( 'Taxonomy updated.', 'mb-custom-post-type' ),
			// Translators: %s - date and time of the revision.
			5  => $revision ? sprintf( __( 'Taxonomy restored to revision from %s.', 'mb-custom-post-type' ), wp_post_revision_title( $revision, false ) ) : false,
			// Translators: %s - add fields link.
			6  => sprintf( __( 'Taxonomy published. %s', 'mb-custom-post-type' ), $add_fields_link ),
			7  => __( 'Taxonomy saved.', 'mb-custom-post-type' ),
			8  => __( 'Taxonomy submitted.', 'mb-custom-post-type' ),
			// Translators: %s - date and time of the revision.
			9  => sprintf( __( 'Taxonomy scheduled for: <strong>%s</strong>.', 'mb-custom-post-type' ), date_i18n( __( 'M j, Y @ G:i', 'mb-custom-post-type' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Taxonomy draft updated.', 'mb-custom-post-type' ),
		];

		return $messages;
	}

	public function bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages['mb-taxonomy'] = [
			// Translators: %s - taxonomy label in singular and plural forms.
			'updated'   => sprintf( _n( '%s taxonomy updated.', '%s taxonomies updated.', $bulk_counts['updated'], 'mb-custom-post-type' ), $bulk_counts['updated'] ),
			// Translators: %s - taxonomy label in singular and plural forms.
			'locked'    => sprintf( _n( '%s taxonomy not updated, somebody is editing.', '%s taxonomies not updated, somebody is editing.', $bulk_counts['locked'], 'mb-custom-post-type' ), $bulk_counts['locked'] ),
			// Translators: %s - taxonomy label in singular and plural forms.
			'deleted'   => sprintf( _n( '%s taxonomy permanently deleted.', '%s taxonomies permanently deleted.', $bulk_counts['deleted'], 'mb-custom-post-type' ), $bulk_counts['deleted'] ),
			// Translators: %s - taxonomy label in singular and plural forms.
			'trashed'   => sprintf( _n( '%s taxonomy moved to the Trash.', '%s taxonomies moved to the Trash.', $bulk_counts['trashed'], 'mb-custom-post-type' ), $bulk_counts['trashed'] ),
			// Translators: %s - taxonomy label in singular and plural forms.
			'untrashed' => sprintf( _n( '%s taxonomy restored from the Trash.', '%s taxonomies restored from the Trash.', $bulk_counts['untrashed'], 'mb-custom-post-type' ), $bulk_counts['untrashed'] ),
		];

		return $bulk_messages;
	}
}
