<?php
/**
 * Base class to add new or edit object.
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 * @author     Tran Ngoc Tuan Anh <rilwis@gmail.com>
 */

/**
 * The base class which controls all operations for creating / modifying custom post type.
 */
abstract class MB_CPT_Base_Edit {

	/**
	 * Post type name.
	 *
	 * @var string
	 */
	public $post_type;

	/**
	 * Used to prevent duplicated calls like revisions, manual hook to wp_insert_post, etc.
	 *
	 * @var bool
	 */
	public $saved = false;

	/**
	 * Initializing.
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;

		// Enqueue scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		// Add meta box.
		add_filter( 'rwmb_meta_boxes', array( $this, 'register_meta_boxes' ) );
		// Modify post information after save post.
		add_action( "save_post_$post_type", array( $this, 'save_post' ) );
		// Prevent saving data in post_meta
		add_filter( 'rwmb_title_value', '__return_empty_string' );
		add_filter( 'rwmb_content_value', '__return_empty_string' );
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {
		if ( ! $this->is_edit_screen() ) {
			return;
		}

		wp_enqueue_style( 'mb-cpt', MB_CPT_URL . 'css/style.css', [], '1.8.0' );
		wp_enqueue_style( 'highlightjs', MB_CPT_URL . 'css/atom-one-dark.min.css', [], '9.15.8' );

		wp_enqueue_script( 'mb-cpt', MB_CPT_URL . 'js/post-type.js', [], '1.0.0', true );
		wp_localize_script( 'mb-cpt', 'MbCpt', $this->js_vars() );
	}

	/**
	 * List of Javascript variables.
	 *
	 * @return array
	 */
	public function js_vars() {
		return [];
	}

	/**
	 * Register meta boxes for add/edit mb-post-type page
	 *
	 * @param array $meta_boxes Meat boxes.
	 *
	 * @return array
	 */
	public function register_meta_boxes( $meta_boxes ) {
		return $meta_boxes;
	}

	/**
	 * Modify post information and post meta after save post
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_post( $post_id ) {
		$title = filter_input( INPUT_POST, 'title', FILTER_SANITIZE_STRING );
		$content = filter_input( INPUT_POST, 'content', FILTER_SANITIZE_STRING );

		// If label_singular_name is empty or if this function is called to prevent duplicated calls like revisions, manual hook to wp_insert_post, etc.
		if ( ! $title || true === $this->saved ) {
			return;
		}

		$this->saved = true;

		// Update post title.
		$post = [
			'ID'           => $post_id,
			'post_title'   => $title,
			'post_content' => $content,
		];

		wp_update_post( $post );

		// Flush rewrite rules after create new or edit taxonomies.
		flush_rewrite_rules();
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
}
