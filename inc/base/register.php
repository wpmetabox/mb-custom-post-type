<?php
abstract class MB_CPT_Base_Register {
	public function __construct() {
		// Register post types.
		add_action( 'init', array( $this, 'register_post_types' ), 5 );

		// Change the output of post/bulk post updated messages.
		add_filter( 'post_updated_messages', array( $this, 'updated_message' ), 10, 1 );
	}

	// Migration helper methods.

	protected function unarray( &$value, $key, $ignore = [] ) {
		$value = 1 === count( $value ) && ! in_array( $key, $ignore, true ) ? $value[0] : $value;
	}

	protected function normalize_checkbox( &$value ) {
		if ( is_numeric( $value ) && in_array( $value, [0, 1] ) ) {
			$value = 1 == (int) $value;
		}
	}

	protected function change_key( &$array, $from, $to ) {
		if ( isset( $array[ $from ] ) ) {
			$array[ $to ] = $array[ $from ];
		}
		unset( $array[ $from ] );
	}
}
