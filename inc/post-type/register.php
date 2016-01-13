<?php
/**
 * Controls all operations of MB Custom Post Type extension for registering custom post type.
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 * @author     Doan Manh Duc
 * @author     Tran Ngoc Tuan Anh
 */

/**
 * Controls all operations for registering custom post type.
 */
class MB_CPT_Post_Type_Register extends MB_CPT_Base_Register
{
	/**
	 * Register custom post types
	 */
	public function register_post_types()
	{
		// Register main post type 'mb-post-type'
		$labels = array(
			'name'               => _x( 'Post Types', 'Post Type General Name', 'mb-custom-post-type' ),
			'singular_name'      => _x( 'Post Type', 'Post Type Singular Name', 'mb-custom-post-type' ),
			'menu_name'          => __( 'Post Types', 'mb-custom-post-type' ),
			'name_admin_bar'     => __( 'Post Type', 'mb-custom-post-type' ),
			'parent_item_colon'  => __( 'Parent Post Type:', 'mb-custom-post-type' ),
			'all_items'          => __( 'Post Types', 'mb-custom-post-type' ),
			'add_new_item'       => __( 'Add New Post Type', 'mb-custom-post-type' ),
			'add_new'            => __( 'New Post Type', 'mb-custom-post-type' ),
			'new_item'           => __( 'New Post Type', 'mb-custom-post-type' ),
			'edit_item'          => __( 'Edit Post Type', 'mb-custom-post-type' ),
			'update_item'        => __( 'Update Post Type', 'mb-custom-post-type' ),
			'view_item'          => __( 'View Post Type', 'mb-custom-post-type' ),
			'search_items'       => __( 'Search Post Type', 'mb-custom-post-type' ),
			'not_found'          => __( 'Not found', 'mb-custom-post-type' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'mb-custom-post-type' ),
		);
		$args   = array(
			'label'        => __( 'Post Types', 'mb-custom-post-type' ),
			'labels'       => $labels,
			'supports'     => false,
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-editor-justify',
			'can_export'   => true,
			'rewrite'      => false,
			'query_var'    => false,
		);
		register_post_type( 'mb-post-type', $args );

		// Get all registered custom post types
		$post_types = $this->get_post_types();
		foreach ( $post_types as $post_type => $args )
		{
			register_post_type( $post_type, $args );
		}
	}

	/**
	 * Get all registered post types
	 *
	 * @return array
	 */
	public function get_post_types()
	{
		// This array stores all registered custom post types
		$post_types = array();

		// Get all post where where post_type = mb-post-type
		$post_type_ids = get_posts( array(
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'post_type'      => 'mb-post-type',
			'no_found_rows'  => true,
			'fields'         => 'ids',
		) );

		foreach ( $post_type_ids as $post_type )
		{
			// Get all post meta from current post
			$post_meta = get_post_meta( $post_type );

			$labels = array();
			$args   = array();
			foreach ( $post_meta as $key => $value )
			{
				$data = 1 == count( $value ) && $key != 'args_taxonomies' ? $value[0] : $value;
				$data = is_numeric( $data ) ? ( 1 == intval( $data ) ? true : false ) : $data;

				// If post meta has prefix 'label' then add it to $labels
				if ( false !== strpos( $key, 'label' ) )
				{
					$labels[str_replace( 'label_', '', $key )] = $data;
				}
				// If post meta has prefix 'args' then add it to $args
				elseif ( false !== strpos( $key, 'args' ) )
				{
					$args[str_replace( 'args_', '', $key )] = $data;
				}
			}

			$post_types[$args['post_type']] = $this->set_up_post_type( $labels, $args );
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
		$labels = wp_parse_args( $labels, array(
			'menu_name'          => $labels['name'],
			'name_admin_bar'     => $labels['singular_name'],
			'add_new'            => __( 'Add New', 'mb-custom-post-type' ),
			'add_new_item'       => sprintf( __( 'Add New %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
			'new_item'           => sprintf( __( 'New %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
			'edit_item'          => sprintf( __( 'Edit %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
			'view_item'          => sprintf( __( 'View %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
			'update_item'        => sprintf( __( 'Update %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
			'all_items'          => sprintf( __( 'All %s', 'mb-custom-post-type' ), $labels['name'] ),
			'search_items'       => sprintf( __( 'Search %s', 'mb-custom-post-type' ), $labels['name'] ),
			'parent_item_colon'  => sprintf( __( 'Parent %s:', 'mb-custom-post-type' ), $labels['name'] ),
			'not_found'          => sprintf( __( 'No %s found.', 'mb-custom-post-type' ), $labels['name'] ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'mb-custom-post-type' ), $labels['name'] ),
		) );
		$args   = wp_parse_args( $args, array(
			'label'  => $labels['name'],
			'labels' => $labels,
			'public' => true,
		) );
		unset( $args['post_type'] );

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
		$label            = ucfirst( $label_lower );

		$message = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( __( '%s updated.', 'mb-custom-post-type' ), $label ),
			2  => __( 'Custom field updated.', 'mb-custom-post-type' ),
			3  => __( 'Custom field deleted.', 'mb-custom-post-type' ),
			4  => sprintf( __( '%s updated.', 'mb-custom-post-type' ), $label ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( '%s restored to revision from %s.', 'mb-custom-post-type' ), $label, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( '%s published.', 'mb-custom-post-type' ), $label ),
			7  => sprintf( __( '%s saved.', 'mb-custom-post-type' ), $label ),
			8  => sprintf( __( '%s submitted.', 'mb-custom-post-type' ), $label ),
			9  => sprintf( __( '%s scheduled for: <strong>%s</strong>.', 'mb-custom-post-type' ), $label, date_i18n( __( 'M j, Y @ G:i', 'mb-custom-post-type' ), strtotime( $post->post_date ) ) ),
			10 => sprintf( __( '%s draft updated.', 'mb-custom-post-type' ), $label ),
		);

		// Get all post where where post_type = mb-post-type
		$post_types = get_posts( array(
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'post_type'      => 'mb-post-type',
			'fields'         => 'ids',
			'no_found_rows'  => true,
		) );
		foreach ( $post_types as $post_type )
		{
			$slug            = get_post_meta( $post_type, 'args_post_type', true );
			$messages[$slug] = $message;

			if ( get_post_meta( $post_type, 'args_publicly_queryable', true ) )
			{
				$permalink = get_permalink( $post->ID );

				$view_link = sprintf( ' <a href="%s">%s</a>.', esc_url( $permalink ), sprintf( __( 'View %s', 'mb-custom-post-type' ), $label_lower ) );
				$messages[$slug][1] .= $view_link;
				$messages[$slug][6] .= $view_link;
				$messages[$slug][9] .= $view_link;

				$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
				$preview_link      = sprintf( ' <a target="_blank" href="%s">%s</a>.', esc_url( $preview_permalink ), sprintf( __( 'Preview %s', 'mb-custom-post-type' ), $label_lower ) );
				$messages[$slug][8] .= $preview_link;
				$messages[$slug][10] .= $preview_link;
			}
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
				'singular' => __( 'post type', 'mb-custom-post-type' ),
				'plural'   => __( 'post types', 'mb-custom-post-type' ),
			),
		);

		// Get all post where where post_type = mb-post-type
		$post_types = get_posts( array(
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'post_type'      => 'mb-post-type',
			'no_found_rows'  => true,
			'fields'         => 'ids',
		) );
		foreach ( $post_types as $post_type )
		{
			$slug          = get_post_meta( $post_type, 'args_post_type', true );
			$labels[$slug] = array(
				'singular' => strtolower( get_post_meta( $post_type, 'label_singular_name', true ) ),
				'plural'   => strtolower( get_post_meta( $post_type, 'label_name', true ) ),
			);
		}

		foreach ( $labels as $post_type => $label )
		{
			$singular = $label['singular'];
			$plural   = $label['plural'];

			$bulk_messages[$post_type] = array(
				'updated'   => sprintf( __( '%s %s updated.', 'mb-custom-post-type' ), $bulk_counts['updated'], $bulk_counts['updated'] > 1 ? $plural : $singular ),
				'locked'    => sprintf( __( '%s %s not updated, somebody is editing.', 'mb-custom-post-type' ), $bulk_counts['locked'], $bulk_counts['locked'] > 1 ? $plural : $singular ),
				'deleted'   => sprintf( __( '%s %s permanently deleted.', 'mb-custom-post-type' ), $bulk_counts['deleted'], $bulk_counts['deleted'] > 1 ? $plural : $singular ),
				'trashed'   => sprintf( __( '%s %s moved to the Trash.', 'mb-custom-post-type' ), $bulk_counts['trashed'], $bulk_counts['trashed'] > 1 ? $plural : $singular ),
				'untrashed' => sprintf( __( '%s %s restored from the Trash.', 'mb-custom-post-type' ), $bulk_counts['untrashed'], $bulk_counts['untrashed'] > 1 ? $plural : $singular ),
			);
		}

		return $bulk_messages;
	}
}
