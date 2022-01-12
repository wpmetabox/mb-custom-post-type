<?php
namespace MBCPT;

use WP_Post;
use MetaBox\Support\Arr;

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
			'map_meta_cap'    => true,
			'capabilities'    => [
				// Meta capabilities.
				'edit_post'              => 'edit_mb_post_type',
				'read_post'              => 'read_mb_post_type',
				'delete_post'            => 'delete_mb_post_type',

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
			$settings = $this->get_post_type_settings( $post );
			$post_types[ $settings['slug'] ] = $settings;
		}

		return $post_types;
	}

	public function get_post_type_settings( WP_Post $post ) {
		$settings = empty( $post->post_content ) || isset( $_GET['mbcpt-force'] ) ? $this->migrate_data( $post ) : json_decode( $post->post_content, true );
		$this->parse_archive_slug( $settings );
		$this->parse_icon( $settings );
		$this->parse_supports( $settings );
		$this->parse_capabilities( $settings );

		return $settings;
	}

	private function migrate_data( WP_Post $post ) {
		$args      = [ 'labels' => [] ];
		$post_meta = get_post_meta( $post->ID );

		foreach ( $post_meta as $key => $value ) {
			if ( 0 !== strpos( $key, 'label_' ) && 0 !== strpos( $key, 'args_' ) ) {
				continue;
			}
			$this->unarray( $value, $key, [ 'args_taxonomies', 'args_supports' ] );
			$this->normalize_checkbox( $value );
			$value = 'args_menu_position' === $key ? (int) $value : $value;

			if ( 0 === strpos( $key, 'label_' ) ) {
				$key = str_replace( 'label_', '', $key );
				$args['labels'][ $key ] = $value;
			} else {
				$key = str_replace( 'args_', '', $key );
				$args[ $key ] = $value;
			}

			// delete_post_meta( $post->ID, $key );
		}
		$this->change_key( $args, 'post_type', 'slug' );

		// Bypass new post types.
		if ( isset( $_GET['mbcpt-force'] ) && empty( $args['slug'] ) ) {
			return json_decode( $post->post_content, true );
		}

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
			'post_content' => wp_json_encode( $args, JSON_UNESCAPED_UNICODE ),
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

		$message = [
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
		];

		// Get all post where where post_type = mb-post-type.
		$post_types = get_posts( [
			'posts_per_page'         => -1,
			'post_status'            => 'any',
			'post_type'              => 'mb-post-type',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		] );
		foreach ( $post_types as $post_type ) {
			$settings = $this->get_post_type_settings( $post_type );
			$slug = Arr::get( $settings, 'slug' );

			$messages[ $slug ] = $message;

			if ( ! Arr::get( $settings, 'publicly_queryable' ) ) {
				continue;
			}

			$permalink = get_permalink( $post->ID );

			// translators: %s: Post link, %s: View post text, %s: Post type label.
			$view_link 			   = sprintf( ' <a href="%s">%s</a>.', esc_url( $permalink ), sprintf( __( 'View %s', 'mb-custom-post-type' ), $label_lower ) );
			$messages[ $slug ][1] .= $view_link;
			$messages[ $slug ][6] .= $view_link;
			$messages[ $slug ][9] .= $view_link;

			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			// translators: %s: Post link, %s: Preview post text, %s: Post type label.
			$preview_link 			= sprintf( ' <a target="_blank" href="%s">%s</a>.', esc_url( $preview_permalink ), sprintf( __( 'Preview %s', 'mb-custom-post-type' ), $label_lower ) );
			$messages[ $slug ][8]  .= $preview_link;
			$messages[ $slug ][10] .= $preview_link;
		}

		$messages['mb-post-type']	= $message;
		return $messages;
	}

	public function bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		$labels = [
			'mb-post-type' => [
				'singular' => __( 'post type', 'mb-custom-post-type' ),
				'plural'   => __( 'post types', 'mb-custom-post-type' ),
			],
		];

		// Get all post where where post_type = mb-post-type.
		$post_types = get_posts( [
			'posts_per_page'         => -1,
			'post_status'            => 'any',
			'post_type'              => 'mb-post-type',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		] );
		foreach ( $post_types as $post_type ) {
			$settings = $this->get_post_type_settings( $post_type );
			$slug = Arr::get( $settings, 'slug' );

			$singular = strtolower( Arr::get( $settings, 'labels.singular_name' ) );
			$plural   = strtolower( Arr::get( $settings, 'labels.name' ) );

			$bulk_messages[ $slug ] = array(
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

	private function parse_archive_slug( &$settings ) {
		if ( empty( Arr::get( $settings, 'has_archive' ) ) || empty( Arr::get( $settings, 'archive_slug' ) ) ) {
			return;
		}
		Arr::set( $settings, 'has_archive', $settings['archive_slug'] );
	}
	private function parse_capabilities( &$settings ) {
		if ( 'custom' !== Arr::get( $settings, 'capability_type' ) ) {
			return;
		}
		$plural_name = sanitize_key( Arr::get( $settings, 'labels.name' ) );
		$singular_name = sanitize_key( Arr::get( $settings, 'labels.singular_name' ) );
		if ( $plural_name === $singular_name ) {
			$plural_name .= 's';
		}

		Arr::set( $settings, 'capability_type', [ $singular_name, $plural_name ] );
		Arr::set( $settings, 'map_meta_cap', true );
	}

	private function parse_supports( &$settings ) {
		if ( !empty( Arr::get( $settings, 'supports' ) ) ) {
			return;
		}
		Arr::set( $settings, 'supports', false );
	}

	private function parse_icon( &$settings ) {
		$default = Arr::get( $settings, 'menu_icon', 'dashicons-admin-generic' );

		$icons = [
			'dashicons' => Arr::get( $settings, 'icon' ),
			'svg'       => Arr::get( $settings, 'icon_svg' ),
			'custom'    => Arr::get( $settings, 'icon_custom' ),
		];
		$type = Arr::get( $settings, 'icon_type', 'dashicons' );
		$icon = Arr::get( $icons, $type ) ?: $default;
		Arr::set( $settings, 'menu_icon', $icon );

		unset( $settings['icon_type'] );
		unset( $settings['icon'] );
		unset( $settings['icon_svg'] );
		unset( $settings['icon_custom'] );
	}
}
