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
class MB_CPT_Base_Edit {

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
		add_filter( 'rwmb_name_value', '__return_empty_string' );
		add_filter( 'rwmb_content_value', '__return_empty_string' );

		add_action( 'wp_ajax_nopriv_show_code', array( $this, 'generate_code' ) );
		add_action( 'wp_ajax_show_code', array( $this, 'generate_code' ) );
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {
		if ( ! $this->is_edit_screen() ) {
			return;
		}

		wp_enqueue_style( 'mb-cpt', MB_CPT_URL . 'css/style.css', ['wp-components'], '1.8.0' );
		wp_enqueue_style( 'highlightjs', MB_CPT_URL . 'css/atom-one-dark.min.css', [], '9.15.8' );

		if ( 'mb-post-type' === get_current_screen()->id ) {
			wp_enqueue_script( 'mb-cpt', MB_CPT_URL . 'js/post-type.js', ['wp-element', 'wp-components'], '1.0.0', true );
			wp_localize_script( 'mb-cpt', 'MbCpt', $this->js_vars() );
			wp_localize_script( 'mb-cpt', 'AjaxVars', [
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'ajax-nonce' )
			] );
		}

		if ( 'mb-taxonomy' === get_current_screen()->id ) {
			$options    = [];
			$post_types = get_post_types( '', 'objects' );
			unset( $post_types['mb-taxonomy'], $post_types['revision'], $post_types['nav_menu_item'] );
			foreach ( $post_types as $post_type => $post_type_object ) {
				$options[ $post_type ] = $post_type_object->labels->singular_name;
			}

			wp_enqueue_script( 'mb-taxonomy', MB_CPT_URL . 'js/taxonomy.js', ['wp-element', 'wp-components'], '1.0.0', true );
			wp_localize_script( 'mb-taxonomy', 'MbTax', $this->js_vars() );
			wp_localize_script( 'mb-taxonomy', 'MbPtOptions', $options );
			wp_localize_script( 'mb-taxonomy', 'AjaxVars', [
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'ajax-nonce' )
			] );
		}
	}

	public function generate_code() {
		if ( $_POST['post_type_data'] ) {
			echo MB_CPT_URL . 'js/post-type-result.js';
		}

		if ( $_POST['taxonomy_data'] ) {
			echo MB_CPT_URL . 'js/taxonomy-result.js';
		}

		wp_die();
	}

	/**
	 * List of Javascript variables.
	 *
	 * @return array
	 */
	public function js_vars() {
		$screen = get_current_screen();

		if ( ! is_admin() || ! in_array( $screen->id, ['mb-post-type', 'mb-taxonomy'] ) ) {
			return null;
		}

		global $post;
		return (array) $post->post_content;
	}

	/**
	 * Register meta boxes for add/edit mb-post-type page
	 *
	 * @param array $meta_boxes Meat boxes.
	 *
	 * @return array
	 */
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
					'id'   => 'title',
					'type' => 'hidden',
				],
				[
					'id'   => 'name',
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
	 * Modify post information and post meta after save post
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_post( $post_id ) {
		$title   = filter_input( INPUT_POST, 'title', FILTER_SANITIZE_STRING );
		$slug    = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_STRING );
		$content = filter_input( INPUT_POST, 'content' );

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
			'post_name'    => $slug,
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
