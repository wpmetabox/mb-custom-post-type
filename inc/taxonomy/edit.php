<?php
/**
 * Controls all operations of MB Custom Taxonomy extension for creating / modifying custom taxonomy.
 *
 * @package    Meta Box
 * @subpackage MB Custom Taxonomy
 * @author     Tran Ngoc Tuan Anh <rilwis@gmail.com>
 */

/**
 * Controls all operations for creating / modifying custom taxonomy.
 */
class MB_CPT_Taxonomy_Edit extends MB_CPT_Base_Edit {

	/**
	 * Taxonomy register object.
	 *
	 * @var MB_CPT_Taxonomy_Register
	 */
	protected $register;

	/**
	 * Encoder object.
	 *
	 * @var MB_CPT_Encoder_Interface
	 */
	protected $encoder;

	/**
	 * Class MB_CPT_Taxonomy_Edit constructor.
	 *
	 * @param string                   $taxonomy Post type name.
	 * @param MB_CPT_Taxonomy_Register $register Post type register object.
	 * @param MB_CPT_Encoder_Interface $encoder  Encoder object.
	 */
	public function __construct( $taxonomy, MB_CPT_Taxonomy_Register $register, MB_CPT_Encoder_Interface $encoder ) {
		parent::__construct( $taxonomy );

		$this->register = $register;
		$this->encoder  = $encoder;
	}

	/**
	 * List of Javascript variables.
	 *
	 * @return array
	 */
	public function js_vars() {
		// @codingStandardsIgnoreStart
		return array_merge( parent::js_vars(), array(
			'menu_name'                  => '%name%',
			'all_items'                  => __( 'All %name%', 'mb-custom-post-type' ),
			'edit_item'                  => __( 'Edit %singular_name%', 'mb-custom-post-type' ),
			'view_item'                  => __( 'View %singular_name%', 'mb-custom-post-type' ),
			'update_item'                => __( 'Update %singular_name%', 'mb-custom-post-type' ),
			'add_new_item'               => __( 'Add new %singular_name%', 'mb-custom-post-type' ),
			'new_item_name'              => __( 'New %singular_name%', 'mb-custom-post-type' ),
			'parent_item'                => __( 'Parent %singular_name%', 'mb-custom-post-type' ),
			'parent_item_colon'          => __( 'Parent %singular_name%:', 'mb-custom-post-type' ),
			'search_items'               => __( 'Search %name%', 'mb-custom-post-type' ),
			'popular_items'              => __( 'Popular %name%', 'mb-custom-post-type' ),
			'separate_items_with_commas' => __( 'Separate %name% with commas', 'mb-custom-post-type' ),
			'add_or_remove_items'        => __( 'Add or remove %name%', 'mb-custom-post-type' ),
			'choose_from_most_used'      => __( 'Choose most used %name%', 'mb-custom-post-type' ),
			'not_found'                  => __( 'No %name% found', 'mb-custom-post-type' ),
		) );
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Register meta boxes for add/edit mb-taxonomy page
	 *
	 * @param array $meta_boxes Custom meta boxes.
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
				'placeholder' => __( 'General name for the taxonomy', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Singular name', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'singular_name',
				'type'        => 'text',
				'placeholder' => __( 'Name for one object of this taxonomy', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Slug', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'taxonomy',
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
				'name'        => __( 'All items', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'all_items',
				'type'        => 'text',
				'placeholder' => __( 'The all items text used in the menu', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Edit item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'edit_item',
				'type'        => 'text',
				'placeholder' => __( 'The edit item text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'View item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'view_item',
				'type'        => 'text',
				'placeholder' => __( 'The view item text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Update item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'update_item',
				'type'        => 'text',
				'placeholder' => __( 'The update item text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Add new item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'add_new_item',
				'type'        => 'text',
				'placeholder' => __( 'The add new item text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'New item name', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'new_item_name',
				'type'        => 'text',
				'placeholder' => __( 'The new item name text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Parent Item', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'parent_item',
				'type'        => 'text',
				'placeholder' => __( 'The parent item text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Parent Item Colon', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'parent_item_colon',
				'type'        => 'text',
				'placeholder' => __( 'The same as parent item, but with colon (:) in the end', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Search items', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'search_items',
				'type'        => 'text',
				'placeholder' => __( 'The search items text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Popular items', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'popular_items',
				'type'        => 'text',
				'placeholder' => __( 'The popular items text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Separate items with commas', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'separate_items_with_commas',
				'type'        => 'text',
				'placeholder' => __( 'The separate items with commas text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Add or remove items', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'add_or_remove_items',
				'type'        => 'text',
				'placeholder' => __( 'The add or remove items text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Choose from most used', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'choose_from_most_used',
				'type'        => 'text',
				'placeholder' => __( 'The choose from most used text', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'Not found', 'mb-custom-post-type' ),
				'id'          => $label_prefix . 'not_found',
				'type'        => 'text',
				'placeholder' => __( 'The not found text', 'mb-custom-post-type' ),
			),
		);
		$advanced_fields = array(
			array(
				'name' => __( 'Public?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'public',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'If the taxonomy should be publicly queryable.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show UI?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_ui',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether to generate a default UI for managing this taxonomy.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show in menu?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_menu',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Where to show the taxonomy in the admin menu. <code>show_ui</code> must be <code>true</code>.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show in nav menus?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_nav_menus',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether taxonomy is available for selection in navigation menus.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show on edit page?', 'mb-custom-taxonomy' ),
				'id'   => $args_prefix . 'meta_box_cb',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether to show the taxonomy on the edit page.', 'mb-custom-taxonomy' ),
			),
			array(
				'name' => __( 'Show tag cloud?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_tagcloud',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether to allow the Tag Cloud widget to use this taxonomy.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show in quick edit?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_quick_edit',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether to show the taxonomy in the quick/bulk edit panel.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show admin column?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_admin_column',
				'type' => 'checkbox',
				'desc' => __( 'Whether to allow automatic creation of taxonomy columns on associated post-types table.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Show in REST?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_rest',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether to include the taxonomy in the REST API.', 'mb-custom-post-type' ),
			),
			array(
				'name'        => __( 'REST API base slug', 'mb-custom-post-type' ),
				'id'          => $args_prefix . 'rest_base',
				'type'        => 'text',
				'placeholder' => __( 'Slug to use in REST API URLs', 'mb-custom-post-type' ),
				'desc'        => __( 'Leave empty to use the taxonomy slug.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Hierarchical?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'hierarchical',
				'type' => 'checkbox',
				'desc' => __( 'Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Query var', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'query_var',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Uncheck to disable the query var, check to use the taxonomy\'s "name" as query var.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Sort?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'sort',
				'type' => 'checkbox',
				'desc' => __( 'Whether this taxonomy should remember the order in which terms are added to objects.', 'mb-custom-post-type' ),
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
			array(
				'name' => __( 'Hierarchical URL', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'rewrite_hierarchical',
				'type' => 'checkbox',
			),
		);

		$code_fields = array(
			array(
				'name' => __( 'Function name', 'mb-custom-post-type' ),
				'id'   => 'function_name',
				'type' => 'text',
				'std'  => 'your_prefix_register_taxonomy',
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
			'id'         => 'ct-basic-settings',
			'title'      => __( 'Basic Settings', 'mb-custom-post-type' ),
			'post_types' => 'mb-taxonomy',
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

		$buttons = '<button type="button" class="button" id="ct-toggle-labels">' . esc_html__( 'Toggle Labels Settings', 'mb-custom-post-type' ) . '</button> <button type="button" class="button" id="ct-toggle-code">' . esc_html__( 'Get PHP Code', 'mb-custom-post-type' ) . '</button>';

		if ( function_exists( 'mb_builder_load' ) && function_exists( 'mb_user_meta_load' ) ) {
			$buttons .= ' <a class="button button-primary" href="' . esc_url( admin_url( 'edit.php?post_type=meta-box' ) ) . '" target="_blank">' . esc_html__( 'Add Custom Fields', 'mb-custom-post-type' ) . '</a>';
		}

		$meta_boxes[] = array(
			'id'         => 'ct-buttons',
			'title'      => ' ',
			'post_types' => array( 'mb-taxonomy' ),
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
			'id'         => 'mb-ct-label-settings',
			'title'      => __( 'Labels Settings', 'mb-custom-post-type' ),
			'post_types' => 'mb-taxonomy',
			'fields'     => $labels_fields,
		);

		// Advanced settings.
		$meta_boxes[] = array(
			'id'         => 'mb-ct-advanced-settings',
			'title'      => __( 'Advanced Settings', 'mb-custom-post-type' ),
			'post_types' => 'mb-taxonomy',
			'fields'     => $advanced_fields,
		);

		$meta_boxes[] = array(
			'id'         => 'mb-ct-generate-code',
			'title'      => __( 'Generate Code', 'mb-custom-post-type' ),
			'post_types' => array( 'mb-taxonomy' ),
			'fields'     => $code_fields,
		);

		// Post types.
		$options    = array();
		$post_types = get_post_types( '', 'objects' );
		unset( $post_types['mb-taxonomy'], $post_types['revision'], $post_types['nav_menu_item'] );
		foreach ( $post_types as $post_type => $post_type_object ) {
			$options[ $post_type ] = $post_type_object->labels->singular_name;
		}
		$meta_boxes[] = array(
			'id'         => 'mb-ct-assign',
			'title'      => __( 'Assign To Post Types', 'mb-custom-post-type' ),
			'context'    => 'side',
			'post_types' => 'mb-taxonomy',
			'priority'   => 'low',
			'fields'     => array(
				array(
					'id'      => $args_prefix . 'post_types',
					'type'    => 'checkbox_list',
					'options' => $options,
				),
				array(
					'type'    => 'custom_html',
					'std'    => '<a href="' . esc_url( admin_url( 'edit.php?post_type=mb-post-type' ) ) . '" class="button" target="_blank">' . esc_html__( 'Add custom post types', 'mb-custom-post-type' ) . '</a>',
				),
			),
		);

		if ( ! $this->is_premium_user() ) {
			$meta_boxes[] = array(
				'id'         => 'mb-ct-upgrade',
				'title'      => __( 'Upgrade to Meta Box Premium', 'mb-custom-post-type' ),
				'post_types' => array( 'mb-post-type', 'mb-taxonomy' ),
				'context'    => 'side',
				'priority'   => 'low',
				'fields'     => array(
					array(
						'type'     => 'custom_html',
						'callback' => array( $this, 'upgrade_message' ),
					),
				),
			);
		}

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
		if ( 'mb-taxonomy' !== get_current_screen()->id ) {
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
			$html  = preg_replace( '/value="(.*?)"/', 'value="{{labels.' . $model . '}}"', $html );
			return $html;
		}

		if ( 'args_taxonomy' === $field['id'] ) {
			$html = str_replace(
				'>',
				sprintf(
					' ng-model="taxonomy" ng-init="taxonomy=\'%s\'">',
					$meta
				),
				$html
			);
			$html = preg_replace( '/value="(.*?)"/', 'value="{{taxonomy}}"', $html );
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
		list( $labels, $args ) = $this->register->get_taxonomy_data( $post_id );
		$taxonomy_data         = $this->register->set_up_taxonomy( $labels, $args );

		if ( isset( $taxonomy_data['meta_box_cb'] ) ) {
			unset( $taxonomy_data['meta_box_cb'] );
		}

		$encode_data    = array(
			'function_name' => get_post_meta( $post_id, 'function_name', true ),
			'text_domain'   => get_post_meta( $post_id, 'text_domain', true ),
			'taxonomy'      => $args['taxonomy'],
			'taxonomy_data' => $taxonomy_data,
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
	 * Display upgrade message.
	 *
	 * @return string
	 */
	public function upgrade_message() {
		$output  = '<ul>';
		$output .= '<li>' . __( 'Create custom fields with drag-n-drop interface - no coding knowledge required!', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li>' . __( 'Add custom fields to taxonomies or user profile.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li>' . __( 'Create custom settings pages.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li>' . __( 'Create frontend submission forms.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li>' . __( 'And much more!', 'mb-custom-post-type' ) . '</li>';
		$output .= '</ul>';
		$output .= '<a href="https://metabox.io/pricing/?utm_source=plugin_cpt&utm_medium=btn_upgrade&utm_campaign=cpt_upgrade" class="button button-primary">' . esc_html__( 'Get Meta Box Premium now', 'mb-custom-post-type' ) . '</a>';

		return $output;
	}
}
