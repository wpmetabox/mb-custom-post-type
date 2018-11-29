<?php
/**
 * Post type encoder class
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 */

if ( ! class_exists( 'MB_CPT_Post_Type_Encoder' ) ) {

	/**
	 * Class MB_CPT_Post_Type_Encoder
	 */
	class MB_CPT_Post_Type_Encoder implements MB_CPT_Encoder_Interface {

		/**
		 * Function name.
		 *
		 * @var string
		 */
		protected $function_name = 'your_prefix_register_post_type';

		/**
		 * Text domain.
		 *
		 * @var string
		 */
		protected $text_domain = 'text-domain';

		/**
		 * Post type name.
		 *
		 * @var string
		 */
		protected $post_type;

		/**
		 * Encode.
		 *
		 * @param  array $data Data to encode.
		 * @return string      Encoded string.
		 */
		public function encode( $data ) {
			if ( empty( $data['post_type_data'] ) || empty( $data['post_type'] ) ) {
				return false;
			}

			if ( ! empty( $data['function_name'] ) ) {
				$this->function_name = $data['function_name'];
			}

			if ( ! empty( $data['text_domain'] ) ) {
				$this->text_domain = $data['text_domain'];
			}

			$this->post_type = $data['post_type'];

			$post_type_data = $data['post_type_data'];

			$post_type_data = $this->make_translatable( $post_type_data );

			$string_data = var_export( $post_type_data, true );
			$string_data = $this->replace_get_text_function( $string_data );
			$string_data = $this->fix_code_standard( $string_data );
			$string_data = $this->wrap_function_call( $string_data );

			return $string_data;
		}

		/**
		 * Add prefix and suffix to translatable string.
		 *
		 * @param  array $data Encode data.
		 * @return array
		 */
		protected function make_translatable( $data ) {
			$data['label'] = sprintf( '###%s###', $data['label'] );

			foreach ( $data['labels'] as $key => $value ) {
				$data['labels'][ $key ] = sprintf( '###%s###', $value );
			}

			return $data;
		}

		/**
		 * Replace translatable string with gettext function.
		 *
		 * @param  string $string_data Encoded string.
		 * @return string
		 */
		protected function replace_get_text_function( $string_data ) {
			$find    = "/'###(.*)###'/";
			$replace = "esc_html__( '$1', '" . $this->text_domain . "' )";

			return preg_replace( $find, $replace, $string_data );
		}

		/**
		 * Make encoded string compatible with WordPress coding standard.
		 *
		 * @param  string $string_data Encoded string.
		 * @return string
		 */
		protected function fix_code_standard( $string_data ) {
			$search = array(
				'/  /',
				"/\n\t/",
				"/\n\)/",
				"/=> \n\t\tarray \(/",
				"/\n\t\t\t\\d => /",
			);

			$replace = array(
				"\t",
				"\n\t\t",
				"\n\t)",
				'=> array(',
				"\n\t\t\t",
			);

			$string_data = preg_replace( $search, $replace, $string_data );

			return $string_data;
		}

		/**
		 * Wrap encoded string with function name and hook.
		 *
		 * @param  string $string_data Encoded string.
		 * @return string
		 */
		protected function wrap_function_call( $string_data ) {
			$string_data = sprintf(
				"function %1\$s() {\n\n\t\$args = %2\$s;\n\n\tregister_post_type( '%3\$s', \$args );\n}\nadd_action( 'init', '%1\$s' );",
				$this->function_name,
				$string_data,
				$this->post_type
			);

			return $string_data;
		}
	}
} // End if().
