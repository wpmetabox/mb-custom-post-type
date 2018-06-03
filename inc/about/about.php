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
	 * Init hooks.
	 */
	public function init() {
		add_action( 'rwmb_about_tabs', array( $this, 'add_tabs' ) );
		add_action( 'rwmb_about_tabs_content', array( $this, 'add_tabs_content' ) );
	}

	/**
	 * Add tabs to the About page.
	 */
	public function add_tabs() {
		?>
		<a href="#types-taxonomies" class="nav-tab"><?php esc_html_e( 'Post Types & Taxonomies', 'mb-custom-post-type' ); ?></a>
		<?php
	}

	/**
	 * Add tabs content to the About page.
	 */
	public function add_tabs_content() {
		include dirname( __FILE__ ) . '/types-taxonomies.php';
	}
}
