<?php
use WP_Post as WP_Post;

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
		$post_types = [];

		$posts = get_posts( [
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'post_type'      => 'mb-post-type',
				'no_found_rows'  => true,
		] );

		foreach ( $posts as $post ) {
			$data = $this->get_post_type_data( $post );
			$post_types[ $data->slug ] = $this->set_up_post_type( $data );
		}

		return $post_types;
	}

	public function get_post_type_data( WP_Post $post ) {
		$this->migrate_data( $post );

		return json_decode( $post->post_content );
	}

	private function migrate_data( WP_Post $post ) {
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

			$key = str_replace( ['label_', 'args_'], '', $key );
			$args[ $key ] = $value;

			// delete_post_meta( $post->ID, $key );
		}

		$args['slug'] = $args['post_type'];
		unset( $args['post_type'] );

		$args['function_name'] = empty( $args['function_name'] ) ? 'your_function_name' : $args['function_name'];
		$args['text_domain'] = empty( $args['text_domain'] ) ? 'text-domain' : $args['text_domain'];

		wp_update_post( [
			'ID'           => $post->ID,
			'post_content' => wp_json_encode( $args ),
		] );
	}

	public function set_up_post_type( $data ) {
		$labels = [
			'menu_name'          => $data->name,
			'name_admin_bar'     => $data->singular_name,
			'add_new'            => $data->add_new,
			'add_new_item'       => $data->add_new_item,
			'new_item'           => $data->new_item,
			'edit_item'          => $data->edit_item,
			'view_item'          => $data->view_item,
			'update_item'        => $data->update_item,
			'all_items'          => $data->all_items,
			'search_items'       => $data->search_items,
			'parent_item_colon'  => $data->parent_item_colon,
			'not_found'          => $data->not_found,
			'not_found_in_trash' => $data->not_found_in_trash,
		];
		$args = [
			'label'  => $data->name,
			'labels' => $labels,
			'public' => true,
		];

		if ( property_exists ( $data, 'capability_type' ) && 'custom' === $data->capability_type ) {
			$args['capability_type'] = [ strtolower( $data->singular_name ), strtolower( $data->name ) ];
			$args['map_meta_cap'] = true;
		}

		if ( property_exists( $data, 'has_archive' ) && property_exists( $data, 'archive_slug' ) ) {
			$args['has_archive'] = $data->archive_slug;
		}

		if ( ! property_exists ( $data, 'rewrite_slug' ) && ! property_exists ( $data, 'rewrite_no_front' ) ) {
			$args['rewrite'] = true;
		} else {
			$rewrite = [];
			if ( property_exists ( $data, 'rewrite_slug' ) ) {
				$rewrite['slug'] = $data->rewrite_slug;
			}
			if ( property_exists ( $data, 'rewrite_no_front' ) ) {
				$rewrite['with_front'] = false;
			}

			$args['rewrite'] = $rewrite;
		}

		return $args;
	}

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
		$messages['mb-post-type'] = $message;

		return $messages;
	}
}
