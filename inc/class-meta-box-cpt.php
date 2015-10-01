<?php

/**
 * This class controls all operations of Meta Box Custom Post Type extension
 * for creating / modifying custom post type.
 */
class Meta_Box_CPT
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
		// Register post types
		add_action( 'init', array( $this, 'register_post_types' ), 0 );
		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		// Add notice if Meta Box Plugin wasn't activated
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		// Add meta box
		add_filter( 'rwmb_meta_boxes', array( $this, 'register_meta_boxes' ) );
		// Modify post information after save post
		add_action( 'save_post', array( $this, 'save_post' ) );
		// Change the output of post/bulk post updated messages
		add_filter( 'post_updated_messages', array( $this, 'updated_message' ), 10, 1 );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_updated_messages' ), 10, 2 );
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
	 * Register meta boxes for add/edit mb-post-type page
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	public function register_meta_boxes( $meta_boxes )
	{
		$label_prefix    = 'label_';
		$args_prefix     = 'args_';
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
		$advanced_fields = array(
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
			array(
				'name'    => __( 'Menu Icon', 'mb-cpt' ),
				'id'      => $args_prefix . 'menu_icon',
				'type'    => 'radio',
				'options' => mb_cpt_get_dashicons(),
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
						'std'  => __( 'Advanced Settings', 'mb-cpt' ),
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

		// Advance settings
		$meta_boxes[] = array(
			'id'     => 'advanced-settings',
			'title'  => __( 'Advanced Settings', 'mb-cpt' ),
			'pages'  => array( 'mb-post-type' ),
			'fields' => array_merge(
				$advanced_fields,
				array(
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
				)
			),
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

		$fields = array_merge( $basic_fields, $advanced_fields );

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
		else
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
	 * Custom updated wordpress messages
	 *
	 * @param array $messages
	 *
	 * @return array
	 */
	public function updated_message( $messages )
	{
		$post = get_post();

		$messages['mb-post-type'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf(
				__( '%s updated.', 'mb-post-type' ),
				$post->post_title
			),
			2  => __( 'Custom field updated.', 'mb-post-type' ),
			3  => __( 'Custom field deleted.', 'mb-post-type' ),
			4  => sprintf(
				__( '%s updated.', 'mb-post-type' ),
				$post->post_title
			),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( '%s restored to revision from %s', 'mb-post-type' ), $post->post_title, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf(
				__( '%s updated.', 'mb-post-type' ),
				$post->post_title
			),
			7  => sprintf(
				__( '%s updated.', 'mb-post-type' ),
				$post->post_title
			),
			8  => sprintf(
				__( '%s submitted.', 'mb-post-type' ),
				$post->post_title
			),
			9  => sprintf(
				__( '%s scheduled for: <strong>%s</strong>.', 'mb-post-type' ),
				$post->post_title,
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'mb-post-type' ), strtotime( $post->post_date ) )
			),
			10 => sprintf(
				__( '%s draft updated.', 'mb-post-type' ),
				$post->post_title
			),
		);

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
		$bulk_messages['mb-post-type'] = array(
			'updated'   => sprintf( _n( '%s post type updated.', '%s post types updated.', $bulk_counts['updated'], 'mb-cpt' ), $bulk_counts['updated'] ),
			'locked'    => sprintf( _n( '%s post type not updated, somebody is editing.', '%s post types not updated, somebody is editing.', $bulk_counts['locked'], 'mb-cpt' ), $bulk_counts['locked'] ),
			'deleted'   => sprintf( _n( '%s post type permanently deleted.', '%s post types permanently deleted.', $bulk_counts['deleted'], 'mb-cpt' ), $bulk_counts['deleted'] ),
			'trashed'   => sprintf( _n( '%s post type moved to the Trash.', '%s post types moved to the Trash.', $bulk_counts['trashed'], 'mb-cpt' ), $bulk_counts['trashed'] ),
			'untrashed' => sprintf( _n( '%s post type restored from the Trash.', '%s post types restored from the Trash.', $bulk_counts['untrashed'], 'mb-cpt' ), $bulk_counts['untrashed'] ),
		);

		return $bulk_messages;
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
