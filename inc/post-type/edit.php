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
	 * Class MB_CPT_Post_Type_Edit constructor.
	 *
	 * @param string                    $post_type Post type name.
	 * @param MB_CPT_Post_Type_Register $register  Post type register object.
	 */
	public function __construct( $post_type, MB_CPT_Post_Type_Register $register ) {
		parent::__construct( $post_type );

		$this->register = $register;
	}

	/**
	 * Register meta boxes for add/edit mb-post-type page.
	 *
	 * @param array $meta_boxes Meta boxes.
	 *
	 * @return array
	 */
	public function register_meta_boxes( $meta_boxes ) {
		$meta_boxes[] = [
			'title'      => ' ',
			'id'         => 'mb-cpt',
			'post_types' => [ 'mb-post-type' ],
			'style'      => 'seamless',
			'context'    => 'after_title',
			'fields'     => [
				[
					'type' => 'custom_html',
					'std'  => '<div id="root" class="mb-cpt"></div>',
				],
				[
					'type' => 'custom_html',
					'std'  => '<div id="code-result" class="mb-cpt"></div>',
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