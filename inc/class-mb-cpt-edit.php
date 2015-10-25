<?php
/**
 * Controls all operations of MB Custom Post Type extension for creating / modifying custom post type.
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 * @author     Doan Manh Duc
 * @author     Tran Ngoc Tuan Anh <rilwis@gmail.com>
 */

/**
 * Controls all operations for creating / modifying custom post type.
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
		wp_enqueue_style( 'mb-cpt', MB_CPT_URL . 'css/style.css', array(), '1.0.0', false );
		wp_enqueue_script( 'mb-cpt', MB_CPT_URL . 'js/script.js', array( 'jquery', 'angular' ), '1.0.0', false );

		$labels = array(
			'menu_name'          => '%name%',
			'name_admin_bar'     => '%singular_name%',
			'all_items'          => __( 'All %name%', 'mb-custom-post-type' ),
			'add_new'            => __( 'Add new', 'mb-custom-post-type' ),
			'add_new_item'       => __( 'Add new %singular_name%', 'mb-custom-post-type' ),
			'edit_item'          => __( 'Edit %singular_name%', 'mb-custom-post-type' ),
			'new_item'           => __( 'New %singular_name%', 'mb-custom-post-type' ),
			'view_item'          => __( 'View %singular_name%', 'mb-custom-post-type' ),
			'search_items'       => __( 'Search %name%', 'mb-custom-post-type' ),
			'not_found'          => __( 'No %name% found', 'mb-custom-post-type' ),
			'not_found_in_trash' => __( 'No %name% found in Trash', 'mb-custom-post-type' ),
			'parent_item_colon'  => __( 'Parent %singular_name%', 'mb-custom-post-type' ),
		);
		wp_localize_script( 'mb-cpt', 'MBPostTypeLabels', $labels );
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
				'name'        => __( 'Plural name', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'name',
				'type'        => 'text',
				'placeholder' => __( 'General name for the post type', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Singular name', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'singular_name',
				'type'        => 'text',
				'placeholder' => __( 'Name for one object of this post type', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Slug', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'post_type',
				'type' => 'text',
			),
		);
		$labels_fields   = array(
			array(
				'name'        => __( 'Menu name', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'menu_name',
				'type'        => 'text',
				'placeholder' => __( 'The menu name text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Name in admin bar', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'name_admin_bar',
				'type'        => 'text',
				'placeholder' => __( 'Name given for the Add New dropdown on admin bar', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'All items', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'all_items',
				'type'        => 'text',
				'placeholder' => __( 'The all items text used in the menu', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Add new', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'add_new',
				'type'        => 'text',
				'placeholder' => __( 'The add new text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Add new item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'add_new_item',
				'type'        => 'text',
				'placeholder' => __( 'The add new item text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Edit item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'edit_item',
				'type'        => 'text',
				'placeholder' => __( 'The edit item text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'New item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'new_item',
				'type'        => 'text',
				'placeholder' => __( 'The new item text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'View item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'view_item',
				'type'        => 'text',
				'placeholder' => __( 'The view item text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Search items', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'search_items',
				'type'        => 'text',
				'placeholder' => __( 'The search items text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Not found', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'not_found',
				'type'        => 'text',
				'placeholder' => __( 'The not found text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Not found in Trash', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'not_found_in_trash',
				'type'        => 'text',
				'placeholder' => __( 'The not found in trash text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Parent Items:', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'parent_item_colon',
				'type'        => 'text',
				'placeholder' => __( 'The parent text', 'mb-custom-post-type' ),
			),
		);
		$advanced_fields = array(
			array(
				'name'        => __( 'Description', 'mb-custom-post-type' ),
				'id'          => $args_prefix . 'description',
				'type'        => 'textarea',
				'placeholder' => __( 'A short descriptive summary of what the post type is.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Public?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'public',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Controls how the type is visible to authors and readers.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Exclude from search?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'exclude_from_search',
				'type' => 'checkbox',
				'desc' => __( 'Whether to exclude posts with this post type from frontend search results.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Publicly queryable?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'publicly_queryable',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether queries can be performed on the frontend.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show UI?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_ui',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether to generate a default UI for managing this post type in the admin.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show in nav menus?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_nav_menus',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether post type is available for selection in navigation menus.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show in menu?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_menu',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Where to show the post type in the admin menu. <code>show_ui</code> must be <code>true</code>.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show in admin bar?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_admin_bar',
				'type' => 'checkbox',
				'desc' => __( 'Whether to make this post type available in the WordPress admin bar.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Menu position', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'menu_position',
				'type' => 'number',
			),
			array(
				'name'    => __( 'Menu icon', 'mb-custom-post-type' ),
				'id'      => $args_prefix . 'menu_icon',
				'type'    => 'radio',
				'options' => mb_cpt_get_dashicons(),
			),
			array(
				'name'    => __( 'Capability type', 'mb-custom-post-type' ),
				'id'      => $args_prefix . 'capability_type',
				'type'    => 'select',
				'options' => array(
					'post' => __( 'Post', 'mb-custom-post-type' ),
					'page' => __( 'Page', 'mb-custom-post-type' ),
				),
				'std'     => 'post',
			),
			array(
				'name' => __( 'Hierarchical?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'hierarchical',
				'type' => 'checkbox',
				'desc' => __( 'Whether the post type is hierarchical. Allows Parent to be specified.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Has archive?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'has_archive',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Enables post type archives. Will use <code>$post_type</code> as archive slug by default.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Query var', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'query_var',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'False to prevent queries, or string value of the query var to use for this post type.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Can export?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'can_export',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Can this post type be exported.', 'mb-custom-post-type' ),
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
					'std'     => array( 'title', 'editor', 'thumbnail' ),
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
	 * @param string $html
	 * @param array  $field
	 * @param string $meta
	 *
	 * @return string
	 */
	public function modify_field_html( $html, $field, $meta )
	{
		// Labels
		if ( 0 === strpos( $field['id'], 'label_' ) )
		{
			$model = substr( $field['id'], 6 );
			$html  = str_replace( '>', sprintf(
				' ng-model="labels.%s" ng-init="labels.%s=\'%s\'"%s>',
				$model,
				$model,
				$meta,
				in_array( $model, array( 'name', 'singular_name' ) ) ? ' ng-change="updateLabels()"' : ''
			), $html );
			$html  = preg_replace( '/value="(.*?)"/', 'value="{{labels.' . $model . '}}"', $html );
		}
		// Slug
		elseif ( 'args_post_type' == $field['id'] )
		{
			$html = str_replace( '>', sprintf(
				' ng-model="post_type" ng-init="post_type=\'%s\'">',
				$meta
			), $html );
			$html = preg_replace( '/value="(.*?)"/', 'value="{{post_type}}"', $html );
		}
		// Menu icons
		elseif ( 'args_menu_icon' == $field['id'] )
		{
			$html  = '';
			$icons = mb_cpt_get_dashicons();
			foreach ( $icons as $icon )
			{
				$html .= sprintf( '
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
		return $html;
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
