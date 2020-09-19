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
	 * Class MB_CPT_Taxonomy_Edit constructor.
	 *
	 * @param string                   $taxonomy Post type name.
	 * @param MB_CPT_Taxonomy_Register $register Post type register object.
	 * @param MB_CPT_Encoder_Interface $encoder  Encoder object.
	 */
	public function __construct( $taxonomy, MB_CPT_Taxonomy_Register $register ) {
		parent::__construct( $taxonomy );

		$this->register = $register;
	}

	public function js_vars() {
		$options    = [];
		$post_types = get_post_types( '', 'objects' );
		unset( $post_types['mb-taxonomy'], $post_types['revision'], $post_types['nav_menu_item'] );
		foreach ( $post_types as $post_type => $post_type_object ) {
			$options[ $post_type ] = $post_type_object->labels->singular_name;
		}

		$vars = parent::js_vars();
		$vars['postTypeOptions'] = $options;
	}

	/**
	 * Register meta boxes for add/edit mb-taxonomy page
	 *
	 * @param array $meta_boxes Custom meta boxes.
	 *
	 * @return array
	 */
	public function register_meta_boxes( $meta_boxes ) {
		$meta_boxes[] = [
			'title'      => __( 'Taxonomy Editor', 'mb-custom-post-type' ),
			'id'         => 'ctg',
			'post_types' => [ 'mb-taxonomy' ],
			'style'      => 'seamless',
			'fields'     => [
				[
					'type' => 'custom_html',
					'std'  => '<div id="root" class="mb-cpt"></div>',
				],
				[
					'id'   => 'title',
					'type' => 'hidden',
				],
				[
					'id'   => 'name',
					'type' => 'hidden',
				],
				[
					'id'   => 'content',
					'type' => 'hidden',
				],
			],
		];

		return $meta_boxes;
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