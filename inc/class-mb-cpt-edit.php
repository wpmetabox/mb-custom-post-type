<?php

/**
 * This class controls all operations of Meta Box Custom Post Type extension
 * for creating / modifying custom post type.
 */
class MB_CPT_Edit
{
	/**
	 * @var bool Used to prevent duplicated calls like revisions, manual hook to wp_insert_post, etc.
	 */
	public $saved = false;

	/**
	 * Initiating
	 */
	public function __construct()
	{
		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		// Add meta box
		add_filter( 'rwmb_meta_boxes', array( $this, 'register_meta_boxes' ) );
		// Modify post information after save post
		add_action( 'save_post_mb-post-type', array( $this, 'save_post' ) );
		// Add ng-controller to form
		add_action( 'post_edit_form_tag', array( $this, 'add_ng_controller' ) );
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function enqueue_scripts()
	{
		if ( ! $this->is_mb_post_type() )
		{
			return;
		}

		wp_register_script( 'angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.4.2/angular.min.js', array(), '1.4.2', true );
		wp_enqueue_style( 'mb-cpt-css', MB_CPT_CSS_URL . 'styles.css', array(), '1.0.0', false );
		wp_enqueue_script( 'mb-cpt-js', MB_CPT_JS_URL . 'scripts.js', array( 'jquery', 'angular' ), '1.0.0', false );

		wp_localize_script( 'mb-cpt-js', 'MBPostTypeLabels', array(
			'add_new'            => __( 'Add New', 'mb-custom-post-type' ),
			'add_new_item'       => __( 'Add New ', 'mb-custom-post-type' ),
			'new_item'           => __( 'New ', 'mb-custom-post-type' ),
			'edit_item'          => __( 'Edit ', 'mb-custom-post-type' ),
			'view_item'          => __( 'View ', 'mb-custom-post-type' ),
			'update_item'        => __( 'Update ', 'mb-custom-post-type' ),
			'all_items'          => __( 'All ', 'mb-custom-post-type' ),
			'search_items'       => __( 'Search ', 'mb-custom-post-type' ),
			'parent_item_colon'  => __( 'Parent ', 'mb-custom-post-type' ),
			'no'                 => __( 'No ', 'mb-custom-post-type' ),
			'not_found'          => __( ' found.', 'mb-custom-post-type' ),
			'not_found_in_trash' => __( ' found in Trash.', 'mb-custom-post-type' ),
		) );
	}

	/**
	 * Register meta boxes for add/edit mb-post-type page
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	public function register_meta_boxes( $meta_boxes )
	{
		$label_prefix = 'label_';
		$args_prefix  = 'args_';

		$basic_fields    = array(
			array(
				'name'        => __( 'Plural Name', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'name',
				'type'        => 'text',
				'placeholder' => __( 'Plural Name', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Singular Name', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'singular_name',
				'type'        => 'text',
				'placeholder' => __( 'Singular Name', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Slug', 'mb-custom-post-type' ),
				'id'          => $args_prefix . 'post_type',
				'type'        => 'text',
				'placeholder' => __( 'Slug', 'mb-custom-post-type' ),
			),
		);
		$labels_fields   = array(
			array(
				'name'        => __( 'Menu Name', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'menu_name',
				'type'        => 'text',
				'placeholder' => __( 'Menu Name', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Name Admin Bar', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'name_admin_bar',
				'type'        => 'text',
				'placeholder' => __( 'Name Admin Bar', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Parent Items:', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'parent_item_colon',
				'type'        => 'text',
				'placeholder' => __( 'Parent Items:', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'All Items', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'all_items',
				'type'        => 'text',
				'placeholder' => __( 'All Items', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Add New Item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'add_new_item',
				'type'        => 'text',
				'placeholder' => __( 'Add New Item', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Add New', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'add_new',
				'type'        => 'text',
				'placeholder' => __( 'Add New', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'New Item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'new_item',
				'type'        => 'text',
				'placeholder' => __( 'New Item', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Edit Item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'edit_item',
				'type'        => 'text',
				'placeholder' => __( 'Edit Item', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Update Item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'update_item',
				'type'        => 'text',
				'placeholder' => __( 'Update Item', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'View Item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'view_item',
				'type'        => 'text',
				'placeholder' => __( 'View Item', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Search Items', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'search_items',
				'type'        => 'text',
				'placeholder' => __( 'Search Items', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Not found', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'not_found',
				'type'        => 'text',
				'placeholder' => __( 'Not found', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Not found in Trash', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'not_found_in_trash',
				'type'        => 'text',
				'placeholder' => __( 'Not found in Trash', 'mb-custom-post-type' ),
			),
		);
		$advanced_fields = array(
			array(
				'name'    => __( 'Menu Icon', 'mb-custom-post-type' ),
				'id'      => $args_prefix . 'menu_icon',
				'type'    => 'radio',
				'options' => mb_cpt_get_dashicons(),
			),
			array(
				'name'        => __( 'Description', 'mb-custom-post-type' ),
				'id'          => $args_prefix . 'description',
				'type'        => 'textarea',
				'placeholder' => __( 'Description', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Public', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'public',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Allow the post type appear in the Frontend', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Publicly Queryable', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'publicly_queryable',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether post type queries can be performed from the front end.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show UI', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_ui',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether to show the post type in the admin menu and where to show that menu. Note that show_ui must be true.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show In Menu', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_menu',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether post type is available for selection in menus.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Query Var', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'query_var',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'False to prevent queries, or string value of the query var to use for this post type.', 'mb-custom-post-type' ),
			),
			array(
				'name'    => __( 'Capability Type', 'mb-custom-post-type' ),
				'id'      => $args_prefix . 'capability_type',
				'type'    => 'select',
				'options' => array(
					'post' => __( 'Post', 'mb-custom-post-type' ),
					'page' => __( 'Page', 'mb-custom-post-type' ),
				)
			),
			array(
				'name' => __( 'Has Archive', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'has_archive',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Allow to have custom archive slug for CPT.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Hierarchical', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'hierarchical',
				'type' => 'checkbox',
				'desc' => __( 'Whether the post type is hierarchical. Allows Parent to be specified.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Can Export', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'can_export',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Can this post type be exported.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show In Nav Menus', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_nav_menus',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether post type is available for selection in navigation menus.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Exclude From Search', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'exclude_from_search',
				'type' => 'checkbox',
				'desc' => __( 'Whether to exclude posts with this post type from search results.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Menu Position', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'menu_position',
				'type' => 'number',
			),
		);

		// Basic settings
		$meta_boxes[] = array(
			'id'         => 'basic-settings',
			'title'      => __( 'Basic Settings', 'mb-custom-post-type' ),
			'pages'      => array( 'mb-post-type' ),
			'fields'     => array_merge(
				$basic_fields,
				array(
					array(
						'id'   => 'btn-toggle-advanced',
						'type' => 'button',
						'std'  => __( 'Advanced', 'mb-custom-post-type' ),
					),
				)
			),
			'validation' => array(
				'rules'    => array(
					$label_prefix . 'name'          => array(
						'required' => true,
					),
					$label_prefix . 'singular_name' => array(
						'required' => true,
					),
					$args_prefix . 'post_type'      => array(
						'required' => true,
					),
				),
				'messages' => array(
					$label_prefix . 'name'          => array(
						'required' => __( 'Plural name is required', 'mb-custom-post-type' ),
					),
					$label_prefix . 'singular_name' => array(
						'required' => __( 'Singular name is required', 'mb-custom-post-type' ),
					),
					$args_prefix . 'post_type'      => array(
						'required' => __( 'Slug is required', 'mb-custom-post-type' ),
					),
				)
			),
		);

		// Labels settings
		$meta_boxes[] = array(
			'id'     => 'label-settings',
			'title'  => __( 'Labels Settings', 'mb-custom-post-type' ),
			'pages'  => array( 'mb-post-type' ),
			'fields' => $labels_fields,
		);

		// Advanced settings
		$meta_boxes[] = array(
			'id'     => 'advanced-settings',
			'title'  => __( 'Advanced Settings', 'mb-custom-post-type' ),
			'pages'  => array( 'mb-post-type' ),
			'fields' => $advanced_fields,
		);

		// Supports
		$meta_boxes[] = array(
			'id'       => 'supports',
			'title'    => __( 'Supports', 'mb-custom-post-type' ),
			'pages'    => array( 'mb-post-type' ),
			'priority' => 'low',
			'context'  => 'side',
			'fields'   => array(
				array(
					'id'      => $args_prefix . 'supports',
					'type'    => 'checkbox_list',
					'options' => array(
						'title'           => __( 'Title', 'mb-custom-post-type' ),
						'editor'          => __( 'Editor', 'mb-custom-post-type' ),
						'author'          => __( 'Author', 'mb-custom-post-type' ),
						'thumbnail'       => __( 'Thumbnail', 'mb-custom-post-type' ),
						'excerpt'         => __( 'Excerpt', 'mb-custom-post-type' ),
						'trackbacks'      => __( 'Trackbacks', 'mb-custom-post-type' ),
						'comments'        => __( 'Comments', 'mb-custom-post-type' ),
						'revisions'       => __( 'Revisions', 'mb-custom-post-type' ),
						'page-attributes' => __( 'Page Attributes', 'mb-custom-post-type' ),
					),
				),
			),
		);

		$fields = array_merge( $basic_fields, $labels_fields, $advanced_fields );

		// Add ng-model attribute to all fields
		foreach ( $fields as $field )
		{
			add_filter( 'rwmb_' . $field['id'] . '_html', array( $this, 'modify_field_html' ), 10, 3 );
		}

		return $meta_boxes;
	}

	/**
	 * Modify html output of field
	 *
	 * @param string $field_html
	 * @param array  $field
	 * @param string $meta
	 *
	 * @return string
	 */
	public function modify_field_html( $field_html, $field, $meta )
	{
		if ( 'args_menu_icon' == $field['id'] )
		{
			$field_html = '';
			$icons      = mb_cpt_get_dashicons();
			foreach ( $icons as $icon )
			{
				$field_html .= sprintf( '
					<label class="icon-single%s">
						<i class="wp-menu-image dashicons-before %s"></i>
						<input type="radio" name="args_menu_icon" value="%s" class="hidden"%s>
					</label>',
					$icon == $meta ? ' active' : '',
					$icon,
					$icon,
					checked( $icon, $meta, false )
				);
			}
		}
		elseif ( false === strpos( $field['id'], 'args' ) || 'args_post_type' == $field['id'] )
		{
			$meta       = "'$meta'";
			$field_html = str_replace( '>', ' ng-model="' . $field['id'] . '" ng-init="' . $field['id'] . ' = ' . $meta . '" >', $field_html );
		}
		return $field_html;
	}

	/**
	 * Modify post information and post meta after save post
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function save_post( $post_id )
	{
		// If label_singular_name is empty or if this function is called to prevent duplicated calls like revisions, manual hook to wp_insert_post, etc.
		if ( empty( $_POST['label_singular_name'] ) || true === $this->saved )
		{
			return;
		}

		$this->saved = true;

		// Update post title
		$post = array(
			'ID'         => $post_id,
			'post_title' => $_POST['label_singular_name'],
		);

		wp_update_post( $post );

		// Flush rewrite rules after create new or edit post types
		flush_rewrite_rules();
	}

	/**
	 * Check if current link is mb-post-type post type or not
	 *
	 * @return boolean
	 */
	public function is_mb_post_type()
	{
		$screen = get_current_screen();
		return 'post' === $screen->base && 'mb-post-type' === $screen->post_type;
	}

	/**
	 * Add angular controller to form tag
	 *
	 * @return void
	 */
	public function add_ng_controller()
	{
		if ( $this->is_mb_post_type() )
		{
			echo 'ng-controller="PostTypeController"';
		}
	}
}
