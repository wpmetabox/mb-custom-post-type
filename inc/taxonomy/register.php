<?php
use WP_Post as WP_Post;

class MB_CPT_Taxonomy_Register extends MB_CPT_Base_Register {
	/**
	 * Initializing.
	 */
	public function __construct() {
		parent::__construct();

		add_filter( 'rest_prepare_taxonomy', array( $this, 'hide_taxonomy_meta_box' ), 10, 3 );
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

	/**
	 * Register custom post type for taxonomies
	 */
	public function register_post_types() {
		// Register post type of the plugin 'mb-taxonomy'.
		$labels = [
			'name'               => _x( 'Taxonomies', 'Taxonomy General Name', 'mb-custom-post-type' ),
			'singular_name'      => _x( 'Taxonomy', 'Taxonomy Singular Name', 'mb-custom-post-type' ),
			'menu_name'          => __( 'Taxonomies', 'mb-custom-post-type' ),
			'name_admin_bar'     => __( 'Taxonomy', 'mb-custom-post-type' ),
			'parent_item_colon'  => __( 'Parent Taxonomy:', 'mb-custom-post-type' ),
			'all_items'          => __( 'Taxonomies', 'mb-custom-post-type' ),
			'add_new_item'       => __( 'Add New Taxonomy', 'mb-custom-post-type' ),
			'add_new'            => __( 'Add New', 'mb-custom-post-type' ),
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
			'show_in_menu' => 'meta-box',
			'menu_icon'    => 'dashicons-exerpt-view',
			'can_export'   => true,
			'rewrite'      => false,
			'query_var'    => false,
		];
		register_post_type( 'mb-taxonomy', $args );

		// Get all registered custom taxonomies.
		$taxonomies = $this->get_taxonomies();
		foreach ( $taxonomies as $taxonomy => $tax_args ) {
			if ( isset( $tax_args['meta_box_cb'] ) && false !== $tax_args['meta_box_cb'] ) {
				unset( $tax_args['meta_box_cb'] );
			}
			register_taxonomy( $taxonomy, isset( $tax_args['post_types'] ) ? $tax_args['post_types'] : null, $tax_args );
		}
	}

	/**
	 * Get all registered taxonomies
	 *
	 * @return array
	 */
	public function get_taxonomies() {
		$taxonomies = [];

		$posts = get_posts( [
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'post_type'      => 'mb-taxonomy',
			'no_found_rows'  => true,
		] );

		foreach ( $posts as $post ) {
			$data = $this->get_taxonomy_data( $post );
			$taxonomies[ mb_cpt_get_prop( $data, 'slug' ) ] = $this->set_up_taxonomy( $data );
		}

		return $taxonomies;
	}

	public function get_taxonomy_data( WP_Post $post ) {
		$this->migrate_data( $post );

		return json_decode( $post->post_content );
	}

	/**
	 * Get new taxonomy data from mb custom taxonomy id.
	 *
	 * @param  int $mb_cpt_id MB custom taxonomy id.
	 * @return array          Array contains label and args of new taxonomy.
	 */
	public function migrate_data( WP_Post $post ) {
		if ( ! empty( $post->post_content ) ) {
			return;
		}

		$args   = [];
		$post_meta = get_post_meta( $post->ID );

		foreach ( $post_meta as $key => $value ) {
			$value = 1 === count( $value ) && ! in_array( $key, [ 'args_taxonomies', 'args_supports' ], true ) ? $value[0] : $value;

			if ( ! in_array( $key, [ 'args_menu_position' ] ) ) {
				$value = is_numeric( $value ) ? ( 1 === intval( $value ) ? true : false ) : $value;
			} else {
				$value = intval( $value );
			}

			$key = str_replace( 'args_', '', $key );
			$args[ $key ] = $value;

			if ( strpos( $key, 'label_' ) ) {
				$key = str_replace( 'label_', '', $key );
				$args[ 'labels' ][] = $value;
			}

			// delete_post_meta( $post->ID, $key );
		}

		$args['slug'] = $args['taxonomy'];
		unset( $args['taxonomy'] );

		$args['function_name'] = empty( $args['function_name'] ) ? 'your_function_name' : $args['function_name'];
		$args['text_domain'] = empty( $args['text_domain'] ) ? 'text-domain' : $args['text_domain'];

		wp_update_post( [
			'ID'           => $post->ID,
			'post_content' => wp_json_encode( $args ),
		] );
	}

	public function set_up_taxonomy( $data ) {
		$labels = [
			'name'                       => mb_cpt_get_prop( $data, 'labels', 'name' ),
			'singular_name'              => mb_cpt_get_prop( $data, 'labels', 'singular_name' ),
			'search_items'               => mb_cpt_get_prop( $data, 'labels', 'search_items' ),
			'popular_items'              => mb_cpt_get_prop( $data, 'labels', 'popular_items' ),
			'all_items'                  => mb_cpt_get_prop( $data, 'labels', 'all_items' ),
			'parent_item'                => mb_cpt_get_prop( $data, 'labels', 'parent_item' ),
			'parent_item_colon'          => mb_cpt_get_prop( $data, 'labels', 'parent_item_colon' ),
			'edit_item'                  => mb_cpt_get_prop( $data, 'labels', 'edit_item' ),
			'view_item'                  => mb_cpt_get_prop( $data, 'labels', 'view_item' ),
			'update_item'                => mb_cpt_get_prop( $data, 'labels', 'update_item' ),
			'add_new_item'               => mb_cpt_get_prop( $data, 'labels', 'add_new_item' ),
			'new_item_name'              => mb_cpt_get_prop( $data, 'labels', 'new_item_name' ),
			'separate_items_with_commas' => mb_cpt_get_prop( $data, 'labels', 'separate_items_with_commas' ),
			'add_or_remove_items'        => mb_cpt_get_prop( $data, 'labels', 'add_or_remove_items' ),
			'choose_from_most_used'      => mb_cpt_get_prop( $data, 'labels', 'choose_from_most_used' ),
			'not_found'                  => mb_cpt_get_prop( $data, 'labels', 'not_found' ),
		];
		$args   = [ 'labels' => $labels ];

		$params = [
			'description',
			'public',
			'publicly_queryable',
			'hierarchical',
			'show_ui',
			'show_in_menu',
			'show_in_nav_menus',
			'show_in_rest',
			'rest_base',
			'show_tagcloud',
			'show_in_quick_edit',
			'show_admin_column',
			'query_var',
		];

		foreach ( $params as $param ) {
			if ( property_exists( $data, $param ) ) {
				$args[ $param ] = $data->$param;
			}
		}

		if ( ! property_exists( $data, 'rewrite_no_front' ) ) {
			$args['rewrite'] = true;
		} else {
			$rewrite = [];
			if ( property_exists( $data, 'rewrite_slug' ) ) {
				$rewrite['slug'] = $data->rewrite_slug;
			}
			if ( $data->rewrite_no_front ) {
				$rewrite['with_front'] = false;
			}
			if ( property_exists( $data, 'rewrite_hierarchical' ) ) {
				$rewrite['hierarchical'] = true;
			}
			$args['rewrite'] = $rewrite;
			unset( $args['rewrite_slug'] );
			unset( $args['rewrite_no_front'] );
			unset( $args['rewrite_hierarchical'] );
		}

		$options    = [];
		$post_types = array_keys( mb_cpt_get_post_types() );
		foreach ( $post_types as $post_type ) {
			if ( property_exists( $data, $post_type ) && $data->$post_type ) {
				array_push( $options, $post_type );
			}
		}

		$args['post_types'] = $options;

		unset( $args['taxonomy'] );
		return $args;
	}

	/**
	 * Custom post updated messages
	 *
	 * @param array $messages Post messages.
	 *
	 * @return array
	 */
	public function updated_message( $messages ) {
		$post     = get_post();
		$revision = filter_input( INPUT_GET, 'revision', FILTER_SANITIZE_NUMBER_INT );

		$messages['mb-taxonomy'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Taxonomy updated.', 'mb-custom-taxonomy' ),
			2  => __( 'Custom field updated.', 'mb-custom-taxonomy' ),
			3  => __( 'Custom field deleted.', 'mb-custom-taxonomy' ),
			4  => __( 'Taxonomy updated.', 'mb-custom-taxonomy' ),
			// translators: %s: Date and time of the revision.
			5  => $revision ? sprintf( __( 'Taxonomy restored to revision from %s.', 'mb-custom-taxonomy' ), wp_post_revision_title( $revision, false ) ) : false,
			6  => __( 'Taxonomy published.', 'mb-custom-taxonomy' ),
			7  => __( 'Taxonomy saved.', 'mb-custom-taxonomy' ),
			8  => __( 'Taxonomy submitted.', 'mb-custom-taxonomy' ),
			// translators: %s: Date and time of the revision.
			9  => sprintf( __( 'Taxonomy scheduled for: <strong>%s</strong>.', 'mb-custom-taxonomy' ), date_i18n( __( 'M j, Y @ G:i', 'mb-custom-taxonomy' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Taxonomy draft updated.', 'mb-custom-taxonomy' ),
		);

		return $messages;
	}

	/**
	 * Custom post management WordPress messages
	 *
	 * @param array $bulk_messages Post bulk messages.
	 * @param array $bulk_counts   Number of posts.
	 *
	 * @return array
	 */
	public function bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages['mb-taxonomy'] = array(
			// translators: %s: Name of the taxonomy in singular and plural form.
			'updated'   => sprintf( _n( '%s taxonomy updated.', '%s taxonomies updated.', $bulk_counts['updated'], 'mb-custom-taxonomy' ), $bulk_counts['updated'] ),
			// translators: %s: Name of the taxonomy in singular and plural form.
			'locked'    => sprintf( _n( '%s taxonomy not updated, somebody is editing.', '%s taxonomies not updated, somebody is editing.', $bulk_counts['locked'], 'mb-custom-taxonomy' ), $bulk_counts['locked'] ),
			// translators: %s: Name of the taxonomy in singular and plural form.
			'deleted'   => sprintf( _n( '%s taxonomy permanently deleted.', '%s taxonomies permanently deleted.', $bulk_counts['deleted'], 'mb-custom-taxonomy' ), $bulk_counts['deleted'] ),
			// translators: %s: Name of the taxonomy in singular and plural form.
			'trashed'   => sprintf( _n( '%s taxonomy moved to the Trash.', '%s taxonomies moved to the Trash.', $bulk_counts['trashed'], 'mb-custom-taxonomy' ), $bulk_counts['trashed'] ),
			// translators: %s: Name of the taxonomy in singular and plural form.
			'untrashed' => sprintf( _n( '%s taxonomy restored from the Trash.', '%s taxonomies restored from the Trash.', $bulk_counts['untrashed'], 'mb-custom-taxonomy' ), $bulk_counts['untrashed'] ),
		);

		return $bulk_messages;
	}
}
