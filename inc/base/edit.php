<?php
class MB_CPT_Base_Edit {
	public $post_type;
	public $saved = false;

	public function __construct( $post_type ) {
		$this->post_type = $post_type;

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'rwmb_meta_boxes', array( $this, 'register_meta_boxes' ) );

		// Prevent saving data in post meta.
		add_filter( 'rwmb_post_title_value', '__return_empty_string' );
		add_filter( 'rwmb_content_value', '__return_empty_string' );
	}

	public function enqueue_scripts() {
		if ( ! $this->is_edit_screen() ) {
			return;
		}

		wp_enqueue_style( $this->post_type, MB_CPT_URL . 'css/style.css', ['wp-components'], '1.8.0' );
		wp_enqueue_style( 'highlightjs', MB_CPT_URL . 'css/atom-one-dark.min.css', [], '9.15.8' );

		$object = str_replace( 'mb-', '', $this->post_type );
		$objectName = str_replace( ' ', '', ucwords( str_replace( '-', ' ', $this->post_type ) ) );
		wp_enqueue_script( $this->post_type, MB_CPT_URL . "js/$object.js", ['wp-element', 'wp-components'], '1.0.0', true );
		wp_localize_script( $this->post_type, $objectName, $this->js_vars() );
	}

	public function js_vars() {
		$vars = [];
		$vars['settings'] = get_post()->post_content;

		$object = str_replace( 'mb-', '', $this->post_type );
		$vars['result'] = MB_CPT_URL . "js/$object-result.js";

		if ( 'mb-taxonomy' === get_current_screen()->id ) {
			$options    = [];
			$post_types = get_post_types( '', 'objects' );
			unset( $post_types['mb-taxonomy'], $post_types['revision'], $post_types['nav_menu_item'] );
			foreach ( $post_types as $post_type => $post_type_object ) {
				$options[ $post_type ] = $post_type_object->labels->singular_name;
			}

			$vars['postTypeOptions'] = $options;
		}

		return $vars;
	}

	public function register_meta_boxes( $meta_boxes ) {
		$meta_boxes[] = [
			'title'      => ' ',
			'id'         => 'mb-cpt',
			'post_types' => [ 'mb-post-type', 'mb-taxonomy' ],
			'style'      => 'seamless',
			'context'    => 'after_title',
			'fields'     => [
				[
					'type' => 'custom_html',
					'std'  => '<div id="root" class="mb-cpt"></div>',
				],
				[
					'type' => 'custom_html',
					'std'  => '<div id="code-result" class="mb-cpt"></div>',
				],
				[
					'id'   => 'post_title',
					'type' => 'hidden',
				],
				[
					'id'   => 'content',
					'type' => 'hidden',
				],
			],
		];

		return $meta_boxes;
	}

	/**
	 * Check if current link is mb-post-type post type or not.
	 *
	 * @return boolean
	 */
	public function is_edit_screen() {
		$screen = get_current_screen();

		return 'post' === $screen->base && $this->post_type === $screen->post_type;
	}

	/**
	 * Check if current user is a premium user.
	 *
	 * @return bool
	 */
	public function is_premium_user() {
		$update_option = new RWMB_Update_Option();
		$update_checker = new RWMB_Update_Checker( $update_option );
		return $update_checker->has_extensions();
	}

	/**
	 * Display upgrade message.
	 *
	 * @return string
	 */
	public function upgrade_message() {
		$output  = '<ul>';
		$output .= '<li>' . __( 'Create custom fields with drag-n-drop interface - no coding knowledge required!', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li>' . __( 'Add custom fields to taxonomies or user profile.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li>' . __( 'Create custom settings pages.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li>' . __( 'Create frontend submission forms.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li>' . __( 'And much more!', 'mb-custom-post-type' ) . '</li>';
		$output .= '</ul>';
		$output .= '<a href="https://metabox.io/pricing/?utm_source=plugin_cpt&utm_medium=btn_upgrade&utm_campaign=cpt_upgrade" class="button button-primary">' . esc_html__( 'Get Meta Box Premium now', 'mb-custom-post-type' ) . '</a>';

		return $output;
	}
}
