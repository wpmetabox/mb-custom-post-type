<?php
/**
 * Plugin about page
 *
 * @package Meta Box
 * @subpackage MB Custom Post Type
 */

/**
 * Class MB_CPT_About_Page
 */
class MB_CPT_About_Page {

	/**
	 * Plugin data.
	 *
	 * @var array
	 */
	protected $plugin;

	/**
	 * Init hooks.
	 */
	public function init() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			include ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$this->plugin = get_plugin_data( MB_CPT_FILE );

		add_action( 'admin_menu', array( $this, 'register_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Register admin page.
	 */
	public function register_page() {
		add_submenu_page(
			'edit.php?post_type=mb-post-type',
			__( 'MB Custom Post Type About', 'mb-custom-post-type' ),
			__( 'About', 'mb-custom-post-type' ),
			'manage_options',
			'mb-cpt-about',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render admin page.
	 */
	public function render_page() {
		?>
		<div class="wrap about-wrap">
			<?php include dirname( __FILE__ ) . '/sections/welcome.php'; ?>
			<?php include dirname( __FILE__ ) . '/sections/tabs.php'; ?>
			<?php include dirname( __FILE__ ) . '/sections/getting-started.php'; ?>
		</div>
		<?php
	}

	/**
	 * Enqueue CSS and JS.
	 */
	public function enqueue() {
		global $plugin_page;
		if ( 'mb-cpt-about' !== $plugin_page ) {
			return;
		}
		wp_enqueue_style( 'mb-cpt-about', MB_CPT_URL . 'inc/about/css/style.css' );
		wp_enqueue_script( 'mb-cpt-about', MB_CPT_URL . 'inc/about/js/script.js', array( 'jquery' ), '1.4', true );
	}
}
