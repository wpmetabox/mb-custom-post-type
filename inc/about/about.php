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
			<h1>
				<?php
				// Translators: %1$s - Plugin name, %2$s - Plugin version.
				printf(
					esc_html__( 'Welcome to %1$s - Version %2$s', 'mb-custom-post-type' ),
					esc_html( $this->plugin['Name'] ),
					esc_html( $this->plugin['Version'] )
				);
				?>
			</h1>

			<div class="about-text"><?php echo wp_kses_post( $this->plugin['Description'] ); ?></div>
			<a target="_blank" href="https://metabox.io/" class="wp-badge">Meta Box</a>

			<h2 class="nav-tab-wrapper">
				<a href="#getting-started" class="nav-tab nav-tab-active">Getting Started</a>
			</h2>

			<div id="getting-started" class="gt-tab-pane gt-is-active">
				<div class="feature-section two-col">
					<div class="col">
						<h3>Read Full Documentation</h3>
						<p class="about">Need any help to setup and configure the plugin? Please check our full documentation for detailed information on how to use it.</p>
						<p>
							<a href="#" target="_blank" class="button button-primary">Read Documentation</a>
						</p>
					</div>

					<div class="col">
						<h3>More extensions from Meta Box</h3>
						<a href="#" target="_blank" class="button button-secondary">Find more</a>
					</div>
				</div>
			</div>

			<div class="three-col">
				<div class="col">
					<div class="project">
						<a href="https://gretathemes.com" title="GretaThemes"><img class="project__image" src="https://elightup.com/images/gretathemes.png" alt="gretathemes" width="96" height="96"></a>
						<div class="project__body">
							<h3 class="project__title"><a href="https://gretathemes.com" title="GretaThemes">GretaThemes</a></h3>
							<p class="project__description">Modern, clean, responsive <strong>premium WordPress themes</strong> for all your needs. Fast loading, easy to use and optimized for SEO.</p>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="project">
						<a href="https://metabox.io" title="Meta Box"><img class="project__image" src="https://elightup.com/images/meta-box.png" alt="meta box" width="96" height="96"></a>
						<div class="project__body">
							<h3 class="project__title"><a href="https://metabox.io" title="Meta Box">Meta Box</a></h3>
							<p class="project__description">The lightweight &amp; feature-rich WordPress plugin that helps developers to save time building <strong>custom meta boxes and custom fields</strong>.</p>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="project">
						<a href="https://prowcplugins.com" title="Professional WooCommerce Plugins"><img class="project__image" src="https://elightup.com/images/prowcplugins.png" alt="professional woocommerce plugins" width="96" height="96"></a>
						<div class="project__body">
							<h3 class="project__title"><a href="https://prowcplugins.com" title="Professional WooCommerce Plugins">ProWCPlugins</a></h3>
							<p class="project__description">Professional <strong>WordPress plugins for WooCommerce</strong> that help you empower your e-commerce sites and grow your business.</p>
						</div>
					</div>
				</div>
			</div>
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
