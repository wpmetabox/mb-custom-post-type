<?php
namespace MBCPT;

use MetaBox\Support\Arr;

abstract class Register {
	public function __construct() {
		$this->register();
		add_filter( 'post_updated_messages', [ $this, 'updated_message' ] );
		add_filter( 'bulk_post_updated_messages', [ $this, 'bulk_updated_messages' ], 10, 2 );
	}

	abstract public function register();

	protected function unarray( &$value, $key, $ignore = [] ) {
		$value = 1 === count( $value ) && ! in_array( $key, $ignore, true ) ? $value[0] : $value;
	}

	protected function normalize_checkbox( &$value ) {
		if ( is_numeric( $value ) && in_array( $value, [ 0, 1 ] ) ) { // phpcs:ignore
			$value = 1 === (int) $value;
		}
	}

	protected function change_key( &$arr, $from, $to ) {
		if ( isset( $arr[ $from ] ) ) {
			$arr[ $to ] = $arr[ $from ];
		}
		unset( $arr[ $from ] );
	}


	protected function sanitize_labels( &$settings ): void {
		$labels = Arr::get( $settings, 'labels', [] );
		$labels = array_map( 'sanitize_text_field', $labels );
		$labels = array_map( function ( $text ) {
			return str_replace( [ '&lt;', '&gt;' ], '', $text );
		}, $labels );
		Arr::set( $settings, 'labels', $labels );
	}
}
