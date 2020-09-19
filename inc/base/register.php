<?php
abstract class MB_CPT_Base_Register {
	public function __construct() {
		// Register post types.
		add_action( 'init', array( $this, 'register_post_types' ), 5 );

		// Change the output of post/bulk post updated messages.
		add_filter( 'post_updated_messages', array( $this, 'updated_message' ), 10, 1 );
	}
}
