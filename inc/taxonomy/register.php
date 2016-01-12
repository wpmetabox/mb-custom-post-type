<?php
/**
 * Controls all operations of MB Custom Taxonomy extension for registering custom taxonomy.
 *
 * @package    Meta Box
 * @subpackage MB Custom Taxonomy
 * @author     Doan Manh Duc
 * @author     Tran Ngoc Tuan Anh
 */

/**
 * Controls all operations for registering custom taxonomy.
 */
class MB_CPT_Taxonomy_Register extends MB_CPT_Base_Register
{
	/**
	 * Register custom post type for taxonomies
	 */
	public function register_post_types()
	{
		// Register post type of the plugin 'mb-taxonomy'
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
			'show_in_menu' => 'edit.php?post_type=mb-post-type',
			'menu_icon'    => 'dashicons-exerpt-view',
			'can_export'   => true,
			'rewrite'      => false,
			'query_var'    => false,
		);
		register_post_type( 'mb-taxonomy', $args );

		// Get all registered custom taxonomies
		$taxonomies = $this->get_taxonomies();
		foreach ( $taxonomies as $taxonomy => $args )
		{
			register_taxonomy( $taxonomy, $args['post_types'], $args );
		}
	}

	/**
	 * Get all registered taxonomies
	 * @return array
	 */
	public function get_taxonomies()
	{
		// This array stores all registered custom taxonomies
		$taxonomies = array();

		// Get all post where where post_type = mb-taxonomy
		$taxonomy_ids = get_posts( array(
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'post_type'      => 'mb-taxonomy',
			'no_found_rows'  => true,
			'fields'         => 'ids',
		) );

		foreach ( $taxonomy_ids as $taxonomy_id )
		{
			// Get all post meta from current post
			$post_meta = get_post_meta( $taxonomy_id );
			// Create array that contains Labels of this current custom taxonomy
			$labels = array();
			// Create array that contains arguments of this current custom taxonomy
			$args = array();

			foreach ( $post_meta as $key => $value )
			{
				// If post meta has prefix 'label' then add it to $labels
				if ( false !== strpos( $key, 'label' ) )
				{
					$data = 1 == count( $value ) ? $value[0] : $value;

					$labels[str_replace( 'label_', '', $key )] = $data;
				}
				// If post meta has prefix 'args' then add it to $args
				elseif ( false !== strpos( $key, 'args' ) )
				{
					$data = 1 == count( $value ) ? $value[0] : $value;
					$data = is_numeric( $data ) ? ( 1 == intval( $data ) ? true : false ) : $data;

					$args[str_replace( 'args_', '', $key )] = $data;
				}
			}

			$taxonomies[$args['taxonomy']] = $this->set_up_taxonomy( $labels, $args );
		}

		return $taxonomies;
	}

	/**
	 * Setup labels, arguments for a custom taxonomy
	 *
	 * @param array $labels
	 * @param array $args
	 * @return array
	 */
	public function set_up_taxonomy( $labels = array(), $args = array() )
	{
		$labels = wp_parse_args( $labels, array(
			'menu_name'                  => $labels['name'],
			'all_items'                  => sprintf( __( 'All %s', 'mb-custom-post-type' ), $labels['name'] ),
			'edit_item'                  => sprintf( __( 'Edit %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
			'view_item'                  => sprintf( __( 'View %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
			'update_item'                => sprintf( __( 'Update %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
			'add_new_item'               => sprintf( __( 'Add new %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
			'new_item_name'              => sprintf( __( 'New %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
			'parent_item'                => sprintf( __( 'Parent %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
			'parent_item_colon'          => sprintf( __( 'Parent %s:', 'mb-custom-post-type' ), $labels['singular_name'] ),
			'search_items'               => sprintf( __( 'Search %s', 'mb-custom-post-type' ), $labels['name'] ),
			'popular_items'              => sprintf( __( 'Popular %s', 'mb-custom-post-type' ), $labels['name'] ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'mb-custom-post-type' ), $labels['name'] ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'mb-custom-post-type' ), $labels['name'] ),
			'choose_from_most_used'      => sprintf( __( 'Choose most used %s', 'mb-custom-post-type' ), $labels['name'] ),
			'not_found'                  => sprintf( __( 'No %s found', 'mb-custom-post-type' ), $labels['name'] ),
		) );
		$args   = wp_parse_args( $args, array(
			'label'  => $labels['name'],
			'labels' => $labels,
			'public' => true,
		) );
		unset( $args['taxonomy'] );
		return $args;
	}

	/**
	 * Custom post updated messages
	 *
	 * @param array $messages
	 * @return array
	 */
	public function updated_message( $messages )
	{
		$post = get_post();

		$messages['mb-taxonomy'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Taxonomy updated.', 'mb-custom-post-type' ),
			2  => __( 'Custom field updated.', 'mb-custom-post-type' ),
			3  => __( 'Custom field deleted.', 'mb-custom-post-type' ),
			4  => __( 'Taxonomy updated.', 'mb-custom-post-type' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Taxonomy restored to revision from %s.', 'mb-custom-post-type' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Taxonomy published.', 'mb-custom-post-type' ),
			7  => __( 'Taxonomy saved.', 'mb-custom-post-type' ),
			8  => __( 'Taxonomy submitted.', 'mb-custom-post-type' ),
			9  => sprintf( __( 'Taxonomy scheduled for: <strong>%s</strong>.', 'mb-custom-post-type' ), date_i18n( __( 'M j, Y @ G:i', 'mb-custom-post-type' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Taxonomy draft updated.', 'mb-custom-post-type' ),
		);
		return $messages;
	}

	/**
	 * Custom post management WordPress messages
	 *
	 * @param array $bulk_messages
	 * @param array $bulk_counts
	 * @return array
	 */
	public function bulk_updated_messages( $bulk_messages, $bulk_counts )
	{
		$bulk_messages['mb-taxonomy'] = array(
			'updated'   => sprintf( _n( '%s taxonomy updated.', '%s taxonomies updated.', $bulk_counts['updated'], 'mb-custom-post-type' ), $bulk_counts['updated'] ),
			'locked'    => sprintf( _n( '%s taxonomy not updated, somebody is editing.', '%s taxonomies not updated, somebody is editing.', $bulk_counts['locked'], 'mb-custom-post-type' ), $bulk_counts['locked'] ),
			'deleted'   => sprintf( _n( '%s taxonomy permanently deleted.', '%s taxonomies permanently deleted.', $bulk_counts['deleted'], 'mb-custom-post-type' ), $bulk_counts['deleted'] ),
			'trashed'   => sprintf( _n( '%s taxonomy moved to the Trash.', '%s taxonomies moved to the Trash.', $bulk_counts['trashed'], 'mb-custom-post-type' ), $bulk_counts['trashed'] ),
			'untrashed' => sprintf( _n( '%s taxonomy restored from the Trash.', '%s taxonomies restored from the Trash.', $bulk_counts['untrashed'], 'mb-custom-post-type' ), $bulk_counts['untrashed'] ),
		);
		return $bulk_messages;
	}
}
