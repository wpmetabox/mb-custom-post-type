<?php
/**
 * Encoder interface
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 */

if ( ! interface_exists( 'MB_CPT_Encoder_Interface' ) ) {

	interface MB_CPT_Encoder_Interface {

		/**
		 * Encode data.
		 *
		 * @param  mixed $data Data need to be encoded.
		 * @return string
		 */
		public function encode( $data );
	}
}
