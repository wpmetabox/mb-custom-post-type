<?php
/**
 * Controls all operations of MB Custom Post Type extension for creating / modifying custom post type.
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 */

/**
 * Controls all operations for creating / modifying custom post type.
 */
class MB_CPT_Post_Type_Edit extends MB_CPT_Base_Edit {

	/**
	 * Post type register object.
	 *
	 * @var MB_CPT_Post_Type_Register
	 */
	protected $register;

	/**
	 * Encoder object.
	 *
	 * @var MB_CPT_Encoder_Interface
	 */
	protected $encoder;

	/**
	 * Class MB_CPT_Post_Type_Edit constructor.
	 *
	 * @param string                    $post_type Post type name.
	 * @param MB_CPT_Post_Type_Register $register  Post type register object.
	 * @param MB_CPT_Encoder_Interface  $encoder   Encoder object.
	 */
	public function __construct( $post_type, MB_CPT_Post_Type_Register $register, MB_CPT_Encoder_Interface $encoder ) {
		parent::__construct( $post_type );

		$this->register = $register;
		$this->encoder = $encoder;
	}

	/**
	 * List of Javascript variables.
	 *
	 * @return array
	 */
	public function js_vars() {
		// @codingStandardsIgnoreStart
		return array(
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
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Register meta boxes for add/edit mb-post-type page.
	 *
	 * @param array $meta_boxes Meta boxes.
	 *
	 * @return array
	 */
	public function register_meta_boxes( $meta_boxes ) {
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
				'name' => __( 'Show in REST API?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_rest',
				'type' => 'checkbox',
				'desc' => __( 'Whether to add the post type route in the REST API "wp/v2" namespace.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'REST base', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'rest_base',
				'type' => 'text',
				'desc' => __( 'To change the base url of REST API route. Default is post type.', 'mb-custom-post-type' ),
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
			array(
				'name' => __( 'Rewrite', 'mb-custom-post-type' ),
				'type' => 'heading',
			),
			array(
				'name' => __( 'Rewrite slug', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'rewrite_slug',
				'type' => 'text',
				'desc' => __( 'Leave empty to use post type as rewrite slug.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'No prepended permalink structure?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'rewrite_no_front',
				'type' => 'checkbox',
				'desc' => __( 'Do not prepend the permalink structure with the front base.', 'mb-custom-post-type' ),
			),
		);

		$code_fields = array(
			array(
				'name' => __( 'Function name', 'mb-custom-post-type' ),
				'id'   => 'function_name',
				'type' => 'text',
				'std'  => 'your_prefix_register_post_type',
			),
			array(
				'name' => __( 'Text domain', 'mb-custom-post-type' ),
				'id'   => 'text_domain',
				'type' => 'text',
				'std'  => 'text-domain',
			),
			array(
				'name' => __( 'Code', 'mb-custom-post-type' ),
				'id'   => 'code',
				'type' => 'custom-html',
				'callback' => array( $this, 'generated_code_html' ),
			),
		);

		// Basic settings.
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
				),
			),
		);

		// Labels settings.
		$meta_boxes[] = array(
			'id'     => 'label-settings',
			'title'  => __( 'Labels Settings', 'mb-custom-post-type' ),
			'pages'  => array( 'mb-post-type' ),
			'fields' => $labels_fields,
		);

		// Advanced settings.
		$meta_boxes[] = array(
			'id'     => 'advanced-settings',
			'title'  => __( 'Advanced Settings', 'mb-custom-post-type' ),
			'pages'  => array( 'mb-post-type' ),
			'fields' => $advanced_fields,
		);

		// Supports.
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
						'custom-fields'   => __( 'Custom fields', 'mb-custom-post-type' ),
						'comments'        => __( 'Comments', 'mb-custom-post-type' ),
						'revisions'       => __( 'Revisions', 'mb-custom-post-type' ),
						'page-attributes' => __( 'Page Attributes', 'mb-custom-post-type' ),
					),
					'std'     => array( 'title', 'editor', 'thumbnail' ),
				),
			),
		);

		// Default taxonomies.
		$meta_boxes[] = array(
			'id'       => 'taxonomies',
			'title'    => __( 'Default Taxonomies', 'mb-custom-post-type' ),
			'pages'    => array( 'mb-post-type' ),
			'priority' => 'low',
			'context'  => 'side',
			'fields'   => array(
				array(
					'id'      => $args_prefix . 'taxonomies',
					'type'    => 'checkbox_list',
					'options' => array(
						'category' => __( 'Category', 'mb-custom-post-type' ),
						'post_tag' => __( 'Tag', 'mb-custom-post-type' ),
					),
					// translators: %s: Link to edit taxonomies page.
					'desc'    => sprintf( __( 'Add default taxonomies to post type. For custom taxonomies, please <a href="%s" target="_blank">click here</a>.', 'mb-custom-post-type' ), admin_url( 'edit.php?post_type=mb-taxonomy' ) ),
				),
			),
		);

		$meta_boxes[] = array(
			'id'         => 'generated-code',
			'title'      => __( 'Generated code', 'mb-custom-post-type' ),
			'post_types' => array( 'mb-post-type' ),
			'fields'     => $code_fields,
		);

		$fields = array_merge( $basic_fields, $labels_fields, $advanced_fields );

		// Add ng-model attribute to all fields.
		foreach ( $fields as $field ) {
			if ( ! empty( $field['id'] ) ) {
				add_filter( 'rwmb_' . $field['id'] . '_html', array( $this, 'modify_field_html' ), 10, 3 );
			}
		}

		return $meta_boxes;
	}

	/**
	 * Modify html output of field
	 *
	 * @param string $html  HTML out put of the field.
	 * @param array  $field Field parameters.
	 * @param string $meta  Meta value.
	 *
	 * @return string
	 */
	public function modify_field_html( $html, $field, $meta ) {
		// Labels.
		if ( 0 === strpos( $field['id'], 'label_' ) ) {
			$model = substr( $field['id'], 6 );
			$html  = str_replace( '>', sprintf(
				' ng-model="labels.%s" ng-init="labels.%s=\'%s\'"%s>',
				$model,
				$model,
				$meta,
				in_array( $model, array( 'name', 'singular_name' ), true ) ? ' ng-change="updateLabels()"' : ''
			), $html );
			$html  = preg_replace( '/value="(.*?)"/', 'value="{{labels.' . $model . '}}"', $html );
		} elseif ( 'args_post_type' === $field['id'] ) {
			$html = str_replace( '>', sprintf(
				' ng-model="post_type" ng-init="post_type=\'%s\'">',
				$meta
			), $html );
			$html = preg_replace( '/value="(.*?)"/', 'value="{{post_type}}"', $html );
		} elseif ( 'args_menu_icon' === $field['id'] ) {
			$html  = '';
			$icons = mb_cpt_get_dashicons();
			foreach ( $icons as $icon ) {
				$html .= sprintf( '
					<label class="icon-single%s">
						<i class="wp-menu-image dashicons-before %s"></i>
						<input type="radio" name="args_menu_icon" value="%s" class="hidden"%s>
					</label>',
					$icon === $meta ? ' active' : '',
					$icon,
					$icon,
					checked( $icon, $meta, false )
				);
			}
		}

		return $html;
	}

	/**
	 * Print generated code textarea.
	 *
	 * @return string
	 */
	public function generated_code_html() {
		$post_id = get_the_ID();
		list( $labels, $args ) = $this->register->get_post_type_data( $post_id );
		$post_type_data = $this->register->set_up_post_type( $labels, $args );

		$encode_data = array(
			'function_name'  => get_post_meta( $post_id, 'function_name', true ),
			'text_domain'    => get_post_meta( $post_id, 'text_domain', true ),
			'post_type'      => $args['post_type'],
			'post_type_data' => $post_type_data,
		);
		$encoded_string = $this->encoder->encode( $encode_data );

		return '<div id="generated-code"><pre><code class="php">' . esc_textarea( $encoded_string ) . '</code></pre></div>';
	}
}
