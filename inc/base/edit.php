<?php
abstract class MB_CPT_Base_Edit {
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

		return $vars;
	}

	abstract function register_meta_boxes( $meta_boxes );

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
}
