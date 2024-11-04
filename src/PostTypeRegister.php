<?php
namespace MBCPT;

use WP_Post;
use MetaBox\Support\Arr;

class PostTypeRegister extends Register {
	private $menu_positions = [];

	public function register() {
		// Register main post type 'mb-post-type'.
		$labels = [
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
		];
		$args   = [
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
			'map_meta_cap'  => true,
			'capabilities'  => [
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
		];

		register_post_type( 'mb-post-type', $args );

		// Get all registered custom post types.
		$post_types = $this->get_post_types();

		foreach ( $post_types as $post_type => $settings ) {
			// Menu position can be float value. In this case, WordPress will ignore the value. We'll need to fix it later.
			if ( ! empty( $settings['menu_position'] ) && ! is_int( $settings['menu_position'] ) ) {
				$this->menu_positions[ $post_type ] = $settings;
			}
			register_post_type( $post_type, $settings );
		}

		// Fix menu position if a post type is set with float value.
		if ( ! empty( $this->menu_positions ) ) {
			add_action( 'admin_menu', [ $this, 'fix_menu_positions' ] );
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
			if ( empty( $settings ) ) {
				continue;
			}

			$post_types[ $settings['slug'] ] = $settings;
		}

		return $post_types;
	}

	public function get_post_type_settings( WP_Post $post ) {
		// phpcs:ignore
		$settings = empty( $post->post_content ) || isset( $_GET['mbcpt-force'] ) ? $this->migrate_data( $post ) : json_decode( $post->post_content, true );

		$this->sanitize_labels( $settings );
		$this->parse_archive_slug( $settings );

		if ( $this->has_font_awesome( $settings ) ) {
			$this->add_font_awesome_hooks();
		}
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

			if ( 0 === strpos( $key, 'label_' ) ) {
				$key                    = str_replace( 'label_', '', $key );
				$args['labels'][ $key ] = $value;
			} else {
				$key          = str_replace( 'args_', '', $key );
				$args[ $key ] = $value;
			}
		}
		$this->change_key( $args, 'post_type', 'slug' );

		// Bypass new post types.
		if ( isset( $_GET['mbcpt-force'] ) && empty( $args['slug'] ) ) { // phpcs:ignore
			return json_decode( $post->post_content, true );
		}

		// Rewrite.
		$rewrite = [];
		if ( isset( $args['rewrite_slug'] ) ) {
			$rewrite['slug'] = $args['rewrite_slug'];
		}
		$rewrite['with_front'] = isset( $args['rewrite_no_front'] ) ? ! $args['rewrite_no_front'] : true;
		$args['rewrite']       = $rewrite;
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
		$revision         = (int) filter_input( INPUT_GET, 'revision' );

		$add_fields_link = '';
		$settings        = json_decode( $post->post_content, true );
		if ( defined( 'MBB_VER' ) && is_array( $settings ) && ! empty( $settings['slug'] ) ) {
			$link            = add_query_arg( [
				'post_type'              => 'meta-box',
				// Translators: %s - post type singular label.
				'post_title'             => sprintf( __( '%s Fields', 'mb-custom-post-type' ), $post->post_title ),
				'settings[object_type]'  => 'post',
				'settings[post_types][]' => $settings['slug'],
			], admin_url( 'post-new.php' ) );
			$add_fields_link = '<a href=' . esc_url( $link ) . '>' . __( 'Add custom fields to this post type', 'mb-custom-post-type' ) . ' &rarr;</a>';
		}

		$message = [
			0  => '', // Unused. Messages start at index 1.
			// translators: %s - post type singular label.
			1  => sprintf( __( '%s updated.', 'mb-custom-post-type' ), $label ),
			2  => __( 'Custom field updated.', 'mb-custom-post-type' ),
			3  => __( 'Custom field deleted.', 'mb-custom-post-type' ),
			// translators: %s - post type singular label.
			4  => sprintf( __( '%s updated.', 'mb-custom-post-type' ), $label ),
			// translators: %1$s: post type singular label, %2$s - revision title.
			5  => $revision ? sprintf( __( '%1$s restored to revision from %2$s.', 'mb-custom-post-type' ), $label, wp_post_revision_title( $revision, false ) ) : false,
			// translators: %1$s - post type singular label, %2$s - add fields link.
			6  => sprintf( __( '%1$s published. %2$s', 'mb-custom-post-type' ), $label, $add_fields_link ),
			// translators: %s - post type singular label.
			7  => sprintf( __( '%s saved.', 'mb-custom-post-type' ), $label ),
			// translators: %s - post type singular label.
			8  => sprintf( __( '%s submitted.', 'mb-custom-post-type' ), $label ),
			// translators: %1$s: post type singular label, %2$s - revision title.
			9  => sprintf( __( '%1$s scheduled for: <strong>%2$s</strong>.', 'mb-custom-post-type' ), $label, date_i18n( __( 'M j, Y @ G:i', 'mb-custom-post-type' ), strtotime( $post->post_date ) ) ),
			// translators: %s - post type singular label.
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
			$slug     = Arr::get( $settings, 'slug' );

			$messages[ $slug ] = $message;

			if ( ! Arr::get( $settings, 'publicly_queryable' ) ) {
				continue;
			}

			$permalink = get_permalink( $post->ID );

			// Translators: %s - post link, %s - view post text, %s - post type label.
			$view_link             = sprintf( ' <a href="%s">%s</a>.', esc_url( $permalink ), sprintf( __( 'View %s', 'mb-custom-post-type' ), $label_lower ) );
			$messages[ $slug ][1] .= $view_link;
			$messages[ $slug ][6] .= $view_link;
			$messages[ $slug ][9] .= $view_link;

			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			// Translators: %s - post link, %s - preview post text, %s - post type label.
			$preview_link           = sprintf( ' <a target="_blank" href="%s">%s</a>.', esc_url( $preview_permalink ), sprintf( __( 'Preview %s', 'mb-custom-post-type' ), $label_lower ) );
			$messages[ $slug ][8]  .= $preview_link;
			$messages[ $slug ][10] .= $preview_link;
		}

		$messages['mb-post-type'] = $message;
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
			$slug     = Arr::get( $settings, 'slug' );

			$singular = strtolower( Arr::get( $settings, 'labels.singular_name', '' ) );
			$plural   = strtolower( Arr::get( $settings, 'labels.name', '' ) );

			$bulk_messages[ $slug ] = [
				// Translators: %1$s - number of items, %2$s - post type label in singular or plural forms.
				'updated'   => sprintf( __( '%1$s %2$s updated.', 'mb-custom-post-type' ), $bulk_counts['updated'], $bulk_counts['updated'] > 1 ? $plural : $singular ),
				// Translators: %1$s - number of items, %2$s - post type label in singular or plural forms.
				'locked'    => sprintf( __( '%1$s %2$s not updated, somebody is editing.', 'mb-custom-post-type' ), $bulk_counts['locked'], $bulk_counts['locked'] > 1 ? $plural : $singular ),
				// Translators: %1$s - number of items, %2$s - post type label in singular or plural forms.
				'deleted'   => sprintf( __( '%1$s %2$s permanently deleted.', 'mb-custom-post-type' ), $bulk_counts['deleted'], $bulk_counts['deleted'] > 1 ? $plural : $singular ),
				// Translators: %1$s - number of items, %2$s - post type label in singular or plural forms.
				'trashed'   => sprintf( __( '%1$s %2$s moved to the Trash.', 'mb-custom-post-type' ), $bulk_counts['trashed'], $bulk_counts['trashed'] > 1 ? $plural : $singular ),
				// Translators: %1$s - number of items, %2$s - post type label in singular or plural forms.
				'untrashed' => sprintf( __( '%1$s %2$s restored from the Trash.', 'mb-custom-post-type' ), $bulk_counts['untrashed'], $bulk_counts['untrashed'] > 1 ? $plural : $singular ),
			];
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
		$plural_name   = sanitize_key( Arr::get( $settings, 'labels.name' ) );
		$singular_name = sanitize_key( Arr::get( $settings, 'labels.singular_name' ) );
		if ( $plural_name === $singular_name ) {
			$plural_name .= 's';
		}

		Arr::set( $settings, 'capability_type', [ $singular_name, $plural_name ] );
		Arr::set( $settings, 'map_meta_cap', true );
	}

	private function parse_supports( &$settings ) {
		if ( ! empty( Arr::get( $settings, 'supports' ) ) ) {
			return;
		}
		Arr::set( $settings, 'supports', false );
	}

	private function parse_icon( &$settings ) {
		$default = Arr::get( $settings, 'menu_icon', 'dashicons-admin-generic' );

		$icons = [
			'dashicons'    => Arr::get( $settings, 'icon' ),
			'svg'          => Arr::get( $settings, 'icon_svg' ),
			'custom'       => Arr::get( $settings, 'icon_custom' ),
			'font_awesome' => Arr::get( $settings, 'font_awesome' ),
		];
		$type  = Arr::get( $settings, 'icon_type', 'dashicons' );
		$icon  = Arr::get( $icons, $type ) ?: $default;
		if ( $type === 'font_awesome' ) {
			$icon = 'dashicons-' . $icon;
		}
		Arr::set( $settings, 'menu_icon', $icon );

		unset( $settings['icon_type'] );
		unset( $settings['icon'] );
		unset( $settings['icon_svg'] );
		unset( $settings['icon_custom'] );
		unset( $settings['font_awesome'] );
	}

	private function has_font_awesome( $settings ) {
		return Arr::get( $settings, 'icon_type', 'dashicons' ) === 'font_awesome';
	}

	private function add_font_awesome_hooks() {
		add_action( 'admin_init', [ $this, 'enqueue_font_awesome' ] );
		add_action( 'admin_menu', [ $this, 'filter_class_font_awesome' ] );
		add_action( 'adminmenu', [ $this, 'remove_filter_class_font_awesome' ] );
	}

	public function enqueue_font_awesome(): void {
		wp_enqueue_style( 'font-awesome', MB_CPT_URL . 'assets/fontawesome/css/all.min.css', [], '6.6.0' );
		wp_add_inline_style(
			'font-awesome',
			'.fa:before, fas, .fa-solid:before, .fab:before, .fa-brand:before, .far:before, .fa-regular:before {
				font-size: 16px;
				font-family: inherit;
				font-weight: inherit;
			}'
		);
	}

	public function filter_class_font_awesome() {
		add_filter( 'sanitize_html_class', [ $this, 'sanitize_html_class_font_awesome' ], 10, 2 );
	}

	public function remove_filter_class_font_awesome() {
		remove_filter( 'sanitize_html_class', [ $this, 'sanitize_html_class_font_awesome' ] );
	}

	public function sanitize_html_class_font_awesome( $classname, $fallback ) {
		$fa_classnames = [ 'fa', 'fas', 'fa-solid', 'fab', 'fa-brand', 'far', 'fa-regular' ];
		foreach ( $fa_classnames as $fa_classname ) {
			if ( str_contains( $fallback, $fa_classname ) ) {
				return str_replace( 'dashicons-', '', $fallback );
			}
		}
		return $classname;
	}

	public function fix_menu_positions(): void {
		foreach ( $this->menu_positions as $post_type => $settings ) {
			$this->fix_menu_position_for_post_type( $post_type, $settings );
		}
	}

	private function fix_menu_position_for_post_type( string $post_type, array $settings ): void {
		global $menu;

		$post_type_url = $post_type === 'post' ? 'edit.php' : "edit.php?post_type=$post_type";

		// Find the post type menu.
		foreach ( $menu as $position => $args ) {
			// Only process the menu of the post type.
			if ( $args[2] !== $post_type_url ) {
				continue;
			}

			// Remove the existing menu.
			unset( $menu[ $position ] );

			// Avoid the same position by adding a small number.
			// Same technique as in add_menu_page().
			$collision_avoider = (int) base_convert( substr( md5( serialize( $settings ) ), -4 ), 16, 10 ) * 0.00001; // phpcs:ignore
			$position          = (string) ( $settings['menu_position'] + $collision_avoider );

			// Re-add the menu with new position.
			$menu[ $position ] = $args; // phpcs:ignore
		}
	}
}
