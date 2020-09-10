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
	 * List of Javascript variables.
	 *
	 * @return array
	 */
	public function js_vars() {
		$screen = get_current_screen();

		if ( ! is_admin() || $screen->id !== 'mb-post-type' ) {
			return null;
		}

		global $post;

		return array_merge( parent::js_vars(), (array) json_decode( $post->post_content ) );
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
			'title'      => __( 'CPT Editor', 'auto-listings' ),
			'id'         => 'ptg',
			'post_types' => [ 'mb-post-type' ],
			'style'      => 'seamless',
			'fields'     => [
				[
					'type' => 'custom_html',
					'std'  => '<div id="root" class="ptg"></div>',
				],
				[
					'id'   => 'title',
					'type' => 'text',
				],
				[
					'id'   => 'content',
					'type' => 'text',
				],
			],
		];

		return $meta_boxes;
	}
}