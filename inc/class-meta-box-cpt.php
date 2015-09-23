<?php
/**
 * This class controls all operations of Meta Box Custom Post Type extension
 * for creating / modifying custom post type.
 */
class Meta_Box_CPT
{
	/**
	 * Initial
	 */
	public function __construct()
	{
		// Register post types
		add_action( 'init', array( $this, 'register_post_type' ), 0 );
		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		// Add notice if Meta Box Plugin wasn't activated
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		// Add meta box
		add_filter( 'rwmb_meta_boxes', array( $this, 'register_meta_boxes' ) );
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
		wp_enqueue_script( 'mb-cpt-js', MB_CPT_JS_URL . 'scripts.js', array(), '1.0.0', false );
	}

	/**
	 * Register custom post type
	 *
	 * @return void
	 */
	public function register_post_type()
	{
		// Get all registered custom post types
		$cpts = $this->get_all_registered_post_types();

		foreach ( $cpts as $cpt )
		{
			$post_type = $cpt['post_type'];

			$labels = array(
				'name'               => $cpt['name'],
				'singular_name'      => $cpt['singular_name'],
				'menu_name'          => $cpt['menu_name'],
				'name_admin_bar'     => $cpt['name_admin_bar'],
				'add_new'            => $cpt['add_new'],
				'add_new_item'       => $cpt['add_new_item'],
				'new_item'           => $cpt['new_item'],
				'edit_item'          => $cpt['edit_item'],
				'view_item'          => $cpt['view_item'],
				'update_item'        => $cpt['update_item'],
				'all_items'          => $cpt['all_items'],
				'search_items'       => $cpt['search_items'],
				'parent_item_colon'  => $cpt['parent_item_colon'],
				'not_found'          => $cpt['not_found'],
				'not_found_in_trash' => $cpt['not_found_in_trash'],
			);

			$args = array(
				'labels'          => $labels,
				'descriptions'    => $cpt['descriptions'],
				'public'          => $cpt['public'],
				'show_ui'         => $cpt['show_ui'],
				'show_in_menu'    => $cpt['show_in_menu'],
				'query_var'       => $cpt['query_var'],
				'rewrite'         => $cpt['rewrite'],
				'capability_type' => $cpt['capability_type'],
				'hierarchical'    => $cpt['hierarchical'],
				'menu_position'   => $cpt['menu_position'],
				'supports'        => $cpt['supports'],
				'menu_icon'       => $cpt['menu_icon'],
			);

			register_post_type( $post_type, $args );
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
		$cpts   = array();
		$mb_cpt = array(
			'post_type'          => 'mb-post-type',
			'name'               => _x('MB Post Types', 'mb-cpt'),
			'singular_name'      => _x('MB Post Type', 'mb-cpt'),
			'menu_name'          => _x('MB Post Types', 'mb-cpt'),
			'name_admin_bar'     => _x('MB Post Type', 'mb-cpt'),
			'add_new'            => _x('Add New', 'meta-box', 'mb-cpt'),
			'add_new_item'       => __('Add New Post Type', 'mb-cpt'),
			'new_item'           => __('New Post Type', 'mb-cpt'),
			'edit_item'          => __('Edit Post Type', 'mb-cpt'),
			'update_item'        => __('Update Post Type', 'mb-cpt'),
			'view_item'          => __('View Post Type', 'mb-cpt'),
			'all_items'          => __('All Post Types', 'mb-cpt'),
			'search_items'       => __('Search Post Types', 'mb-cpt'),
			'parent_item_colon'  => __('Parent Post Types:', 'mb-cpt'),
			'not_found'          => __('No Post Types found.', 'mb-cpt'),
			'not_found_in_trash' => __('No Post Types found in Trash.', 'mb-cpt'),
			'descriptions'       => __('Post Types GUI', 'mb-cpt'),
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array('slug' => 'mb-post-type'),
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => false,
			'menu_icon'          => 'dashicons-editor-justify',
		);

		$cpts[] = $mb_cpt;

		// get_posts where post_type = mb-post-type
		// foreach post and add to $cpts array

		return $cpts;
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
		$prefix = 'mb_cpt_';

		$meta_boxes[] = array(
			'id'       => 'arguments',
			'title'    => 'Arguments',
			'pages'    => array('mb-post-type'),
			'context'  => 'normal',
			'priority' => 'high',

			'fields'   => array(
				array(
					'name'          => _x( 'Post Type', 'mb-cpt' ),
					'id'            => $prefix . 'post_type',
					'type'          => 'text',
					'placeholder'   => _x( 'Name of Custom Post Type', 'mb-cpt' ),
					'size'          => 50,
				),
				array(
					'name'          => _x( 'Slug', 'mb-cpt' ),
					'id'            => $prefix . 'slug',
					'type'          => 'text',
					'placeholder'   => _x( 'Slug', 'mb-cpt' ),
					'size'          => 50,
				),
				array(
					'name'          => _x( 'Description', 'mb-cpt' ),
					'id'            => $prefix . 'description',
					'type'          => 'textarea',
					'placeholder'   => _x( 'Description', 'mb-cpt' ),
					'cols'          => 10,
				),
				array(
					'name'          => _x( 'Icon', 'mb-cpt' ),
					'id'            => $prefix . 'icon',
					'type'          => 'select',
					'options'       => sl_icons(),
				),
				array(
					'name'          => _x( 'Hierarchical', 'mb-cpt' ),
					'id'            => $prefix . 'hierarchical',
					'type'          => 'checkbox',
				),
			)
		);

		$meta_boxes[] = array(
			'id'       => 'labels',
			'title'    => 'Labels',
			'pages'    => array('mb-post-type'),
			'context'  => 'normal',
			'priority' => 'high',

			'fields'   => array(
				array(
					'name'          => _x( 'Plural Name', 'mb-cpt' ),
					'id'            => $prefix . 'name',
					'type'          => 'text',
					'placeholder'   => _x( 'Plural Name', 'mb-cpt' ),
					'size'          => 50,
				),
				array(
					'name'          => _x( 'Singular Name', 'mb-cpt' ),
					'id'            => $prefix . 'singular_name',
					'type'          => 'text',
					'placeholder'   => _x( 'Singular Name', 'mb-cpt' ),
					'size'          => 50,
				),
				array(
					'name'          => _x( 'Menu Name', 'mb-cpt' ),
					'id'            => $prefix . 'menu_name',
					'type'          => 'text',
					'placeholder'   => _x( 'Menu Name', 'mb-cpt' ),
					'size'          => 50,
				),
				array(
					'name'          => _x( 'Name Admin Bar', 'mb-cpt' ),
					'id'            => $prefix . 'name_admin_bar',
					'type'          => 'text',
					'placeholder'   => _x( 'Name Admin Bar', 'mb-cpt' ),
					'size'          => 50,
				),
				array(
					'name'  => _x( 'Parent Items:', 'mb-cpt' ),
					'id'    => $prefix . 'parent_item_colon',
					'type'  => 'text',
					'std'   => _x( 'Parent Items:', 'mb-cpt' ),
					'size'  => 50,
				),
				array(
					'name'  => _x( 'All Items', 'mb-cpt' ),
					'id'    => $prefix . 'all_item',
					'type'  => 'text',
					'std'   => _x( 'All Items', 'mb-cpt' ),
					'size'  => 50,
				),
				array(
					'name'  => _x( 'Add New Item', 'mb-cpt' ),
					'id'    => $prefix . 'add_new_item',
					'type'  => 'text',
					'std'   => _x( 'Add New Item', 'mb-cpt' ),
					'size'  => 50,
				),
				array(
					'name'  => _x( 'Add New', 'mb-cpt' ),
					'id'    => $prefix . 'add_new',
					'type'  => 'text',
					'std'   => _x( 'Add New', 'mb-cpt' ),
					'size'  => 50,
				),
				array(
					'name'  => _x( 'New Item', 'mb-cpt' ),
					'id'    => $prefix . 'new_item',
					'type'  => 'text',
					'std'   => _x( 'New Item', 'mb-cpt' ),
					'size'  => 50,
				),
				array(
					'name'  => _x( 'Edit Item', 'mb-cpt' ),
					'id'    => $prefix . 'edit_item',
					'type'  => 'text',
					'std'   => _x( 'Edit Item', 'mb-cpt' ),
					'size'  => 50,
				),
				array(
					'name'  => _x( 'Update Item', 'mb-cpt' ),
					'id'    => $prefix . 'update_item',
					'type'  => 'text',
					'std'   => _x( 'Update Item', 'mb-cpt' ),
					'size'  => 50,
				),
				array(
					'name'  => _x( 'View Item', 'mb-cpt' ),
					'id'    => $prefix . 'view_item',
					'type'  => 'text',
					'std'   => _x( 'View Item', 'mb-cpt' ),
					'size'  => 50,
				),
				array(
					'name'  => _x( 'Search Items', 'mb-cpt' ),
					'id'    => $prefix . 'search_item',
					'type'  => 'text',
					'std'   => _x( 'Search Items', 'mb-cpt' ),
					'size'  => 50,
				),
				array(
					'name'  => _x( 'Not found', 'mb-cpt' ),
					'id'    => $prefix . 'not_found',
					'type'  => 'text',
					'std'   => _x( 'Not found', 'mb-cpt' ),
					'size'  => 50,
				),
				array(
					'name'  => _x( 'Not found in Trash', 'mb-cpt' ),
					'id'    => $prefix . 'not_found_in_trash',
					'type'  => 'text',
					'std'   => _x( 'Not found in Trash', 'mb-cpt' ),
					'size'  => 50,
				),
			)
		);

		return $meta_boxes;
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