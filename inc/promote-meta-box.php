<?php
/**
 * Promote meta box
 *
 * @package Meta Box
 * @subpackage MB Custom Post Type
 */

/**
 * Class MB_CPT_Promote_Meta_Box
 */
class MB_CPT_Promote_Meta_Box {

	/**
	 * Init hook.
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	}

	/**
	 * Add meta box.
	 *
	 * @param string $post_type Post type.
	 */
	public function add_meta_box( $post_type ) {
		if ( ! in_array( $post_type, array( 'mb-post-type', 'mb-taxonomy' ) ) ) {
			return;
		}
		add_meta_box(
			'mb-cpt-promote',
			__( 'Meta box', 'mb-custom-post-type' ),
			array( $this, 'render' ),
			$post_type,
			'side',
			'low'
		);
	}

	/**
	 * Render meta box.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render( $post ) {
		?>
		<p>Lorem lipsum.</p>
		<?php
	}
}