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
		$this->encoder  = $encoder;

		// Change the menu positions option after all menus are registered.
		add_action( 'admin_menu', array( $this, 'change_select_options' ), 9999 );
	}

	/**
	 * List of Javascript variables.
	 *
	 * @return array
	 */
	public function js_vars() {
		// @codingStandardsIgnoreStart
		return array_merge( parent::js_vars(), array(
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
		) );
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

		$basic_fields = array(
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
				'desc' => __( 'Maximum 20 characters', 'mb-custom-post-type' ),
			),
		);

		$labels_fields = array(
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

		$menu_icon_options = array();
		$icons = mb_cpt_get_dashicons();
		foreach ( $icons as $icon ) {
			$menu_icon_options[ $icon ] = $icon;
		}

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
			'show_in_menu'  => array(
				'name'       => __( 'Show in menu?', 'mb-custom-post-type' ),
				'id'         => $args_prefix . 'show_in_menu',
				'type'       => 'select_advanced',
				'options'    => array(),
				'desc'       => __( 'Where to show the post type in the admin menu. <code>show_ui</code> must be <code>true</code>.', 'mb-custom-post-type' ),
				'js_options' => array(
					'width' => '400px',
				),
			),
			array(
				'name' => __( 'Show in admin bar?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_admin_bar',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether to make this post type available in the WordPress admin bar.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show in REST API?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_rest',
				'type' => 'checkbox',
				'desc' => __( 'Whether to add the post type in the REST API.', 'mb-custom-post-type' ),
				'std'  => 1,
			),
			array(
				'name'        => __( 'REST API base slug', 'mb-custom-post-type' ),
				'id'          => $args_prefix . 'rest_base',
				'type'        => 'text',
				'placeholder' => __( 'Slug to use in REST API URLs', 'mb-custom-post-type' ),
				'desc'        => __( 'Leave empty to use the post type slug.', 'mb-custom-post-type' ),
			),
			'menu_position' => array(
				'name'    => __( 'Menu position after', 'mb-custom-post-type' ),
				'id'      => $args_prefix . 'menu_position',
				'type'    => 'select_advanced',
				'options' => array(),
			),
			array(
				'name'    => __( 'Menu icon', 'mb-custom-post-type' ),
				'id'      => $args_prefix . 'menu_icon',
				'type'    => 'radio',
				'options' => $menu_icon_options,
			),
			array(
				'name'    => __( 'Capability type', 'mb-custom-post-type' ),
				'id'      => $args_prefix . 'capability_type',
				'type'    => 'radio',
				'inline'  => true,
				'options' => array(
					'post'   => __( 'Post', 'mb-custom-post-type' ),
					'page'   => __( 'Page', 'mb-custom-post-type' ),
					'custom' => __( 'Custom', 'mb-custom-post-type' ),
				),
				'std'     => 'post',
				'desc'    => __( 'The post type to use for checking read, edit, and delete capabilities.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Hierarchical?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'hierarchical',
				'type' => 'checkbox',
				'desc' => __( 'Whether the post type is hierarchical.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Has archive?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'has_archive',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Enables post type archives.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Custom archive slug', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'archive_slug',
				'type' => 'text',
				'desc' => __( 'Default is the post type slug.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Query var', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'query_var',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Enables request the post via URL <code>example.com/?post_type=slug</code>', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Can export?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'can_export',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Can this post type be exported?', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Rewrite', 'mb-custom-post-type' ),
				'type' => 'heading',
			),
			array(
				'name' => __( 'Custom rewrite slug', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'rewrite_slug',
				'type' => 'text',
				'desc' => __( 'Leave empty to use the post type slug.', 'mb-custom-post-type' ),
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
		);
		if ( isset( $_GET['post'] ) ) {
			$code_fields[] = array(
				'name'     => __( 'Code', 'mb-custom-post-type' ),
				'id'       => 'code',
				'type'     => 'custom-html',
				'callback' => array( $this, 'generated_code_html' ),
			);
		}

		// Basic settings.
		$meta_boxes[] = array(
			'id'         => 'mb-cpt-basic-settings',
			'title'      => __( 'Basic Settings', 'mb-custom-post-type' ),
			'post_types' => array( 'mb-post-type' ),
			'fields'     => $basic_fields,
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

		$buttons = '<button type="button" class="button" id="mb-cpt-toggle-labels">' . esc_html__( 'Toggle Labels Settings', 'mb-custom-post-type' ) . '</button> <button type="button" class="button" id="mb-cpt-toggle-code">' . esc_html__( 'Get PHP Code', 'mb-custom-post-type' ) . '</button>';

		if ( function_exists( 'mb_builder_load' ) ) {
			$buttons .= ' <a class="button button-primary" href="' . esc_url( admin_url( 'edit.php?post_type=meta-box' ) ) . '" target="_blank">' . esc_html__( 'Add Custom Fields', 'mb-custom-post-type' ) . '</a>';
		}

		$meta_boxes[] = array(
			'id'         => 'mb-cpt-buttons',
			'title'      => ' ',
			'post_types' => array( 'mb-post-type' ),
			'style'      => 'seamless',
			'fields'     => array(
				array(
					'type' => 'custom_html',
					'std'  => $buttons,
				),
			),
		);

		// Labels settings.
		$meta_boxes[] = array(
			'id'         => 'mb-cpt-label-settings',
			'title'      => __( 'Labels Settings', 'mb-custom-post-type' ),
			'post_types' => array( 'mb-post-type' ),
			'fields'     => $labels_fields,
		);

		// Advanced settings.
		$meta_boxes[] = array(
			'id'         => 'mb-cpt-advanced-settings',
			'title'      => __( 'Advanced Settings', 'mb-custom-post-type' ),
			'post_types' => array( 'mb-post-type' ),
			'fields'     => $advanced_fields,
		);

		// Supports.
		$meta_boxes[] = array(
			'id'         => 'mb-cpt-supports',
			'title'      => __( 'Supports', 'mb-custom-post-type' ),
			'post_types' => array( 'mb-post-type' ),
			'priority'   => 'low',
			'context'    => 'side',
			'fields'     => array(
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
			'id'         => 'mb-cpt-taxonomies',
			'title'      => __( 'Default Taxonomies', 'mb-custom-post-type' ),
			'post_types' => array( 'mb-post-type' ),
			'priority'   => 'low',
			'context'    => 'side',
			'fields'     => array(
				array(
					'id'      => $args_prefix . 'taxonomies',
					'type'    => 'checkbox_list',
					'options' => array(
						'category' => __( 'Category', 'mb-custom-post-type' ),
						'post_tag' => __( 'Tag', 'mb-custom-post-type' ),
					),
				),
				array(
					'type'    => 'custom_html',
					'std'    => '<a href="' . esc_url( admin_url( 'edit.php?post_type=mb-taxonomy' ) ) . '" class="button" target="_blank">' . esc_html__( 'Add custom taxonomies', 'mb-custom-post-type' ) . '</a>',
				),
			),
		);

		$meta_boxes[] = array(
			'id'         => 'mb-cpt-generate-code',
			'title'      => __( 'Generate Code', 'mb-custom-post-type' ),
			'post_types' => array( 'mb-post-type' ),
			'fields'     => $code_fields,
		);

		$fields = array_merge( $basic_fields, $labels_fields, $advanced_fields );

		// Add AngularJS attributes to fields.
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
		if ( 'mb-post-type' !== get_current_screen()->id ) {
			return $html;
		}

		// Fix for escaping single quote for AngularJS.
		$meta = str_replace( '&#039;', "\\'", $meta );

		// Labels.
		if ( 0 === strpos( $field['id'], 'label_' ) ) {
			$model = substr( $field['id'], 6 );
			$html  = str_replace(
				'>',
				sprintf(
					' ng-model="labels.%s" ng-init="labels.%s=\'%s\'"%s>',
					esc_attr( $model ),
					esc_attr( $model ),
					$meta,
					in_array( $model, array( 'name', 'singular_name' ), true ) ? ' ng-change="updateLabels()"' : ''
				),
				$html
			);
			$html  = preg_replace( '/value="(.*?)"/', 'value="{{labels.' . esc_attr( $model ) . '}}"', $html );
			return $html;
		}

		if ( 'args_post_type' === $field['id'] ) {
			$html = str_replace(
				'>',
				sprintf(
					' ng-model="post_type" ng-init="post_type=\'%s\'">',
					$meta
				),
				$html
			);
			$html = preg_replace( '/value="(.*?)"/', 'value="{{post_type}}"', $html );
			return $html;
		}

		if ( 'args_menu_icon' === $field['id'] ) {
			$html  = '';
			$icons = mb_cpt_get_dashicons();
			foreach ( $icons as $icon ) {
				$html .= sprintf(
					'<label class="icon-single%s">
						<i class="wp-menu-image dashicons-before %s"></i>
						<input type="radio" name="args_menu_icon" value="%s" class="hidden"%s>
					</label>',
					$icon === $meta ? ' active' : '',
					esc_attr( $icon ),
					esc_attr( $icon ),
					checked( $icon, $meta, false )
				);
			}
			return $html;
		}

		return $html;
	}

	/**
	 * Print generated code textarea.
	 *
	 * @return string
	 */
	public function generated_code_html() {
		$post_id               = get_the_ID();
		list( $labels, $args ) = $this->register->get_post_type_data( $post_id );
		if ( ! $labels ) {
			return '';
		}

		$post_type_data = $this->register->set_up_post_type( $labels, $args );

		$encode_data    = array(
			'function_name'  => get_post_meta( $post_id, 'function_name', true ),
			'text_domain'    => get_post_meta( $post_id, 'text_domain', true ),
			'post_type'      => $args['post_type'],
			'post_type_data' => $post_type_data,
		);
		$encoded_string = $this->encoder->encode( $encode_data );

		$output  = '
			<div id="generated-code">
				<a href="javascript:void(0);" class="mb-button--copy">
					<svg class="mb-icon--copy" aria-hidden="true" role="img"><use href="#mb-icon-copy" xlink:href="#icon-copy"></use></svg>
					' . esc_html__( 'Copy', 'mb-custom-post-type' ) . '
				</a>
				<pre><code class="php">' . esc_textarea( $encoded_string ) . '</code></pre>
			</div>';
		$output .= '
			<svg style="display: none;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				<symbol id="mb-icon-copy" viewBox="0 0 1024 896">
					<path d="M128 768h256v64H128v-64z m320-384H128v64h320v-64z m128 192V448L384 640l192 192V704h320V576H576z m-288-64H128v64h160v-64zM128 704h160v-64H128v64z m576 64h64v128c-1 18-7 33-19 45s-27 18-45 19H64c-35 0-64-29-64-64V192c0-35 29-64 64-64h192C256 57 313 0 384 0s128 57 128 128h192c35 0 64 29 64 64v320h-64V320H64v576h640V768zM128 256h512c0-35-29-64-64-64h-64c-35 0-64-29-64-64s-29-64-64-64-64 29-64 64-29 64-64 64h-64c-35 0-64 29-64 64z" />
				</symbol>
			</svg>';
		return $output;
	}

	/**
	 * Change select options.
	 */
	public function change_select_options() {
		$meta_box = rwmb_get_registry( 'meta_box' )->get( 'mb-cpt-advanced-settings' );
		$meta_box->meta_box['fields']['menu_position']['options'] = $this->get_menu_positions();
		$meta_box->meta_box['fields']['show_in_menu']['options']  = $this->get_menu_options();
	}

	/**
	 * Get WordPress menu positions
	 *
	 * @return array
	 */
	protected function get_menu_positions() {
		global $menu;
		$positions = array();
		foreach ( $menu as $position => $params ) {
			if ( ! empty( $params[0] ) ) {
				$positions[ $position ] = $this->strip_span( $params[0] );
			}
		}
		return $positions;
	}

	/**
	 * Get WordPress menu options
	 *
	 * @return array
	 */
	protected function get_menu_options() {
		global $menu;
		$options = array(
			'1' => esc_html__( 'Show as top-level menu', 'mb-custom-post-type' ),
		);
		foreach ( $menu as $position => $params ) {
			if ( ! empty( $params[0] ) && ! empty( $params[2] ) ) {
				// Translators: %s is the main menu label.
				$options[ $params[2] ] = sprintf( __( 'Show as sub-menu of %s', 'mb-custom-post-type' ), $this->strip_span( $params[0] ) );
			}
		}
		$options['0'] = esc_html__( 'Do not show in the admin menu', 'mb-custom-post-type' );
		return $options;
	}

	/**
	 * Remove <span> tag (counter) with their content.
	 *
	 * @param string $html HTML content.
	 *
	 * @return string
	 */
	protected function strip_span( $html ) {
		return preg_replace( '@<span .*>.*</span>@si', '', $html );
	}
}
