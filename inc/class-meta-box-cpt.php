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
	public $mb_cpt_saved = false;

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

		wp_enqueue_style( 'mb-cpt-css', MB_CPT_CSS_URL . 'styles.css', array(), '1.0.0', false );
		wp_enqueue_script( 'mb-cpt-js', MB_CPT_JS_URL . 'scripts.js', array( 'jquery' ), '1.0.0', false );
	}

	/**
	 * Register custom post types
	 *
	 * @return void
	 */
	public function register_post_types()
	{
		// Get all registered custom post types
		$cpts = $this->get_all_registered_post_types();

		foreach ( $cpts as $cpt )
		{
			register_post_type( $cpt['post_type'], $cpt );
		}
	}

	/**
	 * Get all registered post types
	 *
	 * @return array
	 */
	public function get_all_registered_post_types()
	{
		// This array stores all registered custom post types
		$cpts = array();

		// Create mb-post-type post type to management/add/edit custom post types
		$cpts[] = $this->set_up_post_type(
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
		$mb_cpts = get_posts( array(
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'post_type'      => 'mb-post-type',
		) );

		foreach ( $mb_cpts as $cpt )
		{
			// Get all post meta from current post
			$post_meta  = get_post_meta( $cpt->ID );
			// Create array that contains Labels of this current custom post type
			$labels     = array();
			// Create array that contains arguments of this current custom post type
			$args       = array();

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

			$cpts[] = $this->set_up_post_type( $labels, $args );
		}

		return $cpts;
	}

	/**
	 * Setup labels, arguments for a custom post type
	 *
	 * @param array     $labels
	 * @param array     $args
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
			'description'        => sprintf( __( '%s GUI', 'mb-cpt' ), $labels['name'] ),
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
			'taxonomies'          => array(),
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
		$label_prefix   = 'label_';
		$args_prefix    = 'args_';

		// Labels meta box
		$meta_boxes[] = array(
			'id'       => 'labels',
			'title'    => __( 'Labels', 'mb-cpt' ),
			'pages'    => array( 'mb-post-type' ),
			'context'  => 'normal',
			'priority' => 'high',
			'fields'   => array(
				array(
					'name'        => __( 'Plural Name', 'mb-cpt' ),
					'id'          => $label_prefix . 'name',
					'type'        => 'text',
					'placeholder' => __( 'Plural Name', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'Singular Name', 'mb-cpt' ),
					'id'          => $label_prefix . 'singular_name',
					'type'        => 'text',
					'placeholder' => __( 'Singular Name', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'Menu Name', 'mb-cpt' ),
					'id'          => $label_prefix . 'menu_name',
					'type'        => 'text',
					'placeholder' => __( 'Menu Name', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'Name Admin Bar', 'mb-cpt' ),
					'id'          => $label_prefix . 'name_admin_bar',
					'type'        => 'text',
					'placeholder' => __( 'Name Admin Bar', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'Parent Items:', 'mb-cpt' ),
					'id'          => $label_prefix . 'parent_item_colon',
					'type'        => 'text',
					'placeholder' => __( 'Parent Items:', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'All Items', 'mb-cpt' ),
					'id'          => $label_prefix . 'all_items',
					'type'        => 'text',
					'placeholder' => __( 'All Items', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'Add New Item', 'mb-cpt' ),
					'id'          => $label_prefix . 'add_new_item',
					'type'        => 'text',
					'placeholder' => __( 'Add New Item', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'Add New', 'mb-cpt' ),
					'id'          => $label_prefix . 'add_new',
					'type'        => 'text',
					'placeholder' => __( 'Add New', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'New Item', 'mb-cpt' ),
					'id'          => $label_prefix . 'new_item',
					'type'        => 'text',
					'placeholder' => __( 'New Item', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'Edit Item', 'mb-cpt' ),
					'id'          => $label_prefix . 'edit_item',
					'type'        => 'text',
					'placeholder' => __( 'Edit Item', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'Update Item', 'mb-cpt' ),
					'id'          => $label_prefix . 'update_item',
					'type'        => 'text',
					'placeholder' => __( 'Update Item', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'View Item', 'mb-cpt' ),
					'id'          => $label_prefix . 'view_item',
					'type'        => 'text',
					'placeholder' => __( 'View Item', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'Search Items', 'mb-cpt' ),
					'id'          => $label_prefix . 'search_items',
					'type'        => 'text',
					'placeholder' => __( 'Search Items', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'Not found', 'mb-cpt' ),
					'id'          => $label_prefix . 'not_found',
					'type'        => 'text',
					'placeholder' => __( 'Not found', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'Not found in Trash', 'mb-cpt' ),
					'id'          => $label_prefix . 'not_found_in_trash',
					'type'        => 'text',
					'placeholder' => __( 'Not found in Trash', 'mb-cpt' ),
					'size'        => 50,
				),
			),
			'validation'    => array(
				'rules'     => array(
					$label_prefix . 'name'          => array(
						'required'  => true,
					),
					$label_prefix . 'singular_name' => array(
						'required'  => true,
					),
				),
				'messages'  => array(
					$label_prefix . 'name'          => array(
						'required'  => __( 'Plural Name is required', 'mb-cpt' ),
					),
					$label_prefix . 'singular_name' => array(
						'required'  => __( 'Singular Name is required', 'mb-cpt' ),
					),
				)
			)
		);

		// Arguments meta box
		$meta_boxes[] = array(
			'id'            => 'arguments',
			'title'         => __( 'Arguments', 'mb-cpt' ),
			'pages'         => array( 'mb-post-type' ),
			'context'       => 'normal',
			'priority'      => 'high',
			'fields'        => array(
				array(
					'name'        => __( 'Slug', 'mb-cpt' ),
					'id'          => $args_prefix . 'post_type',
					'type'        => 'text',
					'placeholder' => __( 'Slug', 'mb-cpt' ),
					'size'        => 50,
				),
				array(
					'name'        => __( 'Description', 'mb-cpt' ),
					'id'          => $args_prefix . 'description',
					'type'        => 'textarea',
					'placeholder' => __( 'Description', 'mb-cpt' ),
				),
				array(
					'name'    => __( 'Icon', 'mb-cpt' ),
					'id'      => $args_prefix . 'menu_icon',
					'type'    => 'select',
					'options' => sl_icons(),
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
					'desc' => __( 'Whether post_type queries can be performed from the front end.', 'mb-cpt' ),
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
					'desc' => __( 'Whether post_type is available for selection in menus.', 'mb-cpt' ),
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
					'desc' => __( 'Can this post_type be exported.', 'mb-cpt' ),
				),
				array(
					'name' => __( 'Show In Nav Menus', 'mb-cpt' ),
					'id'   => $args_prefix . 'show_in_nav_menus',
					'type' => 'checkbox',
					'std'  => 1,
					'desc' => __( 'Whether post_type is available for selection in navigation menus.', 'mb-cpt' ),
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
			),
			'validation'    => array(
				'rules'     => array(
					$args_prefix . 'post_type'  => array(
						'required'  => true,
					),
				),
				'messages'  => array(
					$args_prefix . 'post_type'  => array(
						'required'  => __( 'Slug is required', 'mb-cpt' ),
					),
				)
			)
		);

		// Supports meta box
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
						'title'             => __( 'Title', 'mb-cpt' ),
						'editor'            => __( 'Editor', 'mb-cpt' ),
						'author'            => __( 'Author', 'mb-cpt' ),
						'thumbnail'         => __( 'Thumbnail', 'mb-cpt' ),
						'excerpt'           => __( 'Excerpt', 'mb-cpt' ),
						'trackbacks'        => __( 'Trackbacks', 'mb-cpt' ),
						'comments'          => __( 'Comments', 'mb-cpt' ),
						'revisions'         => __( 'Revisions', 'mb-cpt' ),
						'post-formats'      => __( 'Post Formats', 'mb-cpt' ),
						'page-attributes'   => __( 'Page Attributes', 'mb-cpt' ),
					),
				),
			),
		);

		// Get all taxonomies in Site
		$taxonomies = get_taxonomies( array(), 'objects' );
		$taxes       = array();
		foreach ( $taxonomies as $tax )
		{
			$taxes[$tax->rewrite['slug']] = $tax->labels->singular_name;
		}

		// Taxonomies meta box
		$meta_boxes[] = array(
			'id'       => 'taxonomies',
			'title'    => __( 'Taxonomies', 'mb-cpt' ),
			'pages'    => array( 'mb-post-type' ),
			'priority' => 'low',
			'context'  => 'side',
			'fields'   => array(
				array(
					'name'    => __( 'Registered taxonomies that will be used with this post type.', 'mb-cpt' ),
					'id'      => $args_prefix . 'taxonomies',
					'type'    => 'checkbox_list',
					'options' => $taxes,
				),
			),
		);

		return $meta_boxes;
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
		if ( 'mb-post-type' !== get_post_type( $post_id ) || empty( $_POST['label_singular_name'] ) || true === $this->mb_cpt_saved )
		{
			return;
		}

		$this->mb_cpt_saved = true;

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
		_e ( 'Meta Box Custom Post Type requires Meta Box plugin to work. Please install it.', 'mb-cpt' );
		echo '</div>';
	}

	/**
	 * Check if current link is mb-post-type post type or not
	 *
	 * @return boolean
	 */
	public function is_mb_post_type()
	{
		if ( isset( $_GET['post'] ) )
		{
			$post = get_post( $_GET['post'] );

			return ( ! empty( $post ) && $post->post_type === 'mb-post-type' );
		}

		return ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'mb-post-type' );
	}
}