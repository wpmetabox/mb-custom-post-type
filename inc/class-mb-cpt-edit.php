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
		// Add notice if Meta Box Plugin wasn't activated
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		// Add meta box
		add_filter( 'rwmb_meta_boxes', array( $this, 'register_meta_boxes' ) );
		// Modify post information after save post
		add_action( 'save_post', array( $this, 'save_post' ) );
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

		wp_localize_script( 'mb-cpt-js', 'SlLabels', array(
			'add_new'            => __( 'Add New', 'mb-cpt' ),
			'add_new_item'       => __( 'Add New ', 'mb-cpt' ),
			'new_item'           => __( 'New ', 'mb-cpt' ),
			'edit_item'          => __( 'Edit ', 'mb-cpt' ),
			'view_item'          => __( 'View ', 'mb-cpt' ),
			'update_item'        => __( 'Update ', 'mb-cpt' ),
			'all_items'          => __( 'All ', 'mb-cpt' ),
			'search_items'       => __( 'Search ', 'mb-cpt' ),
			'parent_item_colon'  => __( 'Parent ', 'mb-cpt' ),
			'no'                 => __( 'No ', 'mb-cpt' ),
			'not_found'          => __( ' found.', 'mb-cpt' ),
			'not_found_in_trash' => __( ' found in Trash.', 'mb-cpt' ),
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
				'name'        => __( 'Plural Name', 'mb-cpt' ),
				'id'          => $label_prefix . 'name',
				'type'        => 'text',
				'placeholder' => __( 'Plural Name', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'Singular Name', 'mb-cpt' ),
				'id'          => $label_prefix . 'singular_name',
				'type'        => 'text',
				'placeholder' => __( 'Singular Name', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'Slug', 'mb-cpt' ),
				'id'          => $args_prefix . 'post_type',
				'type'        => 'text',
				'placeholder' => __( 'Slug', 'mb-cpt' ),
			),
		);
		$labels_fields   = array(
			array(
				'name'        => __( 'Menu Name', 'mb-cpt' ),
				'id'          => $label_prefix . 'menu_name',
				'type'        => 'text',
				'placeholder' => __( 'Menu Name', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'Name Admin Bar', 'mb-cpt' ),
				'id'          => $label_prefix . 'name_admin_bar',
				'type'        => 'text',
				'placeholder' => __( 'Name Admin Bar', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'Parent Items:', 'mb-cpt' ),
				'id'          => $label_prefix . 'parent_item_colon',
				'type'        => 'text',
				'placeholder' => __( 'Parent Items:', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'All Items', 'mb-cpt' ),
				'id'          => $label_prefix . 'all_items',
				'type'        => 'text',
				'placeholder' => __( 'All Items', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'Add New Item', 'mb-cpt' ),
				'id'          => $label_prefix . 'add_new_item',
				'type'        => 'text',
				'placeholder' => __( 'Add New Item', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'Add New', 'mb-cpt' ),
				'id'          => $label_prefix . 'add_new',
				'type'        => 'text',
				'placeholder' => __( 'Add New', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'New Item', 'mb-cpt' ),
				'id'          => $label_prefix . 'new_item',
				'type'        => 'text',
				'placeholder' => __( 'New Item', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'Edit Item', 'mb-cpt' ),
				'id'          => $label_prefix . 'edit_item',
				'type'        => 'text',
				'placeholder' => __( 'Edit Item', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'Update Item', 'mb-cpt' ),
				'id'          => $label_prefix . 'update_item',
				'type'        => 'text',
				'placeholder' => __( 'Update Item', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'View Item', 'mb-cpt' ),
				'id'          => $label_prefix . 'view_item',
				'type'        => 'text',
				'placeholder' => __( 'View Item', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'Search Items', 'mb-cpt' ),
				'id'          => $label_prefix . 'search_items',
				'type'        => 'text',
				'placeholder' => __( 'Search Items', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'Not found', 'mb-cpt' ),
				'id'          => $label_prefix . 'not_found',
				'type'        => 'text',
				'placeholder' => __( 'Not found', 'mb-cpt' ),
			),
			array(
				'name'        => __( 'Not found in Trash', 'mb-cpt' ),
				'id'          => $label_prefix . 'not_found_in_trash',
				'type'        => 'text',
				'placeholder' => __( 'Not found in Trash', 'mb-cpt' ),
			),
		);
		$advanced_fields = array(
			array(
				'name'    => __( 'Menu Icon', 'mb-cpt' ),
				'id'      => $args_prefix . 'menu_icon',
				'type'    => 'radio',
				'options' => mb_cpt_get_dashicons(),
			),
			array(
				'name'        => __( 'Description', 'mb-cpt' ),
				'id'          => $args_prefix . 'description',
				'type'        => 'textarea',
				'placeholder' => __( 'Description', 'mb-cpt' ),
			),
			array(
				'name' => __( 'Public', 'mb-cpt' ),
				'id'   => $args_prefix . 'public',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Allow the post type appear in the Frontend', 'mb-cpt' ),
			),
			array(
				'name' => __( 'Publicly Queryable', 'mb-cpt' ),
				'id'   => $args_prefix . 'publicly_queryable',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether post type queries can be performed from the front end.', 'mb-cpt' ),
			),
			array(
				'name' => __( 'Show UI', 'mb-cpt' ),
				'id'   => $args_prefix . 'show_ui',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether to show the post type in the admin menu and where to show that menu. Note that show_ui must be true.', 'mb-cpt' ),
			),
			array(
				'name' => __( 'Show In Menu', 'mb-cpt' ),
				'id'   => $args_prefix . 'show_in_menu',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether post type is available for selection in menus.', 'mb-cpt' ),
			),
			array(
				'name' => __( 'Query Var', 'mb-cpt' ),
				'id'   => $args_prefix . 'query_var',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'False to prevent queries, or string value of the query var to use for this post type.', 'mb-cpt' ),
			),
			array(
				'name'    => __( 'Capability Type', 'mb-cpt' ),
				'id'      => $args_prefix . 'capability_type',
				'type'    => 'select',
				'options' => array(
					'post' => __( 'Post', 'mb-cpt' ),
					'page' => __( 'Page', 'mb-cpt' ),
				)
			),
			array(
				'name' => __( 'Has Archive', 'mb-cpt' ),
				'id'   => $args_prefix . 'has_archive',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Allow to have custom archive slug for CPT.', 'mb-cpt' ),
			),
			array(
				'name' => __( 'Hierarchical', 'mb-cpt' ),
				'id'   => $args_prefix . 'hierarchical',
				'type' => 'checkbox',
				'desc' => __( 'Whether the post type is hierarchical. Allows Parent to be specified.', 'mb-cpt' ),
			),
			array(
				'name' => __( 'Can Export', 'mb-cpt' ),
				'id'   => $args_prefix . 'can_export',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Can this post type be exported.', 'mb-cpt' ),
			),
			array(
				'name' => __( 'Show In Nav Menus', 'mb-cpt' ),
				'id'   => $args_prefix . 'show_in_nav_menus',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether post type is available for selection in navigation menus.', 'mb-cpt' ),
			),
			array(
				'name' => __( 'Exclude From Search', 'mb-cpt' ),
				'id'   => $args_prefix . 'exclude_from_search',
				'type' => 'checkbox',
				'desc' => __( 'Whether to exclude posts with this post type from search results.', 'mb-cpt' ),
			),
			array(
				'name' => __( 'Menu Position', 'mb-cpt' ),
				'id'   => $args_prefix . 'menu_position',
				'type' => 'number',
			),
		);

		// Basic settings
		$meta_boxes[] = array(
			'id'         => 'basic-settings',
			'title'      => __( 'Basic Settings', 'mb-cpt' ),
			'pages'      => array( 'mb-post-type' ),
			'fields'     => array_merge(
				$basic_fields,
				array(
					array(
						'id'   => 'btn-toggle-advanced',
						'type' => 'button',
						'std'  => __( 'Advanced', 'mb-cpt' ),
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
						'required' => __( 'Plural name is required', 'mb-cpt' ),
					),
					$label_prefix . 'singular_name' => array(
						'required' => __( 'Singular name is required', 'mb-cpt' ),
					),
					$args_prefix . 'post_type'      => array(
						'required' => __( 'Slug is required', 'mb-cpt' ),
					),
				)
			),
		);

		// Labels settings
		$meta_boxes[] = array(
			'id'     => 'label-settings',
			'title'  => __( 'Labels Settings', 'mb-cpt' ),
			'pages'  => array( 'mb-post-type' ),
			'fields' => $labels_fields,
		);

		// Advanced settings
		$meta_boxes[] = array(
			'id'     => 'advanced-settings',
			'title'  => __( 'Advanced Settings', 'mb-cpt' ),
			'pages'  => array( 'mb-post-type' ),
			'fields' => $advanced_fields,
		);

		// Supports
		$meta_boxes[] = array(
			'id'       => 'supports',
			'title'    => __( 'Supports', 'mb-cpt' ),
			'pages'    => array( 'mb-post-type' ),
			'priority' => 'low',
			'context'  => 'side',
			'fields'   => array(
				array(
					'id'      => $args_prefix . 'supports',
					'type'    => 'checkbox_list',
					'options' => array(
						'title'           => __( 'Title', 'mb-cpt' ),
						'editor'          => __( 'Editor', 'mb-cpt' ),
						'author'          => __( 'Author', 'mb-cpt' ),
						'thumbnail'       => __( 'Thumbnail', 'mb-cpt' ),
						'excerpt'         => __( 'Excerpt', 'mb-cpt' ),
						'trackbacks'      => __( 'Trackbacks', 'mb-cpt' ),
						'comments'        => __( 'Comments', 'mb-cpt' ),
						'revisions'       => __( 'Revisions', 'mb-cpt' ),
						'page-attributes' => __( 'Page Attributes', 'mb-cpt' ),
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
		// If post type of saved post is not mb-post-type or label_singular_name is empty
		// or if this function is called to prevent duplicated calls like revisions, manual hook to wp_insert_post, etc.
		if ( 'mb-post-type' !== get_post_type( $post_id ) || empty( $_POST['label_singular_name'] ) || true === $this->saved )
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
	}

	/**
	 * Notice when Meta Box plugin is not installed
	 *
	 * @return string
	 */
	public function admin_notice()
	{
		if ( class_exists( 'RW_Meta_Box' ) )
		{
			return;
		}

		echo '<div class="error">';
		_e( 'Meta Box Custom Post Type requires Meta Box plugin to work. Please install it.', 'mb-cpt' );
		echo '</div>';
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
