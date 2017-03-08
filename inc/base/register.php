<?php
/**
 * Base class to register custom post types and custom taxonomies.
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 * @author     Tran Ngoc Tuan Anh
 */

/**
 * Base class to register custom post types and custom taxonomies.
 */
abstract class MB_CPT_Base_Register {

	/**
	 * Initializing.
	 */
	public function __construct() {
		// Register post types.
		add_action( 'init', array( $this, 'register_post_types' ), 5 );

		// Change the output of post/bulk post updated messages.
		add_filter( 'post_updated_messages', array( $this, 'updated_message' ), 10, 1 );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_updated_messages' ), 10, 2 );
	}
}
