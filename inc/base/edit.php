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

		$object      = str_replace( 'mb-', '', $this->post_type );
		$object_name = str_replace( ' ', '', ucwords( str_replace( '-', ' ', $this->post_type ) ) );
		wp_enqueue_code_editor( ['type' => 'php'] );
		wp_enqueue_script( $this->post_type, MB_CPT_URL . "js/$object.js", ['wp-element', 'wp-components', 'clipboard', 'wp-i18n'], '1.0.0', true );
		wp_localize_script( $this->post_type, $object_name, $this->js_vars() );
		wp_set_script_translations( $this->post_type, 'mb-custom-post-type' );
	}

	public function js_vars() {
		$vars = [];
		$vars['settings'] = json_decode( get_post()->post_content, ARRAY_A );

		if ( 'mb-taxonomy' !== get_current_screen()->id ) {
			return $vars;
		}

		$options    = [];
		$post_types = mb_cpt_get_post_types();
		foreach ( $post_types as $post_type => $post_type_object ) {
			$options[ $post_type ] = $post_type_object->labels->singular_name;
		}

		$vars['postTypeOptions'] = $options;

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
					'id'   => 'post_title',
					'type' => 'hidden',
				],
				[
					'id'   => 'content',
					'type' => 'hidden',
				],
			],
		];

		if ( ! $this->is_premium_user() ) {
			$meta_boxes[] = [
				'id'         => 'mb-cpt-upgrade',
				'title'      => __( 'Upgrade', 'mb-custom-post-type' ),
				'post_types' => [ 'mb-post-type', 'mb-taxonomy' ],
				'context'    => 'side',
				'priority'   => 'low',
				'fields'     => [
					[
						'type'     => 'custom_html',
						'callback' => [ $this, 'upgrade_message' ],
					],
				],
			];
		}

		return $meta_boxes;
	}

	public function is_edit_screen() {
		$screen = get_current_screen();

		return 'post' === $screen->base && $this->post_type === $screen->post_type;
	}

	public function is_premium_user() {
		$update_option = new RWMB_Update_Option();
		$update_checker = new RWMB_Update_Checker( $update_option );
		return $update_checker->has_extensions();
	}

	public function upgrade_message() {
		$output = '<p>' . esc_html__( 'Upgrade now to have more features & speedy technical support:', 'mb-custom-post-type' ) . '</p>';
		$output .= '<ul>';
		$output .= '<li><span class="dashicons dashicons-yes"></span>' . esc_html__( 'Create custom fields with UI.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li><span class="dashicons dashicons-yes"></span>' . esc_html__( 'Add custom fields to terms and users.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li><span class="dashicons dashicons-yes"></span>' . esc_html__( 'Create custom settings pages.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li><span class="dashicons dashicons-yes"></span>' . esc_html__( 'Create frontend submission forms.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li><span class="dashicons dashicons-yes"></span>' . esc_html__( 'Create frontend templates.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li><span class="dashicons dashicons-yes"></span>' . esc_html__( 'And much more!', 'mb-custom-post-type' ) . '</li>';
		$output .= '</ul>';
		$output .= '<a href="https://metabox.io/pricing/?utm_source=plugin_cpt&utm_medium=btn_upgrade&utm_campaign=cpt_upgrade" class="button">' . esc_html__( 'Upgrade now', 'mb-custom-post-type' ) . '</a>';

		return $output;
	}
}
