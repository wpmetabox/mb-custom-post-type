<?php
namespace MBCPT;

abstract class Register {
	public function __construct() {
		$this->register();
		add_filter( 'post_updated_messages', [ $this, 'updated_message' ] );
		add_filter( 'bulk_post_updated_messages', [ $this, 'bulk_updated_messages' ], 10, 2 );
	}

	abstract public function register();

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
