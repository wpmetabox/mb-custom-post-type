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
	 * Encoder object.
	 *
	 * @var MB_CPT_Encoder_Interface
	 */
	protected $encoder;

	/**
	 * Class MB_CPT_Post_Type_Edit constructor.
	 *
	 * @param string                    $post_type Post type name.
	 * @param MB_CPT_Post_Type_Register $register  Post type register object.
	 * @param MB_CPT_Encoder_Interface  $encoder   Encoder object.
	 */
	public function __construct( $post_type, MB_CPT_Post_Type_Register $register, MB_CPT_Encoder_Interface $encoder ) {
		parent::__construct( $post_type );

		$this->register = $register;
		$this->encoder  = $encoder;

		// Change the menu positions option after all menus are registered.
		add_action( 'admin_menu', array( $this, 'change_select_options' ), 9999 );
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

		var_dump( (array) json_decode( $post->post_content ) );

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
			],
		];

		return $meta_boxes;
	}

	/**
	 * Modify html output of field
	 *
	 * @param string $html  HTML out put of the field.
	 * @param array  $field Field parameters.
	 * @param string $meta  Meta value.
	 *
	 * @return string
	 */
	public function modify_field_html( $html, $field, $meta ) {
		if ( 'mb-post-type' !== get_current_screen()->id ) {
			return $html;
		}

		// Fix for escaping single quote for AngularJS.
		$meta = str_replace( '&#039;', "\\'", $meta );

		// Labels.
		if ( 0 === strpos( $field['id'], 'label_' ) ) {
			$model = substr( $field['id'], 6 );
			$html  = str_replace(
				'>',
				sprintf(
					' ng-model="labels.%s" ng-init="labels.%s=\'%s\'"%s>',
					esc_attr( $model ),
					esc_attr( $model ),
					$meta,
					in_array( $model, array( 'name', 'singular_name' ), true ) ? ' ng-change="updateLabels()"' : ''
				),
				$html
			);
			$html  = preg_replace( '/value="(.*?)"/', 'value="{{labels.' . esc_attr( $model ) . '}}"', $html );
			return $html;
		}

		if ( 'args_post_type' === $field['id'] ) {
			$html = str_replace(
				'>',
				sprintf(
					' ng-model="post_type" ng-init="post_type=\'%s\'">',
					$meta
				),
				$html
			);
			$html = preg_replace( '/value="(.*?)"/', 'value="{{post_type}}"', $html );
			return $html;
		}

		if ( 'args_menu_icon' === $field['id'] ) {
			$html  = '';
			$icons = mb_cpt_get_dashicons();
			foreach ( $icons as $icon ) {
				$html .= sprintf(
					'<label class="icon-single%s">
						<i class="wp-menu-image dashicons-before %s"></i>
						<input type="radio" name="args_menu_icon" value="%s" class="hidden"%s>
					</label>',
					$icon === $meta ? ' active' : '',
					esc_attr( $icon ),
					esc_attr( $icon ),
					checked( $icon, $meta, false )
				);
			}
			return $html;
		}

		return $html;
	}

	/**
	 * Print generated code textarea.
	 *
	 * @return string
	 */
	public function generated_code_html() {
		$post_id               = get_the_ID();
		list( $labels, $args ) = $this->register->get_post_type_data( $post_id );
		if ( ! $labels ) {
			return '';
		}

		$post_type_data = $this->register->set_up_post_type( $labels, $args );

		$encode_data    = array(
			'function_name'  => get_post_meta( $post_id, 'function_name', true ),
			'text_domain'    => get_post_meta( $post_id, 'text_domain', true ),
			'post_type'      => $args['post_type'],
			'post_type_data' => $post_type_data,
		);
		$encoded_string = $this->encoder->encode( $encode_data );

		$output  = '
			<div id="generated-code">
				<a href="javascript:void(0);" class="mb-button--copy">
					<svg class="mb-icon--copy" aria-hidden="true" role="img"><use href="#mb-icon-copy" xlink:href="#icon-copy"></use></svg>
					' . esc_html__( 'Copy', 'mb-custom-post-type' ) . '
				</a>
				<pre><code class="php">' . esc_textarea( $encoded_string ) . '</code></pre>
			</div>';
		$output .= '
			<svg style="display: none;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				<symbol id="mb-icon-copy" viewBox="0 0 1024 896">
					<path d="M128 768h256v64H128v-64z m320-384H128v64h320v-64z m128 192V448L384 640l192 192V704h320V576H576z m-288-64H128v64h160v-64zM128 704h160v-64H128v64z m576 64h64v128c-1 18-7 33-19 45s-27 18-45 19H64c-35 0-64-29-64-64V192c0-35 29-64 64-64h192C256 57 313 0 384 0s128 57 128 128h192c35 0 64 29 64 64v320h-64V320H64v576h640V768zM128 256h512c0-35-29-64-64-64h-64c-35 0-64-29-64-64s-29-64-64-64-64 29-64 64-29 64-64 64h-64c-35 0-64 29-64 64z" />
				</symbol>
			</svg>';
		return $output;
	}

	/**
	 * Change select options.
	 */
	public function change_select_options() {
		$meta_box = rwmb_get_registry( 'meta_box' )->get( 'mb-cpt-advanced-settings' );
		$meta_box->meta_box['fields']['menu_position']['options'] = $this->get_menu_positions();
		$meta_box->meta_box['fields']['show_in_menu']['options']  = $this->get_menu_options();
	}

	/**
	 * Get WordPress menu positions
	 *
	 * @return array
	 */
	protected function get_menu_positions() {
		global $menu;
		$positions = array();
		foreach ( $menu as $position => $params ) {
			if ( ! empty( $params[0] ) ) {
				$positions[ $position ] = $this->strip_span( $params[0] );
			}
		}
		return $positions;
	}

	/**
	 * Get WordPress menu options
	 *
	 * @return array
	 */
	protected function get_menu_options() {
		global $menu;
		$options = array(
			'1' => esc_html__( 'Show as top-level menu', 'mb-custom-post-type' ),
		);
		foreach ( $menu as $position => $params ) {
			if ( ! empty( $params[0] ) && ! empty( $params[2] ) ) {
				// Translators: %s is the main menu label.
				$options[ $params[2] ] = sprintf( __( 'Show as sub-menu of %s', 'mb-custom-post-type' ), $this->strip_span( $params[0] ) );
			}
		}
		$options['0'] = esc_html__( 'Do not show in the admin menu', 'mb-custom-post-type' );
		return $options;
	}

	/**
	 * Remove <span> tag (counter) with their content.
	 *
	 * @param string $html HTML content.
	 *
	 * @return string
	 */
	protected function strip_span( $html ) {
		return preg_replace( '@<span .*>.*</span>@si', '', $html );
	}
}
