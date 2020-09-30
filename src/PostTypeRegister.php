<?php
namespace MBCPT;

use WP_Post;

class PostTypeRegister extends Register {
	public function register() {
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
			'label'         => __( 'Post Types', 'mb-custom-post-type' ),
			'labels'        => $labels,
			'supports'      => false,
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => defined( 'RWMB_VER' ) ? 'meta-box' : null,
			'menu_icon'     => 'dashicons-editor-justify',
			'can_export'    => true,
			'rewrite'       => false,
			'query_var'     => false,
			'menu_position' => 200,
		);
		register_post_type( 'mb-post-type', $args );

		// Get all registered custom post types.
		$post_types = $this->get_post_types();
		foreach ( $post_types as $post_type => $args ) {
			register_post_type( $post_type, $args );
		}
	}

	public function get_post_types() {
		$post_types = [];

		$posts = get_posts( [
			'posts_per_page'         => -1,
			'post_status'            => 'publish',
			'post_type'              => 'mb-post-type',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		] );

		foreach ( $posts as $post ) {
			$data = $this->get_post_type_data( $post );
			$post_types[ $data['slug'] ] = $data;
		}

		return $post_types;
	}

	public function get_post_type_data( WP_Post $post ) {
		return empty( $post->post_content ) ? $this->migrate_data( $post ) : json_decode( $post->post_content, true );
	}

	private function migrate_data( WP_Post $post ) {
		$args      = [ 'labels' => [] ];
		$post_meta = get_post_meta( $post->ID );

		unset( $post_meta['_edit_last'], $post_meta['_edit_lock'] );
		foreach ( $post_meta as $key => $value ) {
			$this->unarray( $value, $key, [ 'args_taxonomies', 'args_supports' ] );
			$this->normalize_checkbox( $value );
			$value = 'args_menu_position' === $key ? (int) $value : $value;

			if ( false !== strpos( $key, 'label_' ) ) {
				$key = str_replace( 'label_', '', $key );
				$args['labels'][ $key ] = $value;
			} else {
				$key = str_replace( 'args_', '', $key );
				$args[ $key ] = $value;
			}

			// delete_post_meta( $post->ID, $key );
		}
		$this->change_key( $args, 'post_type', 'slug' );

		// Rewrite.
		$rewrite = [];
		if ( isset( $args['rewrite_slug'] ) ) {
			$rewrite['slug'] = $args['rewrite_slug'];
		}
		$rewrite['with_front'] = isset( $args['rewrite_no_front'] ) ? ! $args['rewrite_no_front'] : true;
		$args['rewrite'] = $rewrite;
		unset( $args['rewrite_slug'], $args['rewrite_no_front'] );

		wp_update_post( [
			'ID'           => $post->ID,
			'post_content' => wp_json_encode( $args ),
		] );
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
