<?php

/**
 * This class controls all operations of Meta Box Custom Post Type extension
 * for creating / modifying custom post type.
 */
class MB_CPT_Register
{
	/**
	 * Initiating
	 */
	public function __construct()
	{
		// Register post types
		add_action( 'init', array( $this, 'register_post_types' ), 0 );

		// Change the output of post/bulk post updated messages
		add_filter( 'post_updated_messages', array( $this, 'updated_message' ), 10, 1 );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_updated_messages' ), 10, 2 );
	}

	/**
	 * Register custom post types
	 *
	 * @return void
	 */
	public function register_post_types()
	{
		// Get all registered custom post types
		$post_types = $this->get_all_registered_post_types();

		foreach ( $post_types as $post_type )
		{
			register_post_type( $post_type['post_type'], $post_type );
		}

		// Refresh permalink
		flush_rewrite_rules();
	}

	/**
	 * Get all registered post types
	 *
	 * @return array
	 */
	public function get_all_registered_post_types()
	{
		// This array stores all registered custom post types
		$post_types = array();

		// Create mb-post-type post type to management/add/edit custom post types
		$post_types[] = $this->set_up_post_type(
			array(
				'name'          => __( 'Post Types', 'mb-cpt' ),
				'singular_name' => __( 'Post Type', 'mb-cpt' ),
			),
			array(
				'public'    => false,
				'supports'  => false,
				'menu_icon' => 'dashicons-editor-justify',
				'post_type' => 'mb-post-type',
			)
		);

		// Get all post where where post_type = mb-post-type
		$mb_post_types = get_posts( array(
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'post_type'      => 'mb-post-type',
		) );

		foreach ( $mb_post_types as $post_type )
		{
			// Get all post meta from current post
			$post_meta = get_post_meta( $post_type->ID );
			// Create array that contains Labels of this current custom post type
			$labels = array();
			// Create array that contains arguments of this current custom post type
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

			$post_types[] = $this->set_up_post_type( $labels, $args );
		}

		return $post_types;
	}

	/**
	 * Setup labels, arguments for a custom post type
	 *
	 * @param array $labels
	 * @param array $args
	 *
	 * @return array
	 */
	public function set_up_post_type( $labels = array(), $args = array() )
	{
		// Default labels
		$default_labels = array(
			'menu_name'          => $labels['name'],
			'name_admin_bar'     => $labels['singular_name'],
			'add_new'            => __( 'Add New', 'mb-cpt' ),
			'add_new_item'       => sprintf( __( 'Add New %s', 'mb-cpt' ), $labels['singular_name'] ),
			'new_item'           => sprintf( __( 'New %s', 'mb-cpt' ), $labels['singular_name'] ),
			'edit_item'          => sprintf( __( 'Edit %s', 'mb-cpt' ), $labels['singular_name'] ),
			'view_item'          => sprintf( __( 'View %s', 'mb-cpt' ), $labels['singular_name'] ),
			'update_item'        => sprintf( __( 'Update %s', 'mb-cpt' ), $labels['singular_name'] ),
			'all_items'          => sprintf( __( 'All %s', 'mb-cpt' ), $labels['name'] ),
			'search_items'       => sprintf( __( 'Search %s', 'mb-cpt' ), $labels['name'] ),
			'parent_item_colon'  => sprintf( __( 'Parent %s:', 'mb-cpt' ), $labels['name'] ),
			'not_found'          => sprintf( __( 'No %s found.', 'mb-cpt' ), $labels['name'] ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'mb-cpt' ), $labels['name'] ),
		);

		$labels = wp_parse_args( $labels, $default_labels );

		// Default arguments
		$default_args = array(
			'labels'              => $labels,
			'description'         => sprintf( __( '%s GUI', 'mb-cpt' ), $labels['name'] ),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => $args['post_type'] ),
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-admin-appearance',
			'has_archive'         => true,
			'can_export'          => true,
			'show_in_nav_menus'   => true,
			'exclude_from_search' => false,
		);

		$args = wp_parse_args( $args, $default_args );

		return $args;
	}

	/**
	 * Custom post updated messages
	 *
	 * @param array $messages
	 *
	 * @return array
	 */
	public function updated_message( $messages )
	{
		$post             = get_post();
		$post_type_object = get_post_type_object( $post->post_type );
		$label            = ucfirst( $post_type_object->labels->singular_name );
		$label_lower      = strtolower( $label );

		$permalink = get_permalink( $post );
		if ( ! $permalink )
		{
			$permalink = '';
		}
		$preview_url = add_query_arg( 'preview', 'true', $permalink );

		$message = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( __( '%s updated. <a href="%s">View %s.</a>', 'mb-cpt' ), $label, esc_url( $permalink ), $label_lower ),
			2  => __( 'Custom field updated.', 'mb-cpt' ),
			3  => __( 'Custom field deleted.', 'mb-cpt' ),
			4  => sprintf( __( '%s updated.', 'mb-cpt' ), $label ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( '%s restored to revision from %s.', 'mb-cpt' ), $label, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( '%s published. <a href="%s">View %s</a>.', 'mb-cpt' ), $label, esc_url( $permalink ), $label_lower ),
			7  => sprintf( __( '%s saved.', 'mb-cpt' ), $label ),
			8  => sprintf( __( '%s submitted. <a target="_blank" href="%s">Preview %s</a>.', 'mb-cpt' ), $label, esc_url( $preview_url ), $label_lower ),
			9  => sprintf(
				__( '%s scheduled for: <strong>%s</strong>. <a target="_blank" href="%s">Preview %s</a>.', 'mb-cpt' ),
				$label,
				date_i18n( __( 'M j, Y @ G:i', 'mb-cpt' ), strtotime( $post->post_date ) ),
				esc_url( $permalink ),
				$label_lower
			),
			10 => sprintf( __( '%s draft updated. <a target="_blank" href="%s">Preview %s</a>.', 'mb-cpt' ), $label, esc_url( $preview_url ), $label_lower ),
		);

		// Get all post where where post_type = mb-post-type
		$post_types = get_posts( array(
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'post_type'      => 'mb-post-type',
			'fields'         => 'ids',
		) );
		foreach ( $post_types as $post_type )
		{
			$slug            = get_post_meta( $post_type, 'args_post_type', true );
			$messages[$slug] = $message;
		}

		$messages['mb-post-type'] = $message;

		return $messages;
	}

	/**
	 * Custom post management wordpress messages
	 *
	 * @param array $bulk_messages
	 * @param array $bulk_counts
	 *
	 * @return array
	 */
	public function bulk_updated_messages( $bulk_messages, $bulk_counts )
	{
		$labels = array(
			'mb-post-type' => array(
				'singular' => __( 'post type', 'mb-cpt' ),
				'plural'   => __( 'post types', 'mb-cpt' )
			),
		);

		// Get all post where where post_type = mb-post-type
		$post_types = get_posts( array(
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'post_type'      => 'mb-post-type',
		) );
		foreach ( $post_types as $post_type )
		{
			$slug          = get_post_meta( $post_type->ID, 'args_post_type', true );
			$labels[$slug] = array(
				'singular' => strtolower( get_post_meta( $post_type->ID, 'label_singular_name', true ) ),
				'plural'   => strtolower( get_post_meta( $post_type->ID, 'label_name', true ) ),
			);
		}

		foreach ( $labels as $post_type => $label )
		{
			$singular = $label['singular'];
			$plural   = $label['plural'];

			$bulk_messages[$post_type] = array(
				'updated'   => sprintf( __( '%s %s updated.', 'mb-cpt' ), $bulk_counts['updated'], $bulk_counts['updated'] > 1 ? $plural : $singular ),
				'locked'    => sprintf( __( '%s %s not updated, somebody is editing.', 'mb-cpt' ), $bulk_counts['locked'], $bulk_counts['locked'] > 1 ? $plural : $singular ),
				'deleted'   => sprintf( __( '%s %s permanently deleted.', 'mb-cpt' ), $bulk_counts['deleted'], $bulk_counts['deleted'] > 1 ? $plural : $singular ),
				'trashed'   => sprintf( __( '%s %s moved to the Trash.', 'mb-cpt' ), $bulk_counts['trashed'], $bulk_counts['trashed'] > 1 ? $plural : $singular ),
				'untrashed' => sprintf( __( '%s %s restored from the Trash.', 'mb-cpt' ), $bulk_counts['untrashed'], $bulk_counts['untrashed'] > 1 ? $plural : $singular ),
			);
		}

		return $bulk_messages;
	}
}
