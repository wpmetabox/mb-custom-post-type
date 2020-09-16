<?php
/**
 * Controls all operations of MB Custom Taxonomy extension for registering custom taxonomy.
 *
 * @package    Meta Box
 * @subpackage MB Custom Taxonomy
 */

/**
 * Controls all operations for registering custom taxonomy.
 */
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
		$labels = array(
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
		);
		$args   = array(
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
		);
		register_post_type( 'mb-taxonomy', $args );

		// Get all registered custom taxonomies.
		$taxonomies = $this->get_taxonomies();
		foreach ( $taxonomies as $taxonomy => $args ) {
			if ( isset( $args['meta_box_cb'] ) && false !== $args['meta_box_cb'] ) {
				unset( $args['meta_box_cb'] );
			}
			register_taxonomy( $taxonomy, isset( $args['post_types'] ) ? $args['post_types'] : null, $args );
		}
	}

	/**
	 * Get all registered taxonomies
	 *
	 * @return array
	 */
	public function get_taxonomies() {
		// This array stores all registered custom taxonomies.
		$taxonomies = array();

		// Get all post where where post_type = mb-taxonomy.
		$taxonomy_ids = get_posts(
			array(
				'posts_per_page' => - 1,
				'post_status'    => 'publish',
				'post_type'      => 'mb-taxonomy',
				'no_found_rows'  => true,
				'fields'         => 'ids',
			)
		);

		foreach ( $taxonomy_ids as $taxonomy_id ) {
			list( $labels, $args ) = $this->get_taxonomy_data( $taxonomy_id );

			$taxonomies[ $args['taxonomy'] ] = $this->set_up_taxonomy( $labels, $args );
		}

		return $taxonomies;
	}

	/**
	 * Get new taxonomy data from mb custom taxonomy id.
	 *
	 * @param  int $mb_cpt_id MB custom taxonomy id.
	 * @return array          Array contains label and args of new taxonomy.
	 */
	public function get_taxonomy_data( $mb_cpt_id ) {
		if ( ! get_post( $mb_cpt_id )->post_content ) {
			$post_meta = get_post_meta( $mb_cpt_id );

			$labels = [];
			$args = [];

			foreach ( $post_meta as $key => $value ) {
				if ( false !== strpos( $key, 'label' ) ) {
					// If post meta has prefix 'label' then add it to $labels.
					// @codingStandardsIgnoreLine
					$data = 1 == count( $value ) ? $value[0] : $value;

					$labels[ str_replace( 'label_', '', $key ) ] = $data;
				} elseif ( false !== strpos( $key, 'args' ) ) {
					// If post meta has prefix 'args' then add it to $args.
					// @codingStandardsIgnoreLine
					$data = 1 == count( $value ) ? $value[0] : $value;
					$data = is_numeric( $data ) ? ( 1 === intval( $data ) ? true : false ) : $data;

					$args[ str_replace( 'args_', '', $key ) ] = $data;
				}
			}

			$post = [
				'ID'           => $mb_cpt_id,
				'post_content' => json_encode( [ $labels, $args ] ),
			];
	
			wp_update_post( $post );
		}

		return json_decode( get_post( $mb_cpt_id )->post_content );
	}

	/**
	 * Setup labels, arguments for a custom taxonomy
	 *
	 * @param array $labels Taxonomy labels.
	 * @param array $args   Taxonomy parameters.
	 *
	 * @return array
	 */
	public function set_up_taxonomy( $data ) {
		$labels = [
			'menu_name'                  => $data->name,
			'all_items'                  => $data->all_items,
			'edit_item'                  => $data->edit_item,
			'view_item'                  => $data->view_item,
			'update_item'                => $data->update_item,
			'add_new_item'               => $data->add_new_item,
			'new_item_name'              => $data->new_item_name,
			'parent_item'                => $data->parent_item,
			'parent_item_colon'          => $data->parent_item_colon,
			'search_items'               => $data->search_items,
			'popular_items'              => $data->popular_items,
			'separate_items_with_commas' => $data->separate_items_with_commas,
			'add_or_remove_items'        => $data->add_or_remove_items,
			'choose_from_most_used'      => $data->choose_from_most_used,
			'not_found'                  => $data->not_found,
		];
		$args   = [
			'label'  => $data->name,
			'labels' => $labels,
			'public' => true,
		];

		if ( ! $data->rewrite_slug && ! $data->rewrite_no_front ) {
			$args['rewrite'] = true;
		} else {
			$rewrite = [];
			if ( $data->rewrite_slug ) {
				$rewrite['slug'] = $data->rewrite_slug;
			}
			if ( $data->rewrite_no_front ) {
				$rewrite['with_front'] = false;
			}
			if ( $data->rewrite_hierarchical ) {
				$rewrite['hierarchical'] = true;
			}
			$args['rewrite'] = $rewrite;
			unset( $args['rewrite_slug'] );
			unset( $args['rewrite_no_front'] );
			unset( $args['rewrite_hierarchical'] );
		}
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
