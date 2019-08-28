<?php
/**
 * Controls all operations of MB Custom Post Type extension for registering custom post type.
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 */

/**
 * Controls all operations for registering custom post type.
 */
class MB_CPT_Post_Type_Register extends MB_CPT_Base_Register {

	/**
	 * Register custom post types
	 */
	public function register_post_types() {
		// Register main post type 'mb-post-type'.
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
			'show_in_menu' => 'meta-box',
			'menu_icon'    => 'dashicons-editor-justify',
			'can_export'   => true,
			'rewrite'      => false,
			'query_var'    => false,
		);
		register_post_type( 'mb-post-type', $args );

		// Get all registered custom post types.
		$post_types = $this->get_post_types();
		foreach ( $post_types as $post_type => $args ) {
			register_post_type( $post_type, $args );
		}
	}

	/**
	 * Get all registered post types
	 *
	 * @return array
	 */
	public function get_post_types() {
		// This array stores all registered custom post types.
		$post_types = array();

		// Get all post where where post_type = mb-post-type.
		$post_type_ids = get_posts(
			array(
				'posts_per_page' => - 1,
				'post_status'    => 'publish',
				'post_type'      => 'mb-post-type',
				'no_found_rows'  => true,
				'fields'         => 'ids',
			)
		);

		foreach ( $post_type_ids as $post_type ) {
			list( $labels, $args ) = $this->get_post_type_data( $post_type );

			$post_types[ $args['post_type'] ] = $this->set_up_post_type( $labels, $args );
		}

		return $post_types;
	}

	/**
	 * Get new post type data from mb custom post type id.
	 *
	 * @param  int $mb_cpt_id MB custom post type id.
	 * @return array          Array contains label and args of new post type.
	 */
	public function get_post_type_data( $mb_cpt_id ) {
		// Get all post meta from current post.
		$post_meta = get_post_meta( $mb_cpt_id );

		$labels = array();
		$args   = array();
		foreach ( $post_meta as $key => $value ) {
			// @codingStandardsIgnoreLine
			$data = 1 === count( $value ) && $key !== 'args_taxonomies' ? $value[0] : $value;

			if ( ! in_array( $key, array( 'args_menu_position' ) ) ) {
				$data = is_numeric( $data ) ? ( 1 === intval( $data ) ? true : false ) : $data;
			} else {
				$data = intval( $data );
			}

			// If post meta has prefix 'label' then add it to $labels.
			if ( false !== strpos( $key, 'label' ) ) {
				$labels[ str_replace( 'label_', '', $key ) ] = $data;
			} elseif ( false !== strpos( $key, 'args' ) ) {
				$args[ str_replace( 'args_', '', $key ) ] = $data;
			}
		}

		return array( $labels, $args );
	}

	/**
	 * Setup labels, arguments for a custom post type
	 *
	 * @param array $labels Custom post type labels.
	 * @param array $args   Custom post type parameters.
	 *
	 * @return array
	 */
	public function set_up_post_type( $labels = array(), $args = array() ) {
		$labels = wp_parse_args(
			$labels,
			array(
				'menu_name'          => $labels['name'],
				'name_admin_bar'     => $labels['singular_name'],
				'add_new'            => __( 'Add New', 'mb-custom-post-type' ),
				// translators: %s: Name of the custom post type in singular form.
				'add_new_item'       => sprintf( __( 'Add New %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
				// translators: %s: Name of the custom post type in singular form.
				'new_item'           => sprintf( __( 'New %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
				// translators: %s: Name of the custom post type in singular form.
				'edit_item'          => sprintf( __( 'Edit %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
				// translators: %s: Name of the custom post type in singular form.
				'view_item'          => sprintf( __( 'View %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
				// translators: %s: Name of the custom post type in singular form.
				'update_item'        => sprintf( __( 'Update %s', 'mb-custom-post-type' ), $labels['singular_name'] ),
				// translators: %s: Name of the custom post type in plural form.
				'all_items'          => sprintf( __( 'All %s', 'mb-custom-post-type' ), $labels['name'] ),
				// translators: %s: Name of the custom post type in plural form.
				'search_items'       => sprintf( __( 'Search %s', 'mb-custom-post-type' ), $labels['name'] ),
				// translators: %s: Name of the custom post type in singular form.
				'parent_item_colon'  => sprintf( __( 'Parent %s:', 'mb-custom-post-type' ), $labels['name'] ),
				// translators: %s: Name of the custom post type in plural form.
				'not_found'          => sprintf( __( 'No %s found.', 'mb-custom-post-type' ), $labels['name'] ),
				// translators: %s: Name of the custom post type in plural form.
				'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'mb-custom-post-type' ), $labels['name'] ),
			)
		);
		$args   = wp_parse_args(
			$args,
			array(
				'label'  => $labels['name'],
				'labels' => $labels,
				'public' => true,
			)
		);

		if ( 'custom' === $args['capability_type'] ) {
			$args['capability_type'] = array( strtolower( $labels['singular_name'] ), strtolower( $labels['name'] ) );
			$args['map_meta_cap'] = true;
		}

		if ( ! empty( $args['has_archive'] ) && ! empty( $args['archive_slug'] ) ) {
			$args['has_archive'] = $args['archive_slug'];
			unset( $args['archive_slug'] );
		}

		if ( empty( $args['rewrite_slug'] ) && empty( $args['rewrite_no_front'] ) ) {
			$args['rewrite'] = true;
		} else {
			$rewrite = array();
			if ( ! empty( $args['rewrite_slug'] ) ) {
				$rewrite['slug'] = $args['rewrite_slug'];
			}
			if ( ! empty( $args['rewrite_no_front'] ) ) {
				$rewrite['with_front'] = false;
			}
			$args['rewrite'] = $rewrite;
			unset( $args['rewrite_slug'] );
			unset( $args['rewrite_no_front'] );
		}
		unset( $args['post_type'] );

		return $args;
	}

	/**
	 * Custom post updated messages.
	 *
	 * @param array $messages Post messages.
	 *
	 * @return array
	 */
	public function updated_message( $messages ) {
		$post             = get_post();
		$post_type_object = get_post_type_object( $post->post_type );
		$label            = ucfirst( $post_type_object->labels->singular_name );
		$label_lower      = strtolower( $label );
		$label            = ucfirst( $label_lower );
		$revision         = filter_input( INPUT_GET, 'revision', FILTER_SANITIZE_NUMBER_INT );

		$message = array(
			0  => '', // Unused. Messages start at index 1.
			// translators: %s: Name of the custom post type in singular form.
			1  => sprintf( __( '%s updated.', 'mb-custom-post-type' ), $label ),
			2  => __( 'Custom field updated.', 'mb-custom-post-type' ),
			3  => __( 'Custom field deleted.', 'mb-custom-post-type' ),
			// translators: %s: Name of the custom post type in singular form.
			4  => sprintf( __( '%s updated.', 'mb-custom-post-type' ), $label ),
			// translators: %1$s: Name of the custom post type in singular form, %2$s: Revision title.
			5  => $revision ? sprintf( __( '%1$s restored to revision from %2$s.', 'mb-custom-post-type' ), $label, wp_post_revision_title( $revision, false ) ) : false,
			// translators: %s: Name of the custom post type in singular form.
			6  => sprintf( __( '%s published.', 'mb-custom-post-type' ), $label ),
			// translators: %s: Name of the custom post type in singular form.
			7  => sprintf( __( '%s saved.', 'mb-custom-post-type' ), $label ),
			// translators: %s: Name of the custom post type in singular form.
			8  => sprintf( __( '%s submitted.', 'mb-custom-post-type' ), $label ),
			// translators: %1$s: Name of the custom post type in singular form, %2$s: Revision title.
			9  => sprintf( __( '%1$s scheduled for: <strong>%2$s</strong>.', 'mb-custom-post-type' ), $label, date_i18n( __( 'M j, Y @ G:i', 'mb-custom-post-type' ), strtotime( $post->post_date ) ) ),
			// translators: %s: Name of the custom post type in singular form.
			10 => sprintf( __( '%s draft updated.', 'mb-custom-post-type' ), $label ),
		);

		// Get all post where where post_type = mb-post-type.
		$post_types = get_posts(
			array(
				'posts_per_page' => - 1,
				'post_status'    => 'any',
				'post_type'      => 'mb-post-type',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		foreach ( $post_types as $post_type ) {
			$slug              = get_post_meta( $post_type, 'args_post_type', true );
			$messages[ $slug ] = $message;

			if ( get_post_meta( $post_type, 'args_publicly_queryable', true ) ) {
				$permalink = get_permalink( $post->ID );

				// translators: %s: Post link, %s: View post text, %s: Post type label.
				$view_link             = sprintf( ' <a href="%s">%s</a>.', esc_url( $permalink ), sprintf( __( 'View %s', 'mb-custom-post-type' ), $label_lower ) );
				$messages[ $slug ][1] .= $view_link;
				$messages[ $slug ][6] .= $view_link;
				$messages[ $slug ][9] .= $view_link;

				$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
				// translators: %s: Post link, %s: Preview post text, %s: Post type label.
				$preview_link           = sprintf( ' <a target="_blank" href="%s">%s</a>.', esc_url( $preview_permalink ), sprintf( __( 'Preview %s', 'mb-custom-post-type' ), $label_lower ) );
				$messages[ $slug ][8]  .= $preview_link;
				$messages[ $slug ][10] .= $preview_link;
			}
		}

		$messages['mb-post-type'] = $message;

		return $messages;
	}

	/**
	 * Custom post management WordPress messages.
	 *
	 * @param array $bulk_messages Post bulk messages.
	 * @param array $bulk_counts   Number of posts.
	 *
	 * @return array
	 */
	public function bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		$labels = array(
			'mb-post-type' => array(
				'singular' => __( 'post type', 'mb-custom-post-type' ),
				'plural'   => __( 'post types', 'mb-custom-post-type' ),
			),
		);

		// Get all post where where post_type = mb-post-type.
		$post_types = get_posts(
			array(
				'posts_per_page' => - 1,
				'post_status'    => 'any',
				'post_type'      => 'mb-post-type',
				'no_found_rows'  => true,
				'fields'         => 'ids',
			)
		);
		foreach ( $post_types as $post_type ) {
			$slug            = get_post_meta( $post_type, 'args_post_type', true );
			$labels[ $slug ] = array(
				'singular' => strtolower( get_post_meta( $post_type, 'label_singular_name', true ) ),
				'plural'   => strtolower( get_post_meta( $post_type, 'label_name', true ) ),
			);
		}

		foreach ( $labels as $post_type => $label ) {
			$singular = $label['singular'];
			$plural   = $label['plural'];

			$bulk_messages[ $post_type ] = array(
				// translators: %1$s: Number of items, %2$s: Name of the post type in singular or plural form.
				'updated'   => sprintf( __( '%1$s %2$s updated.', 'mb-custom-post-type' ), $bulk_counts['updated'], $bulk_counts['updated'] > 1 ? $plural : $singular ),
				// translators: %1$s: Number of items, %2$s: Name of the post type in singular or plural form.
				'locked'    => sprintf( __( '%1$s %2$s not updated, somebody is editing.', 'mb-custom-post-type' ), $bulk_counts['locked'], $bulk_counts['locked'] > 1 ? $plural : $singular ),
				// translators: %1$s: Number of items, %2$s: Name of the post type in singular or plural form.
				'deleted'   => sprintf( __( '%1$s %2$s permanently deleted.', 'mb-custom-post-type' ), $bulk_counts['deleted'], $bulk_counts['deleted'] > 1 ? $plural : $singular ),
				// translators: %1$s: Number of items, %2$s: Name of the post type in singular or plural form.
				'trashed'   => sprintf( __( '%1$s %2$s moved to the Trash.', 'mb-custom-post-type' ), $bulk_counts['trashed'], $bulk_counts['trashed'] > 1 ? $plural : $singular ),
				// translators: %1$s: Number of items, %2$s: Name of the post type in singular or plural form.
				'untrashed' => sprintf( __( '%1$s %2$s restored from the Trash.', 'mb-custom-post-type' ), $bulk_counts['untrashed'], $bulk_counts['untrashed'] > 1 ? $plural : $singular ),
			);
		}

		return $bulk_messages;
	}
}
