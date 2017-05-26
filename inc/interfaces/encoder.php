<?php
/**
 * Encoder interface
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 */

if ( ! interface_exists( 'MB_CPT_Encoder_Interface' ) ) {

	interface MB_CPT_Encoder_Interface {

		public function encode( $data );
	}
}
